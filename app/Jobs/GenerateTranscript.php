<?php

namespace oval\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use oval\Transcript;
use Exception;

/**
 * This class handles API requests to convert audio to text.
 * @author Ken
 */
class GenerateTranscript implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    // Important, will throw an error without it
    protected $opt;
    
    /**
     * Create a new GenerateTranscript job instance.
     *
     * @param  array  $opt
     *   Parameters for generating transcript video file, must contains
     *   "videoId" => video id
     *   "userIds" => user id array
     *
     * @return void
     */
    public function __construct(array $opt = [])
    {
        $this->opt = $opt;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // echo json_encode($this->opt) . "\n";
        
        $IBM_WATSON_STT_URL = env('IBM_WATSON_STT_URL', 'https://stream.watsonplatform.net/speech-to-text/api/v1');
        $IBM_WATSON_STT_USER = env('IBM_WATSON_STT_USER', '');
        $IBM_WATSON_STT_PASS = env('IBM_WATSON_STT_PASS', '');
        $HELIX_TEMP_FLAC_FILE = env('HELIX_TEMP_FLAC_FILE', '/tmp/helix_file.flac');

        $video_id = $this->opt['videoId'];
        $user_ids = $this->opt['userIds'];

        $filesize = filesize($HELIX_TEMP_FLAC_FILE);
        if (($filesize / 1000000) > 95) {
            throw new Exception('Video too long, cannot process.');
        }

        $proxy_url = env('CURL_PROXY_URL', '');
        $proxy_user = env('CURL_PROXY_USER', '');
        $proxy_pass = env('CURL_PROXY_PASS', '');
        $ch = curl_init();
        $file = fopen($HELIX_TEMP_FLAC_FILE, 'rb');
        $data = fread($file, $filesize);
        fclose($file);
        
		curl_setopt($ch, CURLOPT_URL, $IBM_WATSON_STT_URL . '/recognize?timestamps=true');
        if (!empty($proxy_url)) {
            curl_setopt($ch, CURLOPT_PROXY, $proxy_url);
        }
        if (!empty($proxy_user)) {
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy_user . ':' . $proxy_pass);
        }
		curl_setopt($ch, CURLOPT_USERPWD, $IBM_WATSON_STT_USER . ":" . $IBM_WATSON_STT_PASS);
        curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		// curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
		    'Content-Type: audio/flac')                                                                       
        );
        $result = curl_exec($ch);

        if ($errno = curl_errno($ch))
        {
            $error_message = curl_strerror($errno);
            error_log('error ' . $errno . ': ' . $error_message);
            return ['error'=>$errno];
        }

        curl_close($ch);
        
        $watson_result = json_decode($result);
        $watson_transctipt = array();
        $text = '';
        
        foreach ($watson_result->results as $row) {
            $w_start = $row->alternatives[0]->timestamps[0][1];
            $w_end = $row->alternatives[0]->timestamps[count($row->alternatives[0]->timestamps) - 1][2];
            $w_transcript = trim($row->alternatives[0]->transcript);
            $watson_transctipt[] = json_encode(array(
                'start' => $w_start,
                'end' => $w_end,
                'transcript' => $w_transcript
            ));
            $text .= $w_transcript . ' ';
        }
        
        // Save transcript into database
        $transcript = Transcript::where('video_id', $video_id)->first();
        if (empty($transcript)) {
            $transcript = new Transcript;
            $transcript->video_id = $video_id;
        }
        $transcript->transcript = json_encode($watson_transctipt);
        $transcript->save();

        if (strlen(trim($text)) < 100) {
            // throw new Exception('Video too short, cannot process.');
            $text = 'No transcript available.';
        }
        dispatch(new AnalyzeTranscript(array(
            'transcript' => $text,
            'videoId' => $video_id,
            'userIds' => $user_ids
        )));
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        echo 'Caught exception: ',  $exception->getMessage(), "\n";
    }
}
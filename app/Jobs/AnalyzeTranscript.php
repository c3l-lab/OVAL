<?php

namespace oval\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use oval\Models\AnalysisRequest;
use oval\Models\Keyword;
use oval\Models\Transcript;
use oval\Models\User;
use oval\Models\Video;
use Exception;

/**
 * This class handles API requests for text analysis
 * @author Ken
 */
class AnalyzeTranscript implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Important, will throw an error without it
    protected $opt;

    /**
     * Create a new AnalyzeTranscript job instance.
     *
     * @param  array  $opt
     *   Parameters for analyse video transcription, must contains
     *   "transcript" => transcript text string
     *   "videoId" => video id
     *   "userIds" => user id array
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

        $IBM_WATSON_NLU_URL = env('IBM_WATSON_NLU_URL', 'https://gateway.watsonplatform.net/natural-language-understanding/api/v1');
        $IBM_WATSON_NLU_VERSION = env('IBM_WATSON_NLU_VERSION', '2017-02-27');
        $IBM_WATSON_NLU_USER = env('IBM_WATSON_NLU_USER', '');
        $IBM_WATSON_NLU_PASS = env('IBM_WATSON_NLU_PASS', '');

        $video_id = $this->opt['videoId'];

        if ($this->opt['transcript'] === 'No transcript available.') {
            $transcript = Transcript::where('video_id', $video_id)->first();
            if (!empty($transcript)) {
                $transcript->analysis = '{}';
                $transcript->save();
            }
        } else {
            $proxy_url = env('CURL_PROXY_URL', '');
            $proxy_user = env('CURL_PROXY_USER', '');
            $proxy_pass = env('CURL_PROXY_PASS', '');
            $ch = curl_init();
            $data_string = '{"html":"' . htmlspecialchars($this->opt['transcript'], ENT_QUOTES) . '","features":{"concepts":{},"keywords":{},"entities":{},"categories":{}}}';
            // echo $data_string;
            //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);////////
            curl_setopt($ch, CURLOPT_URL, $IBM_WATSON_NLU_URL . '/analyze?version=' . $IBM_WATSON_NLU_VERSION);
            if (!empty($proxy_url)) {
                curl_setopt($ch, CURLOPT_PROXY, $proxy_url);
            }
            if (!empty($proxy_user)) {
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy_user . ':' . $proxy_pass);
            }
            curl_setopt($ch, CURLOPT_USERPWD, $IBM_WATSON_NLU_USER . ':' . $IBM_WATSON_NLU_PASS);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            // curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json')
            );
            $result = curl_exec($ch);
            if ($errno = curl_errno($ch)) {
                $error_message = curl_strerror($errno);
                echo "cURL error ({$errno}):\n {$error_message}";
                throw new Exception("cURL error ({$errno}):\n {$error_message}");
			}
            curl_close($ch);
            $analysis_json = json_decode($result);

            // Save the result into database
            $transcript = Transcript::where('video_id', $video_id)->first();
            if (!empty($transcript)) {
                $transcript->analysis = json_encode($analysis_json);
                $transcript->save();

                $video_keywords = array();
                $video_transcript = json_decode($transcript->transcript);

                $concepts = property_exists($analysis_json, 'concepts') ? $analysis_json->concepts : [];
                $entities = property_exists($analysis_json, 'entities') ? $analysis_json->entities : [];
                $keywords = property_exists($analysis_json, 'keywords') ? $analysis_json->keywords : [];
                $categories = property_exists($analysis_json, 'categories') ? $analysis_json->categories : [];
                // echo json_encode($concepts) . "\n";
                // echo json_encode($entities) . "\n";
                // echo json_encode($keywords) . "\n";
                // echo json_encode($categories) . "\n";

                foreach ($concepts as $c) {
                    $k = (object) array();
                    $k->keyword = $c->text;
                    $k->relevance = $c->relevance;
                    $k->type = 'concepts';
                    $video_keywords[] = $k;
                }
                foreach ($entities as $e) {
                    $k = (object) array();
                    $k->keyword = $e->text;
                    $k->relevance = $e->relevance;
                    $k->type = 'entities';
                    $video_keywords[] = $k;
                }

                foreach ($keywords as $kw) {
                    $k = (object) array();
                    $k->keyword = $kw->text;
                    $k->relevance = $kw->relevance;
                    $k->type = 'keywords';
                    $video_keywords[] = $k;
                }

                foreach ($categories as $c) {
                    $k = (object) array();
                    $k->keyword = $c->label;
                    $k->relevance = $c->score;
                    $k->type = 'categories';
                    $video_keywords[] = $k;
                }

                for ($i = 0; $i < count($video_keywords); $i++) {
                    for ($j = 0; $j < count($video_transcript); $j++) {
                        $vk = $video_keywords[$i];
                        $vt = json_decode(trim(preg_replace('/\s+/', ' ', $video_transcript[$j]))); // preg_replace to remove \n character causing json parsing error

                        if (strpos($vt->transcript, $vk->keyword) !== false) {
                            // echo $video_id . "\n";
                            // echo $vk->keyword . "\n";
                            // echo $vt->start . "\n";
                            // echo $vt->end . "\n";
                            // echo $vk->type . "\n";
                            // echo $vk->relevance . "\n";
                            $key = Keyword::where(array(
                                'videoId' => $video_id,
                                'keyword' => $vk->keyword,
                                'startTime' => round($vt->start, 2),
                                'endTime' => round($vt->end, 2),
                                'type' => $vk->type
                            ))->first();

                            if (empty($key)) {
                                $new_key = new Keyword;
                                $new_key->videoId = $video_id;
                                $new_key->keyword = $vk->keyword;
                                $new_key->startTime = round($vt->start, 2);
                                $new_key->endTime = round($vt->end, 2);
                                $new_key->type = $vk->type;
                                $new_key->relevance = $vk->relevance;
                                $new_key->save();
                            }
                        }
                    }
                }
            }
        }

        // Change status to 'processed'
        AnalysisRequest::where(array('video_id' => $video_id))->update(['status' => 'processed']);

        // Send out notification email
        if (!empty($this->opt['userIds'])) {
            $send_user_ids = array_unique($this->opt['userIds'], SORT_REGULAR);
            $video = Video::select('title')->where('id', $video_id)->first();
            $video_title = empty($video) ? 'Untitled Video' : $video->title;
            foreach ($send_user_ids as $user_id) {
                $user = User::select('email')
                    ->where('id', $user_id)->first();
                if (!empty($user)) {
                    $user_email = $user->email;
                    dispatch(new SendEmail([
                        'email'   => 'emails.content-analysis-finished',
                        'subject' => 'Content Analysis finished',
                        'to'      => $user_email,
                        'params'  => [
                            'title' => $video_title
                        ]
                    ]));
                }
            }
        }
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

<?php

namespace oval\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Exception;

/**
 * This class handles processing of Helix video's caption text 
 * by downloading the video, converting to audio using ffmpeg,
 * then calling external API to convert audio to text.
 * @author Ken
 */
class DownloadHelixFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Important, will throw an error without it
    protected $opt;

    /**
     * Create a new job instance.
     *
     * @param  array  $opt
     *   Parameters for download Helix video file, must contains
     *   "url" => Helix video url
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

        $HELIX_TEMP_FLAC_FILE = env('HELIX_TEMP_FLAC_FILE', '/tmp/helix_file.flac');

        $url = $this->opt['url'];
        $video_id = $this->opt['videoId'];
        $user_ids = $this->opt['userIds'];

        $convert = exec('ffmpeg -i "' . $url . '" -acodec flac -sample_fmt s16 -ar 16000 -ac 1 "' . $HELIX_TEMP_FLAC_FILE . '" -y');

        dispatch(new GenerateTranscript(array(
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

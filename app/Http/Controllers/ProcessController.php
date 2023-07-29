<?php

namespace oval\Http\Controllers;

use Illuminate\Http\Request;
use oval;
use DB;
use oval\Classes\YoutubeDataHelper;
use Illuminate\Support\Facades\Auth;
use oval\Models\AnalysisRequest;
use oval\Jobs\AnalyzeTranscript;
use Illuminate\Support\Facades\Storage;

use IMSGlobal\LTI\ToolProvider;
use IMSGlobal\LTI\ToolProvider\DataConnector;

/**
 * This class handles POST requests from php forms.
 */
class ProcessController extends Controller
{
    /**
     * Private method to process text analysis of YouTube video.
     *
     * Get the caption for the video, then dispatch analysis job to queue
     * and returns message letting user know it is being processed.
     * If no transcript is available, returns eerror message.
     * @param Video $video Video object with media_type='youtube'
     * @param array $user_ids Array of int containing ids of users who requested for this analysis
     * @return string message to display
     */
    private function process_youtube_analysis($video, $user_ids)
    {
        $caption_text = $video->downloadCaption();
        $text = "";
        $video = $video->fresh();

        if(!empty($caption_text)) {
            $text = $caption_text;
        } elseif(!empty($video->transcript)) {
            $transcript_json = json_decode($video->transcript->transcript);
            foreach ($transcript_json as $t) {
                $obj = json_decode($t);
                $text .= $obj->transcript." ";
            }
        } else {
            // Change status to 'processed'
            AnalysisRequest::where(array('video_id' => $video->id))->update(['status' => 'processed']);

            return $this->no_transcript;
        }

        // Send analyse transcript job to queue
        $this->dispatch(new AnalyzeTranscript([
            'videoId'    => $video->id,
            'transcript' => $text,
            'userIds'    => $user_ids
        ]));

        return  $this->please_wait;
    }

    /**
     * Private method to add Youtube Video by passing its identifier.
     *
     * This method was copied and adopted from AjaxController.
     * TODO:: move it somewhere else
     * Method calls YoutubeData API to get video metadata,
     * and inserts a record in Videos table.
     *
     * @param string $identifier
     * @return Video The video that was just added
     */
    private function insert_youtube_video($identifier)
    {
        //TODO:: Move to helper class - this was copied (and adopted) from AjaxController
        $proxy_url = env('CURL_PROXY_URL', '');
        $proxy_user = env('CURL_PROXY_USER', '');
        $proxy_pass = env('CURL_PROXY_PASS', '');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/youtube/v3/videos?part=snippet%2CcontentDetails&id=' . $identifier . '&key=AIzaSyA3o2YPTeh2TCx3bQk_1zoJjliTw2pfeXo');
        if (!empty($proxy_url)) {
            curl_setopt($ch, CURLOPT_PROXY, $proxy_url);
        }
        if (!empty($proxy_user)) {
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy_user . ':' . $proxy_pass);
        }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        if ($errno = curl_errno($ch)) {
            $error_message = curl_strerror($errno);
            error_log('error ' . $errno . ': ' . $error_message);
            return ['error'=>$errno];
        }
        $result = json_decode($response);

        $v = new oval\Models\Video();
        $v->identifier = $identifier;
        $v->title = $result->items[0]->snippet->title;
        $desc = $result->items[0]->snippet->description;
        $v->description = strlen($desc)>507 ? substr($desc, 0, 510) : $desc;
        $v->thumbnail_url = "https://img.youtube.com/vi/".$identifier."/1.jpg";
        $v->duration = ISO8601ToSeconds($result->items[0]->contentDetails->duration);
        $v->media_type = "youtube";
        $v->added_by = Auth::user()->id;
        $v->save();
        curl_close($ch);

        return $v;
    }

    /**
     * Method called from /batch_data_insert to upload json file to insert transcripts data and
     * send text analysis request.
     *
     * JSON being uploaded may have array containing transcript objects or video identifiers.
     * If Transcripts are uploaded, save these then fire off text analysis jobs.
     * If Identifiers are uploaded, get info for the video, save it then trigger text analysis.
     * The method then redirects back to originating page with message to display.
     *
     * @uses insert_youtube_video()
     * @uses AnalyzeTranscript
     *
     * @param Request $req Request contains file
     * @return Illuminate\Http\RedirectResponse
     */
    public function batch_data_insert(Request $req)
    {
        $file = $req->file;
        $path = $file->store('temp');
        $j = trim(Storage::get($path));
        $j = str_replace('&quot;', '"', $j);
        $json = json_decode($j);

        if (isset($json->transcripts)) {
            $transcripts = $json->transcripts;

            foreach ($transcripts as $entry) {
                //-- check for existane of video, if not in db, call data api to fetch data & insert
                $identifier = $entry->identifier;
                $v = oval\Models\Video::where('identifier', '=', $identifier)->first();
                if (empty($v)) {
                    $v = $this->insert_youtube_video($identifier);
                }
                $t = oval\Models\Transcript::where('video_id', '=', $v->id)->first();
                if (empty($t)) {
                    $t = new oval\Models\Transcript();
                    $t->video_id = $v->id;
                }
                $t->transcript = json_encode($entry->transcript);
                $t->save();

                $text = '';
                $transcript_json = json_decode($t->transcript, true);
                foreach ($transcript_json as $tj) {
                    $jsonstr = trim(preg_replace('/\s+/', ' ', $tj)); // preg_replace to remove \n character causing json parsing error
                    $obj = json_decode($jsonstr);
                    $text .= $obj->transcript." ";
                }

                // Send analyse transcript job to queue
                $this->dispatch(new AnalyzeTranscript([
                    'videoId'    => $v->id,
                    'transcript' => $text,
                    'userIds'    => null
                ]));
            }
            $msg = "Video(s) and/or Transcript(s) were inserted, and request(s) were sent for text analysis.";
            return back()->with('msg', $msg);
        } elseif (isset($json->identifiers)) {
            $identifiers = $json->identifiers;

            foreach ($identifiers as $i) {
                $v = oval\Models\Video::where('identifier', '=', $i)->first();
                if (empty($v)) {
                    $v = $this->insert_youtube_video($i);
                }
                $this->process_youtube_analysis($v, [Auth::user()->id]);
            }

            $msg = "Video(s) and/or Transcript(s) were inserted, and request(s) were sent for text analysis.";
            return back()->with('msg', $msg);
        }
    }
}

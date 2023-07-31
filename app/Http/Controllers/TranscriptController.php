<?php

namespace oval\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use oval\Jobs\AnalyzeTranscript;
use oval\Models\AnalysisRequest;
use oval\Models\Transcript;
use oval\Models\Video;

class TranscriptController extends Controller
{
    /** @var string $please_wait Message to show when API request for text analysis is sent successfully.  */
    private $please_wait = 'Request has been sent. Data processing can take some time. Please check back later...';

    /** @var string $no_transcript Error message to show when there is no captions available. */
    private $no_transcript = 'The YouTube video you requested analysis doesn\'t have transcript available for us to use.';

    public function index()
    {
        return view('transcripts.index');
    }

    public function store(Request $request)
    {
        $video_id = intval($request->video_id);
        $file = $request->file;
        $path = $file->store('transcripts');
        $srt = trim(Storage::get($path));

        $text = "[";
        $items = preg_split("/\r\n\r\n|\n\n|\r\r/", $srt);
        foreach ($items as $item) {
            $lines = preg_split("/\r\n|\n|\r/", $item);
            for ($i = 0; $i < count($lines); $i++) {
                if ($i == 0) {
                    $text .= '"{';
                } elseif ($i == 1) {
                    $start_to_end = explode(" ", $lines[$i]);
                    $start_parts = explode(":", $start_to_end[0]);
                    $start = intval($start_parts[0]) * 3600 + intval($start_parts[1]) * 60 + floatval(str_replace(',', '.', $start_parts[2]));
                    $end_parts = explode(":", $start_to_end[2]);
                    $end = intval($end_parts[0]) * 3600 + intval($end_parts[1]) * 60 + floatval(str_replace(',', '.', $end_parts[2]));
                    $text .= '\"start\": ' . $start . ', \"end\": ' . $end . ', \"transcript\": \"';
                } else {
                    $text .= $lines[$i];
                }
                if ($i == count($lines) - 1) {
                    $text .= '\"}",';
                }
            }
        }
        $text = rtrim($text, ",") . ']';

        $transcript = Transcript::find($video_id);
        if (empty($transcript)) {
            $transcript = new Transcript();
        }
        $transcript->video_id = $video_id;
        $transcript->transcript = $text;
        $transcript->save();

        Storage::delete($path);

        return redirect()->route('group_videos.index');
    }

    public function upload(Request $request)
    {
        $file = $request->file;
        $path = $file->store('temp');
        $j = trim(Storage::get($path));
        $j = str_replace('&quot;', '"', $j);
        $json = json_decode($j);

        if (isset($json->transcripts)) {
            $transcripts = $json->transcripts;

            foreach ($transcripts as $entry) {
                //-- check for existane of video, if not in db, call data api to fetch data & insert
                $identifier = $entry->identifier;
                $v = Video::where('identifier', '=', $identifier)->first();
                if (empty($v)) {
                    $v = Video::createFromYoutube($identifier, \Auth::user());
                }
                $t = Transcript::where('video_id', '=', $v->id)->first();
                if (empty($t)) {
                    $t = new Transcript();
                    $t->video_id = $v->id;
                }
                $t->transcript = json_encode($entry->transcript);
                $t->save();

                $text = '';
                $transcript_json = json_decode($t->transcript, true);
                foreach ($transcript_json as $tj) {
                    $jsonstr = trim(preg_replace('/\s+/', ' ', $tj)); // preg_replace to remove \n character causing json parsing error
                    $obj = json_decode($jsonstr);
                    $text .= $obj->transcript . " ";
                }

                // Send analyse transcript job to queue
                $this->dispatch(new AnalyzeTranscript([
                    'videoId' => $v->id,
                    'transcript' => $text,
                    'userIds' => null
                ]));
            }
            $msg = "Video(s) and/or Transcript(s) were inserted, and request(s) were sent for text analysis.";
            return back()->with('msg', $msg);
        } elseif (isset($json->identifiers)) {
            $identifiers = $json->identifiers;

            foreach ($identifiers as $i) {
                $v = Video::where('identifier', '=', $i)->first();
                if (empty($v)) {
                    $v = Video::createFromYoutube($i, \Auth::user());
                }
                $this->process_youtube_analysis($v, [\Auth::user()->id]);
            }

            $msg = "Video(s) and/or Transcript(s) were inserted, and request(s) were sent for text analysis.";
            return back()->with('msg', $msg);
        }
    }

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

        if (!empty($caption_text)) {
            $text = $caption_text;
        } elseif (!empty($video->transcript)) {
            $transcript_json = json_decode($video->transcript->transcript);
            foreach ($transcript_json as $t) {
                $obj = json_decode($t);
                $text .= $obj->transcript . " ";
            }
        } else {
            // Change status to 'processed'
            AnalysisRequest::where(array('video_id' => $video->id))->update(['status' => 'processed']);

            return $this->no_transcript;
        }

        // Send analyse transcript job to queue
        $this->dispatch(new AnalyzeTranscript([
            'videoId' => $video->id,
            'transcript' => $text,
            'userIds' => $user_ids
        ]));

        return $this->please_wait;
    }

}

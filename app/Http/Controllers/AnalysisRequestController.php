<?php

namespace oval\Http\Controllers;

use Illuminate\Http\Request;
use oval\Jobs\AnalyzeTranscript;
use oval\Models\AnalysisRequest;
use oval\Models\GoogleCredential;
use oval\Models\Video;

class AnalysisRequestController extends Controller
{
    /** @var string $please_wait Message to show when API request for text analysis is sent successfully.  */
    private $please_wait = 'Request has been sent. Data processing can take some time. Please check back later...';

    /** @var string $no_transcript Error message to show when there is no captions available. */
    private $no_transcript = 'The YouTube video you requested analysis doesn\'t have transcript available for us to use.';


    public function index()
    {
        $current_requests = AnalysisRequest::where('status', '=', 'pending')
            ->orderBy('created_at')
            ->get()
            ->unique('video_id');
        $rejected_requests = AnalysisRequest::where('status', '=', 'rejected')
            ->orderBy('created_at')
            ->get()
            ->unique('video_id');
        $processed_requests = AnalysisRequest::where('status', '=', 'processed')
            ->orWhere('status', '=', 'processing')
            ->orderBy('created_at')
            ->get()
            ->unique('video_id');
        $google_creds = GoogleCredential::all();
        return view(
            'analysis_requests.index',
            compact(
                'current_requests',
                'rejected_requests',
                'processed_requests',
                'google_creds'
            )
        );
    }

    public function store(Request $request)
    {
        $video_id = intval($request->video_id);
        $user_id = intval($request->user_id);
        $msg = "";
        $ar = AnalysisRequest::where([
            ['video_id', '=', $video_id],
            ['user_id', '=', $user_id]
        ])
            ->first();
        if (!empty($ar)) {
            $msg = "Request for this video already exists. Please wait for OVAL administrator to approve it.";
        } else {
            $ar = new AnalysisRequest();
            $ar->video_id = $video_id;
            $ar->user_id = $user_id;
            $res = $ar->save();
            if ($res) {
                $msg = "Request has been sent to OVAL administrator. Please wait for approval.";
            } else {
                $msg = "There was an error. Please try again later.";
            }
        }
        return compact('msg');
    }

    public function resend(Request $request, AnalysisRequest $analysis_request)
    {
        $video = $analysis_request->video;

        // Change status to 'processing'
        AnalysisRequest::where(array('video_id' => $video->id))->update(['status' => 'processing']);

        $user_ids = $analysis_request->requestorsIds();
        array_push($user_ids, \Auth::user()->id);
        $msg = $this->process_youtube_analysis($video, $user_ids);
        return back()->with('msg', $msg);
    }

    public function reject(AnalysisRequest $analysis_request)
    {
        $analysis_request->status = 'rejected';
        $analysis_request->save();
        return back();
    }

    public function recover(AnalysisRequest $analysis_request)
    {
        $analysis_request->status = 'pending';
        $analysis_request->save();
        return back();
    }

    public function destroy(AnalysisRequest $analysis_request)
    {
        $analysis_request->status = 'deleted';
        $analysis_request->save();
        return back();
    }

    public function batch_resend()
    {
        $video_ids = AnalysisRequest::where('status', 'pending')
            ->pluck('video_id')
            ->toArray();
        $videos = Video::find($video_ids);
        foreach ($videos as $v) {
            $analysis_request = AnalysisRequest::where('video_id', $v->id)->first();
            $this->process_youtube_analysis($v, $analysis_request->requestorsIds());
        }
        return back();
    }

    public function batch_reject()
    {
        AnalysisRequest::where('status', 'pending')
            ->update(['status' => 'rejected']);
        return back();
    }

    public function batch_recover()
    {
        AnalysisRequest::where('status', 'rejected')
            ->update(['status' => 'pending']);
        return back();
    }

    public function batch_delete()
    {
        AnalysisRequest::where('status', 'rejected')
            ->update(['status'=>'deleted']);
        return back();
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

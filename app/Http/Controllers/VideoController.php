<?php

namespace oval\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp;
use oval\Classes\YoutubeDataHelper;
use oval\Jobs\AnalyzeTranscript;
use oval\Models\CommentInstruction;
use oval\Models\Group;
use oval\Models\GroupVideo;
use oval\Models\Point;
use oval\Models\Video;

class VideoController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $v = Video::where(['identifier'=>$request->video_id])->first();

        if (empty($v)) {
            try {
                $v = Video::createFromYoutube($request->video_id, \Auth::user());
            } catch (\Exception $e) {
                return ['error' => $e->getMessage()];
            }
        }

        $course_id = intval($request->course_id);
        if (!empty($course_id)) {
            $group = \oval\Models\Course::find($course_id)
                ->defaultGroup();
            $v->assignToGroup($group);
        }

        if ($v->keywords->count() == 0 && $v->media_type == "youtube") {
            $ar = new \oval\Models\AnalysisRequest();
            $ar->video_id = $v->id;
            $ar->user_id = $v->added_by;
            $ar->save();
            $this->process_youtube_text_analysis($ar);
        }

        return ['course_id' => $course_id, 'video_id' => $v->id];
    }

    /**
     * Display the specified resource.
     */
    public function show(Video $video)
    {
        return $video;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $result = Video::destroy($id);
        return ['result' => $result];
    }

    public function assign(Request $request, Video $video)
    {
        $group_ids = $request->group_ids;

        $copy_from_group_id = intval($request->copy_from);
        $copy_comment_instruction = $request->copy_comment_instruction;
        $copy_points = $request->copy_points;
        $copy_quiz = $request->copy_quiz;

        $copy_origin = $copy_from_group_id == -1 ? null : GroupVideo::where([['group_id', '=', $copy_from_group_id], ['video_id', '=', $video->id]])->first();

        if (count($group_ids) > 0) {
            foreach($group_ids as $gid) {
                $group = Group::find($gid);
                $video->assignToGroup($group);
            }
        }
        if(!empty($copy_origin)) {
            foreach($group_ids as $gid) {
                $gv = GroupVideo::where([
                        ['group_id', '=', $gid],
                        ['video_id', '=', $video->id]
                    ])
                    ->first();
                if($copy_comment_instruction == "true") {
                    $originCommentInstruction = CommentInstruction::where('group_video_id', '=', $copy_origin->id)->first();
                    if (!empty($originCommentInstruction)) {
                        $comment_instruction = CommentInstruction::where('group_video_id', '=', $gv->id)->first();
                        if (empty($comment_instruction)) {
                            $comment_instruction = new CommentInstruction();
                            $comment_instruction->group_video_id = $gv->id;
                        }
                        $comment_instruction->description = $originCommentInstruction->description;
                        $comment_instruction->save();
                    }
                }

                if($copy_points == "true") {
                    $points = Point::where('group_video_id', '=', $gv->id)->get();
                    if($points->count() > 0) {
                        //delete them
                    }
                    $copy_points = Point::where('group_video_id', '=', $copy_origin->id)->get();
                    foreach ($copy_points as $cp) {
                        $p = new Point();
                        $p->group_video_id = $gv->id;
                        $p->description = $cp->description;
                        $p->save();
                    }
                }

                if ($copy_quiz == "true") {
                    //todo: implement after editing quiz
                }
            }
        }
    }

    /**
     * Private method to process Youtube video's text analysis
     * for AnalysisRequest object passed in as parameter.
     * TODO: move this somewhere ... This was copied from another controller.
     *
     * @param \oval\Models\AnalysisRequest $analysis_request
     * @return void
     */
    private function process_youtube_text_analysis(\oval\Models\AnalysisRequest $analysis_request)
    {
        //--exit if this video already as result--
        $requests = $analysis_request->requestsForSameVideo();
        foreach ($requests as $r) {
            if ($r->status == "processed") {
                return;
            }
        }

        $video = $analysis_request->video;

        // Change status to 'processing'
        \oval\Models\AnalysisRequest::where(array('video_id' => $video->id))->update(['status' => 'processing']);

        $user_ids = $analysis_request->requestorsIds();
        array_push($user_ids, Auth::user()->id);

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
            \oval\Models\AnalysisRequest::where(array('video_id' => $video->id))->update(['status' => 'processed']);
            return "no transcript";
        }

        // Send analyse transcript job to queue
        $this->dispatch(new AnalyzeTranscript([
            'videoId' => $video->id,
            'transcript' => $text,
            'userIds' => $user_ids
        ]));
    }
}

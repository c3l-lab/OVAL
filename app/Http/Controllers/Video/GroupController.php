<?php

namespace oval\Http\Controllers\Video;

use Illuminate\Http\Request;
use oval\Http\Controllers\Controller;
use oval\Models\Group;
use oval\Models\GroupVideo;
use oval\Models\QuizCreation;
use oval\Models\Video;

class GroupController extends Controller
{
    public function index(Video $video)
    {
        $groups = $video->groups;
        return [
          'groups' => $groups,
        ];
    }

    /**
     * This method receives video_id and returns groups that has access to this video,
     * along with whether the groups have contents that can be copied (comment instruction, points, quiz)
     */
    public function withContents(Video $video)
    {
        $the_groups = Group::whereIn("id", function ($q) use ($video) {
            $q->select('group_id')
              ->from('group_videos')
              ->where('video_id', '=', $video->id)
              ->get();
        })
          ->get();
        $groups = collect();

        foreach ($the_groups as $g) {
            $group_video = GroupVideo::where([
              ['video_id', '=', $video->id],
              ['group_id', '=', $g->id]
            ])
              ->first();
            $points = $group_video->relatedPoints();
            $quiz = $group_video->quiz;

            $group = [
              "course_id" => $g->course->id,
              "course_name" => $g->course->name,
              "id" => $g->id,
              "name" => $g->name,
              "has_comment_instruction" => !empty($comment_instruction),
              "has_points" => $points->count(),
              "has_quiz" => !empty($quiz),
              "group_video_id" => $group_video->id,
              "course" => $group_video->course()->name,
              "def_group" => $group_video->course()->defaultGroup()->name,
              "def_group_comment_inst" => $group_video->course()->defaultGroup()->comment_instruction
            ];
            $groups->push($group);
        }


        $groups = $groups->groupBy('course_id');
        return $groups;
    }
}

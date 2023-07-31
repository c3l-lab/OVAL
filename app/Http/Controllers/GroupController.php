<?php

namespace oval\Http\Controllers;

use Illuminate\Http\Request;
use oval\Models\Course;
use oval\Models\Group;
use oval\Models\Video;

class GroupController extends Controller
{
    public function unassigned(Request $request)
    {
        $course_id = intval($request->course_id);
        $video_id = intval($request->video_id);
        $all_groups = Course::find($course_id)->groups;
        $assigned_groups = Video::find($video_id)->groups;
        $unassigned_groups = $all_groups->reject(function ($val) use ($assigned_groups) {
            return $assigned_groups->contains($val);
        });
        return [
            'unassigned_groups' => $unassigned_groups,
        ];
    }
}

<?php

namespace oval\Http\Controllers;

use Illuminate\Http\Request;
use oval\Models\Course;
use oval\Models\Group;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $user = \Auth::user();
        $course_id = $request->input('course_id');
        $group_id = $request->input('group_id');
        $courses = $user->coursesTeaching();
        $course_id = $course_id ? $course_id : $request->session()->get('current-course', 0);
        $course = $course_id ? Course::find($course_id) : $user->enrolledCourses->first();
        if (!$user->isInstructorOf($course)) {
            foreach ($courses as $c) {
                $course = $c;
                break;
            }
        }
        $group = $group_id ? Group::find($group_id) : $course->groups->first();
        \JavaScript::put([
            'course_id' => $course->id,
            'course_name' => $course->name,
            'group_id' => $group->id,
            'group_name' => $group->name,
        ]);

        // save current course id
        session(['current-course' => $course->id]);

        return view('analytics.index', compact('user', 'courses', 'course', 'group'));
    }
}

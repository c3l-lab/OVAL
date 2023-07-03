<?php

namespace oval\Http\Controllers;

use Illuminate\Http\Request;
use oval\GroupVideo;
use oval\quiz_creation;
use oval\Tracking;

class GroupVideoController extends Controller
{
    public function embed(Request $request, int $id)
    {

        $user = \Auth::user();
        $api_token = $user->api_token;
        $course = null;
        $group = null;
        $group_video = null;

        $group_video = $this->getGroupVideo($id);
        $group = $group_video->group();
        $course = $group->course;

        $video = $group_video->video();

        $group_members = [];
        foreach ($group->students() as $student) {
            $group_members[] = [
                "id" => $student->id,
                "name" => $student->fullName(),
            ];
        }

        // Log every user views
        if (!empty($user) && !empty($video)) {
            $tracking = new Tracking;
            $tracking->group_video_id = $group_video->id;
            $tracking->user_id = $user->id;
            $tracking->event = "View";
            $tracking->event_time = date("Y-m-d H:i:s");
            $tracking->save();
        }

        $keywords = $video->keywords->unique('keyword')->sortBy('keyword', SORT_NATURAL | SORT_FLAG_CASE);
        $analysis = null;
        if (!empty($keywords)) {
            $currents = [];
            $time = null;
            foreach ($keywords as $k) {
                if (($k->type == "keywords") || ($k->type == "concepts")) {
                    $analysis[] = ['text' => $k->keyword, 'occurrences' => $k->occurrences(), 'related' => $k->related()];
                }

                //--construct array containing data for "current keywords"--
                $time = intval(floor($k->startTime));
                if (!array_key_exists($time, $currents)) {
                    $currents[$time] = [$k->keyword];
                } else {
                    array_push($currents[$time], $k->keyword);
                }
            }
        }

        $quizzes = quiz_creation::where('identifier', '=', $video->identifier)->get();
        $has_quiz = $quizzes->count() ? true : false;

        \JavaScript::put([
            'MINE' => 1,
            'INSTRUCTORS' => 2,
            'STUDENTS' => 3,
            'ALL' => 4,
            'user_id' => $user->id,
            'is_instructor' => $user->isInstructorOf($course),
            'user_fullname' => $user->fullName(),
            'course_id' => $course->id,
            'course_name' => $course->name,
            'group_id' => $group->id,
            'group_name' => $group->name,
            'group_members' => $group_members,
            'video_id' => $video->id,
            'video_identifier' => $video->identifier,
            'video_name' => htmlspecialchars($video->title, ENT_QUOTES),
            'video_duration' => $video->duration,
            'thumbnail_url' => $video->thumbnail_url,
            'media_type' => $video->media_type,
            // 'transcript_path'=>$transcript_path,
            'text_analysis' => $analysis,
            'current_keywords' => $currents,
            'group_video_id' => $group_video->id,
            'points' => $group_video->relatedPoints(),
            'api_token' => $api_token,
            'helix_server_host' => env('HELIX_SERVER_HOST', 'https://helix.example.com'),
            'helix_js_host' => env('HELIX_JS_HOST', 'https://helix.example.com'),
        ]);

        // save current course id
        session(['current-course' => $course->id]);

        return view('group_videos.embed', compact('user', 'course', 'group', 'video', 'group_video', 'has_quiz'));

    }

    public function toggleComments(Request $request, int $id)
    {
        $groupVideo = $this->getGroupVideo($id);
        $groupVideo->show_comments = !$groupVideo->show_comments;
        $groupVideo->save();
        return response()->json(['success' => true]);
    }

    public function toggleAnnotations(Request $request, int $id)
    {
        $groupVideo = $this->getGroupVideo($id);
        $groupVideo->show_annotations = !$groupVideo->show_annotations;
        $groupVideo->save();
        return response()->json(['success' => true]);
    }

    private function getGroupVideo($id) {
        return GroupVideo::findOrFail($id);
    }
}

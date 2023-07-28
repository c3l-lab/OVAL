<?php

namespace oval\Http\Controllers;

use Illuminate\Http\Request;
use oval\Models\GroupVideo;

class GroupVideoController extends Controller
{
    public function index(Request $request)
    {
        $user = \Auth::user();
        $course_id = $request->query('course_id');
        $group_id = $request->query('group_id');
        $api_token = $user->api_token;
        if ($user->isAnInstructor()) {
            $courses_teaching = $user->coursesTeaching();
            $course_id = $course_id ? $course_id : $request->session()->get('current-course', 0);
            $course = $course_id ? \oval\Models\Course::find($course_id) : $user->enrolledCourses->first();
            if (!$user->isInstructorOf($course)) {
                foreach ($courses_teaching as $c) {
                    $course = $c;
                    break;
                }
            }
            $group = $group_id ? \oval\Models\Group::find($group_id) : $course->groups->first();
            $videos_without_group = \oval\Models\Video::doesntHave('groups')->get();
            $group_videos = $group->group_videos()->where('status', 'current');

            \JavaScript::put([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'course_name' => $course->name,
                'group_id' => $group->id,
                'group_name' => $group->name,
                'api_token' => $api_token,
            ]);

            // save current course id
            session(['current-course' => $course->id]);

            return view('group_videos.index', [
                'user' => $user,
                'course' => $course,
                'group' => $group,
                'videos_without_group' => $videos_without_group,
                'group_videos' => $group_videos,
            ]);
        } else {
            return view('pages.not-instructor', compact('user'));
        }
    }

    public function show(Request $request, int $id)
    {
        return view('group_videos.show', $this->view($id));
    }
    public function embed(Request $request, int $id)
    {
        return view('group_videos.embed', $this->view($id));
    }

    private function view($id)
    {
        $user = \Auth::user();
        $api_token = $user->api_token;
        $course = null;
        $group = null;
        $group_video = null;
        $group_video_id = intval($id);

        $group_video = \oval\Models\GroupVideo::findOrFail($group_video_id);

        $group = $group_video->group();
        $course = $group->course;

        if (
            !$user->isInstructorOf($course) &&
            (!$user->checkIfEnrolledIn($course) || !$user->checkIfInGroup($group) || $group_video->hide)
        ) {
            abort(404);
        }

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
            $tracking = new \oval\Models\Tracking();
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

        $quizzes = \oval\Models\quiz_creation::where('identifier', '=', $video->identifier)->get();
        $has_quiz = $quizzes->count() ? true : false;

        \JavaScript::put([
            'MINE' => 1,
            'INSTRUCTORS' => 2,
            'STUDENTS' => 3,
            'ALL' => 4,
            'user_id' => $user->id,
            'is_instructor' => $user->isInstructorOf($course),
            'comment_instruction' => $group_video->comment_instruction ? $group_video->comment_instruction->description : null,
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
        ]);

        // save current course id
        session(['current-course' => $course->id]);

        return [
            'user' => $user,
            'course' => $course,
            'group' => $group,
            'video' => $video,
            'group_video' => $group_video,
            'has_quiz' => $has_quiz,
        ];
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

    private function getGroupVideo($id)
    {
        return GroupVideo::findOrFail($id);
    }
}

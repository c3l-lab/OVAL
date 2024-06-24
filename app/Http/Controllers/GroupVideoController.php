<?php

namespace oval\Http\Controllers;

use Illuminate\Http\Request;
use oval\Models\GroupVideo;

use function Laravel\Prompts\select;

class GroupVideoController extends Controller
{
    public function index(Request $request)
    {
        $user = \Auth::user();
        $course_id = $request->query('course_id');
        $group_id = $request->query('group_id');
        $api_token = $user->api_token;
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
        $group_videos = GroupVideo::with('quiz')->whereBelongsTo($group)->where('status', 'current')->get();

        \JavaScript::put([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'course_name' => $course->name,
            'group_id' => $group->id,
            'group_name' => $group->name,
            'api_token' => $api_token,
            'group_videos' => $group_videos,
        ]);

        // save current course id
        session(['current-course' => $course->id]);

        return response()
            ->view('group_videos.index', [
                'user' => $user,
                'course' => $course,
                'group' => $group,
                'videos_without_group' => $videos_without_group,
                'group_videos' => $group_videos,
            ])
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function show(Request $request, int $id)
    {
        return view('group_videos.show', $this->view($id));
    }
    public function embed(Request $request, int $id)
    {
        return view('group_videos.embed', $this->view($id));
    }

    public function config_structured_annotation(int $id, Request $request) {
        $gv = GroupVideo::find($id);

        if (!$gv) {
            return ['result' => 'failed', 'message' => 'GroupVideo not found'];
        }
    
        $annotationConfig = $gv->annotation_config;
    
        $annotationConfig['structured_annotations'] = $request->input('structured_annotations', []);

        $gv->annotation_config = $annotationConfig;
    
        $gv->save();
    
        return ['result' => 'success'];
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

        $group = $group_video->group;
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
            $this->track($group_video->id, [
                "event" => "View",
                "event_time" => date("Y-m-d H:i:s")
            ]);
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

        $has_quiz = !empty($group_video->quiz);
        $is_instructor =  $user->isInstructorOf($course);

        \JavaScript::put([
            'MINE' => 1,
            'INSTRUCTORS' => 2,
            'STUDENTS' => 3,
            'ALL' => 4,
            'user_id' => $user->id,
            'is_instructor' => $is_instructor,
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
            'Oval' => [
                'currentGroupVideo' => $group_video,
            ]
        ]);

        // save current course id
        session(['current-course' => $course->id]);

        return [
            'user' => $user,
            'is_instructor' => $is_instructor,
            'course' => $course,
            'group' => $group,
            'video' => $video,
            'group_video' => $group_video,
            'has_quiz' => $has_quiz,
        ];
    }

    public function archive(GroupVideo $groupVideo)
    {
        $groupVideo->status = "archived";
        $result = $groupVideo->save();
        return compact('result');
    }

    public function destroy(int $id)
    {
        $result = GroupVideo::destroy($id);
        return compact('result');
    }

    public function toggleVisibility(Request $request, GroupVideo $groupVideo)
    {
        $vis = intval($request->visibility);
        $groupVideo->hide = $vis;
        $groupVideo->save();
    }

    public function toggleComments(Request $request, GroupVideo $groupVideo)
    {
        $groupVideo->show_comments = !$groupVideo->show_comments;
        $groupVideo->save();
        return response()->json(['success' => true]);
    }

    public function toggleAnnotations(Request $request, GroupVideo $groupVideo)
    {
        $groupVideo->show_annotations = !$groupVideo->show_annotations;
        $groupVideo->save();
        return response()->json(['success' => true]);
    }

    public function toggleAnalysis(Request $request, GroupVideo $groupVideo)
    {
        $show = intval($request->visibility);
        $groupVideo->show_analysis = $show;
        $groupVideo->save();
    }

    public function byCourse(Request $request)
    {
        $user = \Auth::user();
        $course_id = $request->query('course_id');
        $course = \oval\Models\Course::find(intval($course_id));
        if (!empty($course) && $user->checkIfEnrolledIn($course) == true) {
            $group = $user->groupMemberOf->where('course_id', '=', $course->id)->first();
            if (!empty($group)) {
                $group_videos = $group->availableGroupVideosForUser($user);
                if ($group_videos->count() > 0) {
                    return redirect()->route('group_videos.show', ['group_video' => $group_videos->first()]);
                }
            }
        }
        return view('pages.no-video');
    }

    public function byGroup(Request $request)
    {
        $user = \Auth::user();
        $group_id = $request->query('group_id');
        $group = \oval\Models\Group::find(intval($group_id));
        if (!empty($group) && $user->checkIfInGroup($group) == true) {
            $group_videos = $group->availableGroupVideosForUser($user);
            if ($group_videos->count() > 0) {
                return redirect()->route('group_videos.show', ['group_video' => $group_videos->first()]);
            }
        }
        return view('pages.no-video');
    }

    public function sort(Request $request)
    {
        $group_video_ids = $request->group_video_ids;
        $i = 1;
        foreach ($group_video_ids as $gv_id) {
            $group_video = GroupVideo::find($gv_id);
            $group_video->order = $i;
            $group_video->save();
            $i++;
        }
    }

    public function calibrate(Request $request){
        return view('group_videos.calibration');
    }
}

<?php

namespace oval\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use JavaScript;
use oval;

/**
 * This class handles requests for pages
 * todo: rename it to PagesController
 */
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = \Auth::user();
        $group = $user->groupMemberOf->first();
        if (!empty($group)) {
            $course = $group->course;
            if ($user->isInstructorOf($course)) {
                $group_video = $group->group_videos()
                    ->where('status', '=', 'current')
                    ->first();
            } else {
                $group_video = $group->group_videos()
                    ->where('status', '=', 'current')
                    ->where('hide', '=', false)
                    ->first();
            }
            if (!empty($group_video)) {
                return redirect()->route('group_videos.show', ['group_video' => $group_video]);
            }
        }
        return view('pages.no-video');
    }

    /**
     * Method called for /analytics route
     *
     * Sets up variables for the logged in instructor,
     * sets up variables for JavaScript,
     * and shows analytics page.
     * If the visitor isn't logged in instructor, shows error page.
     *
     * @param Request $req
     * @param string $course_id Default null
     * @param string $group_id Default null
     * @return Illuminate\Support\Facades\View
     */
    public function analytics(Request $req, $course_id = null, $group_id = null)
    {
        $user = Auth::user();
        if ($user->isAnInstructor()) {
            $courses = $user->coursesTeaching();
            $course_id = $course_id ? $course_id : $req->session()->get('current-course', 0);
            $course = $course_id ? oval\Models\Course::find($course_id) : $user->enrolledCourses->first();
            if (!$user->isInstructorOf($course)) {
                foreach ($courses as $c) {
                    $course = $c;
                    break;
                }
            }
            $group = $group_id ? oval\Models\Group::find($group_id) : $course->groups->first();
            JavaScript::put([
                'course_id' => $course->id,
                'course_name' => $course->name,
                'group_id' => $group->id,
                'group_name' => $group->name,
            ]);

            // save current course id
            session(['current-course' => $course->id]);

            return view('pages.analytics', compact('user', 'courses', 'course', 'group'));
        } else {
            return view('pages.not-instructor', compact('user'));
        }
    }

    /**
     * Method called from /content-analysis route
     *
     * Fetches data for the logged in instructor,
     * sets up JavaScript variables,
     * and shows the content-analysis page.
     * If the visitor isn't a logged-in instructor, shows error page.
     * @param Request $req
     * @param string $course_id Default null
     * @param string $group_id Default null
     * @return Illuminate\Support\Facades\View
     */
    public function content_analysis(Request $req, $course_id = null, $group_id = null)
    {
        $user = Auth::user();
        $courses = $user->coursesTeaching();
        if ($user->isAnInstructor()) {
            $course_id = $course_id ? $course_id : $req->session()->get('current-course', 0);
            $course = $course_id ? oval\Models\Course::find($course_id) : $user->enrolledCourses->first();
            if (!$user->isInstructorOf($course)) {
                foreach ($courses as $c) {
                    $course = $c;
                    break;
                }
            }
            $group = $group_id ? oval\Models\Group::find($group_id) : $course->groups->first();
            $videos = $group->videos;
            JavaScript::put([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'course_name' => $course->name,
                'group_id' => $group->id,
                'group_name' => $group->name,
            ]);
            session(['current-course' => $course->id]);
            return view('pages.content-analysis', compact('user', 'courses', 'course', 'group', 'videos'));
        } else {
            return view('pages.not-instructor', compact('user'));
        }
    }

    /**
     * Method called from /points-details route
     *
     * Shows points-details page with data for group_video whose id passed in.
     * If group_video isn't found, or visitor isn't instructor, shows error page.
     *
     * @param Request $req
     * @param string $group_video_id
     */
    public function points_details(Request $req, $group_video_id)
    {
        $user = Auth::user();
        if ($user->isAnInstructor()) {
            $group_video = oval\Models\GroupVideo::find($group_video_id);
            if (!empty($group_video)) {
                return view('pages.points-details', compact('user', 'group_video'));
            } else {
                return view('pages.no-video', compact('user'));
            }
        } else {
            return view('pages.not-instructor', compact('user'));
        }
    }

    /**
     * Method called from /tracking-details/{group_video_id}
     *
     * Shows tracking-details page with data for group_video whose id passed in.
     * If the group_video doesn't exist, or the visitor isn't a logged-in instructor,
     * shows error page.
     *
     * @param Request $req
     * @param string $group_video_id
     * @return Illuminate\Support\Facades\View
     */
    public function tracking_details(Request $req, $group_video_id)
    {
        $user = Auth::user();
        if ($user->isAnInstructor()) {
            $group_video = oval\Models\GroupVideo::find($group_video_id);
            if (!empty($group_video)) {
                return view('pages.tracking-details', compact('user', 'group_video'));
            } else {
                return view('pages.no-video', compact('user'));
            }
        } else {
            return view('pages.not-instructor', compact('user'));
        }
    }

    /**
     * Method called from /text-analysis-details route
     *
     * Shows text-analysis-details page with data fetched for video_id passed in.
     * If the visitor isn't a logged in instructor, shows error page.
     *
     * @param Request $req Request contains video_id.
     * @return Illuminate\Support\Facades\View
     */
    public function text_analysis_details(Request $req)
    {
        $user = Auth::user();
        if ($user->isAnInstructor()) {
            $video = oval\Models\Video::find(intval($req->video_id));
            $analysis = json_decode($video->transcript->analysis, true);
            $group = $video->groups->first();
            $group_id = $group->id;
            $course_id = $group->course->id;

            return view('pages.text-analysis-details', compact('user', 'video', 'analysis', 'course_id', 'group_id'));
        } else {
            return view('pages.not-instructor', compact('user'));
        }

    }

    /**
     * Method called from /select-video route, where instructor clicking LTI link is redirected to
     * if the link's resource_id isn't associated with any group_video.
     *
     * Shows instructor-video-selection page after setting up variables.
     * If the visitor isn't an instructor, shows error page.
     *
     * @param Request $req
     * @param string $link_id Resource_id of LTI request
     * @param string $group_video_id Default null.
     * @return Illuminate\Support\Facades\View
     */
    public function select_video(Request $req, $link_id, $group_video_id = null)
    {
        $user = Auth::user();

        if ($user->isAnInstructor()) {
            $courses = $user->coursesTeaching();
            $groups = collect();
            foreach ($courses as $c) {
                $groups = $groups->merge($c->groups);
            }
            $group_videos = collect();
            foreach ($groups as $g) {
                $group_videos = $group_videos->merge($g->group_videos()->where('status', 'current')->all());
            }
            $current = !empty($group_video_id) ? $group_video_id : null;

            JavaScript::put([
                'link_id' => $link_id,
                'current_group_video_id' => $current
            ]);
            return view('pages.instructor-video-selection', compact('user', 'group_videos', 'link_id'));
        } else {
            return view('pages.not-instructor', compact('user'));
        }

    }

    /**
     * Method called from /manage-analysis-request route.
     *
     * Fetches analysis_request data, and shows admin-page for Administrator user.
     * If the visitor is not admin, shows error page.
     *
     * @param Request $req
     */
    public function manage_analysis_requests(Request $req)
    {
        $user = Auth::user();
        if ($user->role == 'A') {
            $current_requests = oval\Models\AnalysisRequest::where('status', '=', 'pending')
                ->orderBy('created_at')
                ->get()
                ->unique('video_id');
            $rejected_requests = oval\Models\AnalysisRequest::where('status', '=', 'rejected')
                ->orderBy('created_at')
                ->get()
                ->unique('video_id');
            $processed_requests = oval\Models\AnalysisRequest::where('status', '=', 'processed')
                ->orWhere('status', '=', 'processing')
                ->orderBy('created_at')
                ->get()
                ->unique('video_id');
            $google_creds = oval\Models\GoogleCredential::all();
            return view('pages.admin-page', compact('user', 'current_requests', 'rejected_requests', 'processed_requests', 'google_creds'));
        } else {
            return view('pages.not-admin', compact('user'));
        }
    }

    public function batch_upload(Request $req)
    {
        $user = Auth::user();
        if ($user->role == 'A') {
            return view('pages.batch-upload');
        } else {
            return view('pages.not-admin', compact('user'));
        }
    }

    /**
     * Method called from GET route /manage_lti_connections
     *
     * Show manage-lti page if the logged in user is admin,
     * error page if not.
     */
    public function manage_lti_connections(Request $req)
    {
        $user = Auth::user();
        if ($user->role == 'A') {
            $lti_connections = oval\Models\LtiConsumer::all();
            return view('pages.manage-lti', compact('user', 'lti_connections'));
        } else {
            return view('pages.not-admin', compact('user'));
        }
    }

} //end class

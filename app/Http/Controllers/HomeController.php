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
} //end class

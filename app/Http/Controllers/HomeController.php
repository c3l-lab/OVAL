<?php

namespace oval\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
	
	/**
	 * Method called from route /course/{$course_id}
	 * 
	 * If the user is enrolled in the course whose id passed in,
	 * find the first group in that course user belongs to,
	 * redirect to /view to show first group_video for that group.
	 * If any of above checks fail, show no-video error page.
	 * 
	 * @param Request $req 
	 * @param string $course_id
	 * @return Illuminate\Http\RedirectResponse or Illuminate\Support\Facades\View
	 */
	public function course(Request $req, $course_id) {
		$user = Auth::user();
		$course = oval\Course::find(intval($course_id));
		if (!empty($course)&&$user->checkIfEnrolledIn($course)==true) {
			$group = $user->groupMemberOf->where('course_id', '=', $course->id)->first();
			if(!empty($group)) {
				$group_videos = $group->availableGroupVideosForUser($user);
				if($group_videos->count()>0) {
					return redirect()->secure('/view/'.$group_videos->first()->id);
				}
			}
		}
		return view('pages.no-video', compact('user'));
	}

	/**
	 * Method called from route /group/{$group_id}
	 * 
	 * If the user belongs to the group whose id passed in as parameter,
	 * find the first group_video for that group,
	 * and redirect to /view with id of that group_video.
	 * If any checks fail, show no-video error page.
	 * 
	 * @param Request $req
	 * @param string $group_id
	 * @return Illuminate\Http\RedirectResponse or Illuminate\Support\Facades\View
	 */
	public function group(Request $req, $group_id) {
		$user = Auth::user();
		$group = oval\Group::find(intval($group_id));
		if (!empty($group) && $user->checkIfInGroup($group)==true) {
			$group_videos = $group->availableGroupVideosForUser($user);
			if($group_videos->count()>0) {
				return redirect()->secure('/view/'.$group_videos->first()->id);
			}
		}
		return view('pages.no-video', compact('user'));
	}

	/**
	 * Method called from route /view. This shows the home page of OVAL.
	 * 
	 * Fetches data and sets up JavaScript variables.
	 * Shows home page with group_video whose ID passed in. 
	 * If no group_video_id is passed in, find the first group_video available for 
	 * the user that is currently logged in, and show home page with this.
	 * If there is no group_video to show, returns no-video error page.
	 * @param Request $req
	 * @param string $group_video_id Default null
 	 * @return Illuminate\Http\RedirectResponse or Illuminate\Support\Facades\View
	 */
	public function view(Request $req, $group_video_id=null) {
		$user = Auth::user();
		$api_token = $user->api_token;
		$course = null;
		$group = null;
		$group_video = null;
		$group_video_id = intval($group_video_id);
		
		if (!empty($group_video_id)) {
			$group_video = oval\GroupVideo::find($group_video_id);
			if (!empty($group_video)) {
				$group = $group_video->group();
				$course = $group->course;

				if (!$user->isInstructorOf($course) &&
						(!$user->checkIfEnrolledIn($course) || !$user->checkIfInGroup($group) || $group_video->hide)) {
					$group_video = null;
				}
			}
			if (empty($group_video)) {
				return view('pages.no-video', compact('user'));
			}
		}
		else {
			//--find first group_video available for the user if no id passed in
			$group = $user->groupMemberOf->first();
			if (!empty($group)) {
				$course = $group->course;
				if($user->isInstructorOf($course)) {
					$group_video = $group->group_videos()
										->where('status','=','current')
										->first();
				}
				else {
					$group_video = $group->group_videos()
										->where('status','=','current')
										->where('hide', '=', false)
										->first();
				}
			}
			if (empty($group_video)) {
				return view('pages.no-video', compact('user'));
			}
		}
		$video = $group_video->video();





		$comment_instruction = empty($group_video->relatedCommentInstruction()) ? null : $group_video->relatedCommentInstruction()->description;

		$group_members = [];
		foreach ($group->students() as $student) {
			$group_members[] = [
				"id" => $student->id,
				"name" => $student->fullName(),
			];
		}

		// Log every user views
		if (!empty($user) && !empty($video)) {
			$tracking = new oval\Tracking;
			$tracking->group_video_id = $group_video->id;
			$tracking->user_id = $user->id;
			$tracking->event = "View";
			$tracking->event_time = date("Y-m-d H:i:s");
			$tracking->save();
		}

		$point_instruction = $group_video->relatedPointInstruction();
		$instruction = $point_instruction ? $point_instruction->description : "";

		//--if there's transcript, save a .srt file
		//--and get the analysis value if exists
		// $transcript_path = null;
		// $transcript = $video->transcript;
		// if (!empty($transcript)) {
		// 	$filename = $video->id.".srt";
		// 	$destination = public_path().'/transcript/'.$filename;
		// 	$json_transcript = json_decode($transcript->transcript);
		// 	$srt_transcript = "";
		// 	for($i=0; $i<count($json_transcript); $i++) {
		// 		$json_transcript[$i] = json_decode($json_transcript[$i]);
		// 		$srt_transcript.= ($i+1)."\r\n";
		// 		$start = intval($json_transcript[$i]->start);
		// 		$end = intval($json_transcript[$i]->end);
		// 		$srt_transcript.= gmdate("H:i:s", $start)." --> ".gmdate("H:i:s",$end)."\r\n";
		// 		$srt_transcript.= wordwrap(trim($json_transcript[$i]->transcript), 50, "\r\n")."\r\n\r\n";
		// 	}

		// 	file_put_contents($destination, $srt_transcript);
		// 	$transcript_path = url('transcript/'.$filename);
		// }

		$keywords = $video->keywords->unique('keyword')->sortBy('keyword', SORT_NATURAL|SORT_FLAG_CASE);
		$analysis = null;
		if(!empty($keywords)) {
			$currents = [];
			$time = null;
			foreach ($keywords as $k) {
				if (($k->type == "keywords") || ($k->type == "concepts")) {
					$analysis[] = ['text'=>$k->keyword, 'occurrences'=>$k->occurrences(), 'related'=>$k->related()];                
				}

				//--construct array containing data for "current keywords"--
				$time = intval(floor($k->startTime));
				if (!array_key_exists($time, $currents)) {
					$currents[$time] = [$k->keyword];
				}
				else {
					array_push($currents[$time], $k->keyword);
				}
			}
		}

		$quizzes = oval\quiz_creation::where('identifier', '=', $video->identifier)->get();
		$has_quiz = $quizzes->count() ? true : false;

		JavaScript::put([
			'MINE'=>1, 'INSTRUCTORS'=>2, 'STUDENTS'=>3, 'ALL'=>4,
			'user_id'=>$user->id,
			'is_instructor'=>$user->isInstructorOf($course),
			'user_fullname'=>$user->fullName(),
			'course_id'=>$course->id,
			'course_name'=>$course->name,
			'group_id'=>$group->id,
			'group_name'=>$group->name,
			'group_members'=>$group_members,
			'video_id'=>$video->id,
			'video_identifier'=>$video->identifier,
			'video_name'=>htmlspecialchars($video->title, ENT_QUOTES),
			'video_duration'=>$video->duration,
			'thumbnail_url'=>$video->thumbnail_url,
			'media_type'=>$video->media_type,
			// 'transcript_path'=>$transcript_path,
			'comment_instruction'=>$comment_instruction,
			'text_analysis'=>$analysis,
			'current_keywords'=>$currents,
			'group_video_id'=>$group_video->id,
			'points'=>$group_video->relatedPoints(),
			'point_instruction'=>htmlspecialchars($instruction, ENT_QUOTES),
			'api_token'=>$api_token,
			'helix_server_host'=>env('HELIX_SERVER_HOST', 'https://helix.example.com'),
			'helix_js_host'=>env('HELIX_JS_HOST', 'https://helix.example.com'),
		]);

		// save current course id
		session(['current-course' => $course->id]);
		
		return view('pages.home', compact('user', 'course', 'group', 'video', 'group_video', 'has_quiz'));
    }

	/**
	 * Method called from route /video-management
	 * 
	 * Fetches data for the logged in instructor,
	 * sets up JavaScript variables,
	 * and shows video-managment page.
	 * If the visitor is not an instructor, shows error page
	 * 
	 * @param Request $req
	 * @param string $course_id Default null
	 * @param string $group_id Default null
	 * @return Illuminate\Support\Facades\View
	 */
	public function video_management(Request $req, $course_id=null, $group_id=null) {
    	$user = Auth::user();
    	$api_token = $user->api_token;
    	if ($user->isAnInstructor()) {
				$courses_teaching = $user->coursesTeaching();
				$course_id = $course_id ? $course_id : $req->session()->get('current-course', 0);
				$course = $course_id ? oval\Course::find($course_id) : $user->enrolledCourses->first();
				if (!$user->isInstructorOf($course)) {
					foreach ($courses_teaching as $c) {
						$course = $c;
						break;
					}
				}
				$group = $group_id ? oval\Group::find($group_id) : $course->groups->first();
				$videos_without_group = oval\Video::doesntHave('groups')->get();
				$group_videos = $group->group_videos()->where('status', 'current');

				JavaScript::put([
					'user_id'=>$user->id,
					'course_id'=>$course->id,
					'course_name'=>$course->name,
					'group_id'=>$group->id,
					'group_name'=>$group->name,
					'api_token'=>$api_token,
					'helix_server_host'=>env('HELIX_SERVER_HOST', 'https://helix.example.com'),
					'helix_js_host'=>env('HELIX_JS_HOST', 'https://helix.example.com'),
				]);

				// save current course id
				session(['current-course' => $course->id]);

    		return view('pages.video-management', compact('user', 'course', 'group', 'videos_without_group', 'group_videos'));
    	} else {
    		return view('pages.not-instructor', compact('user'));
    	}
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
    public function analytics (Request $req, $course_id=null, $group_id=null) {
    	$user = Auth::user();
    	if($user->isAnInstructor()) {
    		$courses = $user->coursesTeaching();
			$course_id = $course_id ? $course_id : $req->session()->get('current-course', 0);
    		$course = $course_id ? oval\Course::find($course_id) : $user->enrolledCourses->first();
			if (!$user->isInstructorOf($course)) {
				foreach ($courses as $c) {
					$course = $c;
					break;
				}
			}
			$group = $group_id ? oval\Group::find($group_id) : $course->groups->first();
			JavaScript::put([
				'course_id'=>$course->id,
				'course_name'=>$course->name,
				'group_id'=>$group->id,
				'group_name'=>$group->name,
			]);

			// save current course id
			session(['current-course' => $course->id]);

    		return view('pages.analytics', compact('user','courses', 'course', 'group'));
    	}
    	else {
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
	public function content_analysis (Request $req, $course_id=null, $group_id=null) {
		$user = Auth::user();
		$courses = $user->coursesTeaching();
		if($user->isAnInstructor()) {
			$course_id = $course_id ? $course_id : $req->session()->get('current-course', 0);
    		$course = $course_id ? oval\Course::find($course_id) : $user->enrolledCourses->first();
			if (!$user->isInstructorOf($course)) {
				foreach ($courses as $c) {
					$course = $c;
					break;
				}
			}
			$group = $group_id ? oval\Group::find($group_id) : $course->groups->first();
			$videos = $group->videos;
			JavaScript::put([
				'user_id'=>$user->id,
				'course_id'=>$course->id,
				'course_name'=>$course->name,
				'group_id'=>$group->id,
				'group_name'=>$group->name,
			]);
			session(['current-course' => $course->id]);
    		return view('pages.content-analysis', compact('user','courses', 'course', 'group', 'videos'));
		}
		else {
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
	public function points_details (Request $req, $group_video_id) {
		$user= Auth::user();
		if($user->isAnInstructor()) {
			$group_video = oval\GroupVideo::find($group_video_id);
			if(!empty($group_video)) {
				return view('pages.points-details', compact('user', 'group_video'));
			}
			else {
				return view('pages.no-video', compact('user'));
			}
		}
		else {
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
	public function tracking_details (Request $req, $group_video_id) {
		$user= Auth::user();
		if($user->isAnInstructor()) {
			$group_video = oval\GroupVideo::find($group_video_id);
			if(!empty($group_video)) {
				return view('pages.tracking-details', compact('user', 'group_video'));
			}
			else {
				return view('pages.no-video', compact('user'));
			}
		}
		else {
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
	public function text_analysis_details (Request $req) {
		$user= Auth::user();
		if($user->isAnInstructor()) {
			$video = oval\Video::find(intval($req->video_id));
			$analysis = json_decode($video->transcript->analysis, true);
			$group = $video->groups->first();
			$group_id = $group->id;
			$course_id = $group->course->id;
			
			return view('pages.text-analysis-details', compact('user', 'video', 'analysis', 'course_id', 'group_id'));
		}
		else {
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
	public function select_video(Request $req, $link_id, $group_video_id=NULL) {
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
			$current = !empty($group_video_id) ? $group_video_id : NULL;

			JavaScript::put([
				'link_id'=>$link_id,
				'current_group_video_id'=>$current
			]);
			return view('pages.instructor-video-selection', compact('user', 'group_videos', 'link_id'));
		}
		else {
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
	public function manage_analysis_requests (Request $req) {
		$user = Auth::user();
		if ($user->role == 'A') {
			$current_requests = oval\AnalysisRequest::where('status', '=', 'pending')
									->orderBy('created_at')
									->get()
									->unique('video_id');
			$rejected_requests = oval\AnalysisRequest::where('status', '=', 'rejected')
									->orderBy('created_at')
									->get()
									->unique('video_id');
			$processed_requests = oval\AnalysisRequest::where('status', '=', 'processed')
									->orWhere('status', '=', 'processing')
									->orderBy('created_at')
									->get()
									->unique('video_id');
			$google_creds = oval\GoogleCredential::all();
			return view('pages.admin-page', compact('user', 'current_requests', 'rejected_requests', 'processed_requests', 'google_creds'));
		}
		else {
			return view('pages.not-admin', compact('user')); 
		}
	} 

	public function batch_upload (Request $req) {
		$user = Auth::user();
		if ($user->role == 'A') {
			return view('pages.batch-upload');
		}
		else {
			return view('pages.not-admin', compact('user')); 
		}
	}

	/**
	 * Method called from GET route /manage_lti_connections
	 * 
	 * Show manage-lti page if the logged in user is admin,
	 * error page if not.
	 */
	public function manage_lti_connections (Request $req) {
		$user = Auth::user();
		if ($user->role == 'A') {
			$lti_connections = oval\LtiConsumer::all();
			return view('pages.manage-lti', compact('user', 'lti_connections'));
		}
		else {
			return view('pages.not-admin', compact('user')); 
		}
	}

}//end class

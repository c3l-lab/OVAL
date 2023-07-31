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
} //end class

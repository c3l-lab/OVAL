<?php

namespace oval\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use IMSGlobal\LTI\ToolProvider;
use IMSGlobal\LTI\ToolProvider\DataConnector;

use oval\User;
use oval\Course;
use oval\Enrollment;
use oval\Group;
use oval\GroupMember;
use oval\GroupVideo;
use oval\LtiCredential;

const LTI_PASSWORD = '[lti_password]';

class OvalLtiProvider extends ToolProvider\ToolProvider {
    function onLaunch() {
        Log::debug('Starting onLaunch method');

        try {
            $this->user->save();
            Log::debug('User details', ['user' => $this->user]);

            $user = User::where('email', '=', $this->user->email)->first();
            if (empty($user)) {
                $user = new User;
                $user->email = $this->user->email;
                $user->first_name = $this->user->firstname;
                $user->last_name = $this->user->lastname;
                $user->role = $this->getOvalUserRole();
                $user->password = bcrypt(LTI_PASSWORD);
                $user->save();
            }
            Auth::login($user);
            Log::debug('User logged in successfully');

            // The LMS database connection and related code have been removed.
            // You will need to find an alternative method to obtain or provide the course, enrollment, and group information.

        } catch (\Exception $e) {
            Log::error('Exception during onLaunch', [
                'exception' => $e, 
                'message' => $e->getMessage(), 
                'stack_trace' => $e->getTraceAsString()
            ]);
            $this->message->setError('Sorry, there was an error connecting you to the application.');
        }
        
    }

    function getOvalUserRole() {
        if ($this->user->isAdmin()) return 'A';
        return 'O';
    }

    function isInstructor() {
        if ($this->user->isAdmin()) return true;
        if ($this->user->isStaff()) return true;
        return false;
    }

    function onError() {
        Log::error('Error in LTI handling', ['error_message' => $this->message]);
    }
}

class LtiController extends Controller {
    public function launch(Request $req) {
        global $_POST;
        $_POST = $req->all();

        Log::debug('LTI launch request data', ['request_data' => $_POST]);

        $tool = new OvalLtiProvider(null);
        $tool->setParameterConstraint('oauth_consumer_key', TRUE, 50, array('basic-lti-launch-request', 'ContentItemSelectionRequest', 'DashboardRequest'));
        $tool->setParameterConstraint('resource_link_id', TRUE, 50, array('basic-lti-launch-request'));
        $tool->setParameterConstraint('user_id', TRUE, 50, array('basic-lti-launch-request'));
        $tool->setParameterConstraint('roles', TRUE, NULL, array('basic-lti-launch-request'));
        Log::debug('Before handleRequest');
        try {
            $tool->handleRequest();
        } catch (\Exception $e) {
            Log::error('Exception during handleRequest', ['exception' => $e]);
        }
        Log::debug('After handleRequest');

        $lti_user = Auth::user();
        Log::debug('Authenticated user', ['lti_user' => $lti_user]);

        $link_id = $req->resource_link_id;
        $group_video = GroupVideo::where([
                            ['moodle_resource_id', '=', $link_id],
                            ['status', '=', 'current']
                        ])->first();
        Log::debug('Group video query result', ['group_video' => $group_video]);

        $course = Course::where('moodle_course_id', '=', intval($req->context_id))->first();
        Log::debug('Course query result', ['course' => $course]);

        if(empty($course)) {
            return view('pages.message-page', ['title'=>'ERROR', 'message'=>'Oops, something is wrong. Please try again later.']);
        }

        if($lti_user->isInstructorOf($course)){
            Log::debug('User is an instructor. Redirecting to select-video page.');
            return redirect()->secure('/view/');
        }
        elseif(!empty($group_video)) {
            Log::debug('User is a student. Redirecting to view group_video page.');
            return redirect()->secure('/view/');
        }
        else {
            Log::debug('User is a student. Redirecting to course page.');
            return redirect()->secure('/view/');
        }
    }
}


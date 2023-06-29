<?php

namespace oval\Http\Controllers\Api\Lti1p1;

use Illuminate\Http\Request;
use oval\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


use oval\GroupVideo;

use oval\Services\Lti1p1\LtiProvider;
use oval\Services\Lti1p1\DataConnector;

class ToolController extends Controller
{
    public function launch(Request $req)
    {
        global $_POST;
        $_POST = $req->all();
        /**
         * oauth_consumer_key is used to identify the LMS in a multi-tenant environment
         * since we are not using a multi-tenant environment, we can hardcode the value
         **/
        // $_POST['oauth_consumer_key'] = 'lift.c3l.ai';

        Log::debug('LTI launch request data', ['request_data' => $req->all()]);

        Log::debug('Attempting to instantiate OvalLtiProvider');
        $data_connector = new DataConnector($req);
        $tool = new LtiProvider($data_connector);
        Log::debug('OvalLtiProvider instantiated');
        $tool->setParameterConstraint('oauth_consumer_key', TRUE, 50, array('basic-lti-launch-request', 'ContentItemSelectionRequest', 'DashboardRequest'));
        $tool->setParameterConstraint('resource_link_id', TRUE, 50, array('basic-lti-launch-request'));
        $tool->setParameterConstraint('user_id', TRUE, 50, array('basic-lti-launch-request'));
        $tool->setParameterConstraint('roles', TRUE, NULL, array('basic-lti-launch-request'));
        Log::debug('Before handleRequest');
        Log::debug('debug mode', [$tool->getDebugMode() ? 'true' : 'false']);
        try {
            $tool->handleRequest();
        } catch (\Exception $e) {
            Log::error('Exception during handleRequest', ['exception' => $e]);
        }
        Log::debug('After handleRequest');
        // $tool->onLaunch();

        $lti_user = Auth::user();
        Log::debug('Authenticated user', ['lti_user' => $lti_user]);

        $resourceId = $req->query('resource_id');
        $group_video = GroupVideo::where([
            ['id', '=', $resourceId],
            ['status', '=', 'current']
        ])->first();
        Log::debug('Group video query result', ['group_video' => $group_video]);

        $course = $group_video->course();
        Log::debug('Course query result', ['course' => $course]);

        if (empty($course)) {
            return view('pages.message-page', ['title' => 'ERROR', 'message' => 'Oops, something is wrong. Please try again later.']);
        }

        // if the request from studio, the user_id would be 'student'
        if ($req->query('user_id') === 'student') {
            return redirect()->route('view', ['group_video_id' => $group_video->id]);
        } else {
            return redirect()->route('group_videos.show.embed', ['id' => $group_video->id]);
        }
    }
}

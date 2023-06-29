<?php

namespace oval\Http\Controllers\Api\Lti1p1;

use Illuminate\Http\Request;
use oval\Course;
use oval\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


use oval\GroupVideo;

use oval\Services\Lti1p1\LtiProvider;
use oval\Services\Lti1p1\DataConnector;
use oval\Services\LtiLaunchService;

class ToolController extends Controller
{
    public function launch(Request $req)
    {
        global $_POST;
        $_POST = $req->all();

        /**
         * oat-sa/imsglobal-lti requires these variables to verify the request
         * from LTI consumer, but I don't know why these variables are either
         * not set or incorrect on server, so I set them exipitly.
         */
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['SERVER_PORT'] = '';
        $_SERVER['SERVER_NAME'] = $req->getHost();

        Log::debug('LTI launch request data', ['request_data' => $req->all()]);

        $data_connector = new DataConnector($req);
        $tool = new LtiProvider($data_connector);
        Log::debug('OvalLtiProvider instantiated');
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

        $ltiLaunchService = new LtiLaunchService();
        $ltiLaunchService->updateOrCreateCourse($req->input('context_id'), $req->input('context_title'));
        $ltiLaunchService->enrolUser($lti_user, $tool->isInstructor());

        $resourceId = $req->query('resource_id');

        if (empty($resourceId)) {
            return redirect()->route('video_management');
        }

        $group_video = GroupVideo::where([
            ['id', '=', $resourceId],
            ['status', '=', 'current']
        ])->firstOrFail();

        $ltiLaunchService->addToGroup($lti_user, $group_video->group());

        // if the request from studio, the user_id would be 'student'
        if ($req->input('user_id') === 'student') {
            return redirect()->route('view', ['group_video_id' => $group_video->id]);
        } else {
            return redirect()->route('group_videos.show.embed', ['id' => $group_video->id]);
        }
    }
}

<?php

namespace oval\Http\Controllers\Api\Lti1p1;

use Illuminate\Http\Request;
use oval\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;


use oval\Models\GroupVideo;

use oval\Services\Lti1p1\LtiMessage;
use oval\Services\Lti1p1\LtiProvider;
use oval\Services\LtiLaunchService;

class ToolController extends Controller
{
    public function launch(Request $req)
    {
        Log::debug('LTI launch request data', ['request_data' => $req->all()]);

        $tool = new LtiProvider($req);
        $tool->handleRequest();

        $ltiMessage = new LtiMessage($req->all(), $tool->user);
        $ltiLaunchService = new LtiLaunchService($ltiMessage);
        $ltiLaunchService->loginUser();
        $ltiLaunchService->updateOrCreateCourse();
        $ltiLaunchService->enrolUser();

        $resourceId = $req->query('resource_id');

        if (empty($resourceId)) {
            return redirect()->route('group_videos.index');
        }

        $group_video = GroupVideo::where([
            ['id', '=', $resourceId],
            ['status', '=', 'current']
        ])->firstOrFail();

        if (!empty($group_video->group())) {
            $ltiLaunchService->addUserToGroup($group_video->group());
        }

        // if the request from studio, the user_id would be 'student'
        return redirect()->route('group_videos.show.embed', ['id' => $group_video->id]);
    }
}

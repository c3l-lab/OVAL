<?php

namespace oval\Http\Controllers\Api\Lti1p3;

use Illuminate\Http\Request;
use oval\Models\GroupVideo;
use oval\Http\Controllers\Controller;
use oval\Models\LtiRegistration;
use oval\Services\Lti1p3\LtiMessage;
use oval\Services\Lti1p3\LtiService;
use oval\Services\LtiLaunchService;
use Packback\Lti1p3\LtiConstants;

class ToolController extends Controller
{
    private LtiService $ltiService;
    public function __construct()
    {
        $this->ltiService = app(LtiService::class);
    }

    public function jwks()
    {
        return response()->json($this->ltiService->jwks());
    }

    public function login(Request $request)
    {
        \Log::debug($request->all());
        $redirectUrl = $this->ltiService->login($request, $request->input('target_link_uri'));
        return redirect($redirectUrl);
    }

    public function launch(Request $request)
    {
        \Log::debug($request->all());
        $launch = $this->ltiService->validateLaunch($request);
        $launch_data = $launch->getLaunchData();
        $ltiMessage = new LtiMessage($launch_data);
        \Log::debug($launch_data);

        $ltiLaunchService = new LtiLaunchService($ltiMessage);

        $ltiLaunchService->loginUser();

        if ($ltiMessage->getCourseId() && \Auth::user()) {
            $ltiLaunchService->updateOrCreateCourse();
            $ltiLaunchService->enrolUser();
        }

        $resourceId = $request->query("resource_id");

        if (empty($resourceId)) {
            return redirect()->route('group_videos.index');
        }

        $group_video = GroupVideo::findOrFail($resourceId);

        if (!empty($group_video->group) && \Auth::user()) {
            $ltiLaunchService->addUserToGroup($group_video->group);
        }

        if ($launch->isDeepLinkLaunch()) {
            return redirect()->route('view', ['group_video_id' => $group_video->id]);
        } else {
            return redirect()->route('group_videos.show.embed', ['id' => $group_video->id]);
        }
    }
}

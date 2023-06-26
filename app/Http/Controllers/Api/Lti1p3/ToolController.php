<?php

namespace oval\Http\Controllers\Api\Lti1p3;

use Illuminate\Http\Request;
use oval\GroupVideo;
use oval\Http\Controllers\Controller;
use oval\LtiRegistration;
use oval\Services\Lti1p3\LtiService;

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
        $redirectUrl = $this->ltiService->login($request);
        return redirect($redirectUrl);
    }

    public function launch(Request $request)
    {
        \Log::debug($request->all());
        $launch = $this->ltiService->validateLaunch($request);
        $launch_data = $launch->getLaunchData();
        \Log::debug($launch_data);

        $group_video = GroupVideo::findOrFail(intval($request->query("resource_id")));

        if ($launch->isDeepLinkLaunch()) {
            return redirect()->route('view', ['group_video_id' => $group_video->id]);
        } else {
            if (\Auth::user() == null) {
                $this->ltiService->loginUser($launch_data);
            }
            return redirect()->route('group_videos.show.embed', ['id' => $group_video->id]);
        }
    }
}

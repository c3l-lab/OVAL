<?php

namespace oval\Http\Controllers\Api\Lti;

use Illuminate\Http\Request;
use oval\GroupVideo;
use oval\Http\Controllers\Controller;
use oval\LtiRegistration;
use oval\Services\LtiService;

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
        $issuer = $launch_data['iss'];
        $client_id = $launch_data['azp'];


        $registration = LtiRegistration::where('issuer', $issuer)
            ->where('client_id', $client_id)
            ->firstOrFail();

        $group_video = GroupVideo::where('lti_registration_id', $registration->id)
            ->firstOrFail();

        if ($launch->isDeepLinkLaunch()) {
            return redirect()->route('view', ['group_video_id' => $group_video->id]);
        } else {
            $this->ltiService->loginUser($launch_data);
            return redirect()->route('group_videos.show.embed', ['id' => $group_video->id]);
        }
    }
}

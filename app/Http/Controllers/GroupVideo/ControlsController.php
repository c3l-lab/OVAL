<?php

namespace oval\Http\Controllers\GroupVideo;

use Illuminate\Http\Request;
use oval\Http\Controllers\Controller;
use oval\Models\GroupVideo;

class ControlsController extends Controller
{
    public function edit(GroupVideo $groupVideo)
    {
        return view('group_videos.controls.edit', [
          'groupVideo' => $groupVideo,
        ]);
    }

    public function update(Request $request, GroupVideo $groupVideo)
    {
        $groupVideo->controls = $request->controls;
        $groupVideo->save();
        return response()->json(['success' => true]);
    }
}

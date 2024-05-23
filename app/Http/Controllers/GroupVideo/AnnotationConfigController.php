<?php

namespace oval\Http\Controllers\GroupVideo;

use Illuminate\Http\Request;
use oval\Http\Controllers\Controller;
use oval\Models\GroupVideo;

class AnnotationConfigController extends Controller
{
    public function edit(GroupVideo $groupVideo)
    {
        return view('group_videos.annotation_config.edit', [
          'groupVideo' => $groupVideo,
        ]);
    }

    public function update(Request $request, GroupVideo $groupVideo)
    {
        $groupVideo->show_annotations = $request->annotation_config['show_annotations'];
        $annotationConfig =  $request->annotation_config;
        unset($annotationConfig['show_annotations']);
        $groupVideo->annotation_config = $annotationConfig;
        $groupVideo->save();
        return response()->json(['success' => true]);
    }
}

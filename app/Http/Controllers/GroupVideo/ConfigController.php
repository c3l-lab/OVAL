<?php

namespace oval\Http\Controllers\GroupVideo;

use Illuminate\Http\Request;
use oval\Http\Controllers\Controller;
use oval\Models\GroupVideo;

class ConfigController extends Controller
{

    public function configAnnotation(Request $request, int $groupVideoId)
    {
        $annotationConfig = $request->annotation_config;
        $updateData = [];
    
        if (isset($annotationConfig['show_annotations'])) {
            $updateData['show_annotations'] = $annotationConfig['show_annotations'];
            unset($annotationConfig['show_annotations']);
        }
    
        $currentConfig = \DB::table('group_videos')
                             ->where('id', $groupVideoId)
                             ->value('annotation_config');

        $mergedAnnotationConfig = array_merge(json_decode($currentConfig, true), $annotationConfig);
        
        if (isset($mergedAnnotationConfig['structured_annotations'])) {
            unset($annotationConfig['structured_annotations']);
        }
    
        $updateData['annotation_config'] = json_encode($mergedAnnotationConfig);
    
        \DB::table('group_videos')
            ->where('id', $groupVideoId)
            ->update($updateData);
    
        return response()->json(['success' => true]);
    }

    public function toggleEyeTracking(Request $request, int $groupVideoId) {
        $currentConfig = \DB::table('group_videos')
                             ->where('id', $groupVideoId)
                             ->value('enable_eye_tracking');    

        \DB::table('group_videos')
            ->where('id', $groupVideoId)
            ->update(['enable_eye_tracking' => $currentConfig == 1 ? 0 : 1]);
    
        return response()->json(['success' => true]);
    }
}

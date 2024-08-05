<?php

namespace oval\Http\Controllers;

use Illuminate\Http\Request;
use oval\Models\SessionInformation;

class SessionInformationController extends Controller
{
    /**
     * Store session information.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|string',
            'os' => 'nullable|string',
            'browser' => 'nullable|string',
            'doc_width' => 'nullable|integer',
            'doc_height' => 'nullable|integer',
            'layout' => 'nullable|string',
            'init_screen_width' => 'nullable|integer',
            'init_screen_height' => 'nullable|integer',
            'group_video_id' => 'required|integer',
        ]);
    
        $existingRecord = SessionInformation::find($validatedData['id']);
    
        if (!$existingRecord) {
            SessionInformation::create($validatedData);
        }
    
        $this->track($validatedData['group_video_id'], [
            "event" => "View",
            "event_time" => date("Y-m-d H:i:s"),
            "session_id" => $validatedData['id']
        ]);
    
        return response()->json(['message' => 'OK'], 200);
    }
}

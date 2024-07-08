<?php

namespace oval\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use oval\Models\Tracking;

class Controller extends BaseController
{
    protected function track(int $group_video_id, $record)
    {
        $tracking = new Tracking();
        $tracking->group_video_id = intval($group_video_id);
        $tracking->user_id = \Auth::user()->id;
        $tracking->event = data_get($record, 'event', null);
        $tracking->target = data_get($record, 'target', null);
        $tracking->info = data_get($record, 'info', null);
        $tracking->ref_id = data_get($record, 'ref_id', null);
        $tracking->ref_type = data_get($record, 'ref_type', null);
        $tracking->event_time = data_get($record, 'event_time', null);
        $tracking->video_time = data_get($record, 'video_time', null);
        $tracking->session_id = \Session::get('v-session-id');    
        $tracking->save();
    }

    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;
}

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
        $tracking->event = $record['event'];
        $tracking->target = $record['target'];
        $tracking->info = $record['info'];
        $tracking->ref_id = $record['ref_id'];
        $tracking->ref_type = $record['ref_type'];
        $tracking->event_time = $record['event_time'];
        $tracking->save();
    }

    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;
}

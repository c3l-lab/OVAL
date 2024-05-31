<?php

namespace oval\Http\Controllers;

use Illuminate\Http\Request;
use oval\Models\Tracking;

class TrackingController extends Controller
{
    public function store(Request $req)
    {
        $records = $req->data;
        foreach ($records as $record) {
            $tracking = new Tracking();
            $tracking->group_video_id = intval($req->group_video_id);
            $tracking->user_id = \Auth::user()->id;
            $tracking->event = $record['event'];
            $tracking->target = $record['target'];
            $tracking->info = $record['info'];
            $tracking->video_time = data_get($record, 'video_time', null);
            $tracking->event_time = date("Y-m-d H:i:s", (int)($record['event_time'] / 1000));
            $result = $tracking->save();
        }
    }


    public function export(Request $request)
    {
        $fileName = "trackings.csv";

        $trackings = Tracking::with('user')->where([
            'group_video_id' => $request->group_video_id
        ])->get();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Video ID', 'User Email', 'Event', 'Target', 'Info', 'Event Time');

        $callback = function () use ($trackings, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($trackings as $tracking) {
                $row['Video ID']  = $tracking->group_video_id;
                $row['User Email'] = isset($tracking->user) ? $tracking->user->email : '';
                $row['Event']    = $tracking->event;
                $row['Target']  = $tracking->target;
                $row['Info']  = $tracking->info;
                $row['Event Time']  = $tracking->event_time;

                fputcsv($file, array(
                    $row['Video ID'],
                    $row['User Email'],
                    $row['Event'],
                    $row['Target'],
                    $row['Info'],
                    $row['Event Time']
                ));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

<?php

namespace oval\Http\Controllers;

use Illuminate\Http\Request;
use oval\Models\Tracking;
use oval\Services\InfluxDBService;

class TrackingController extends Controller
{
    protected $influxDBService;

    public function __construct(InfluxDBService $influxDBService)
    {
        $this->influxDBService = $influxDBService;
    }

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
            $tracking->ref_id = data_get($record, 'ref_id', null);
            $tracking->ref_type = data_get($record, 'ref_type', null);
            $tracking->event_time = date("Y-m-d H:i:s", (int)($record['event_time'] / 1000));
            $tracking->session_id = \Session::get('v-session-id'); 
            $tracking->save();
        }
    }

    public function eyeTrackingStore(Request $req) 
    {
        $records = $req->data;
        if (!is_array($records) || empty($records)) {
            return;
        }
        $points = [];
        foreach ($records as $record) {
            $id = \Session::get('v-session-id'); 
            $el = $record['el'];
            unset($record['el']); 
            $timestamp = $record['timestamp'];
            unset($record['timestamp']);

            $point = $this->influxDBService->createRecordWithTimestamp($timestamp, $record, $id ? ['id' => $id, 'target' => $el] : ['target' => $el]);   
            array_push($points, $point);
        }

        $this->influxDBService->insertRecords($points);
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

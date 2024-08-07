<?php

namespace oval\Http\Controllers;

use Illuminate\Http\Request;
use oval\Models\Tracking;
use oval\Services\InfluxDBService;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
            $tags = [
                'id' => \Session::get('v-session-id'),
                'target' => $record['target'],
                'gv_id' => $record['gv_id'],
            ];
            $timestamp = $record['timestamp'];
            
            if (!$timestamp) {
                $timestamp = now()->timestamp * 1000;
            }

            unset($record['target']);
            unset($record['gv_id']);
            unset($record['timestamp']);

            $point = $this->influxDBService->createRecordWithTimestamp($timestamp, $record, $tags);   
            array_push($points, $point);
        }

        $this->influxDBService->insertRecords($points);
    }

    public function eyeTrackingDownload(Request $req) {
        $duration = $req->query('duration', null);
        $gv_id = $req->query('gv_id', null);
        $exportFileName = '';
        if($duration) {
            $to = new \DateTime();
            $from = new \DateTime();
            $from->modify("-{$this->mapDuration($duration)}");
            $exportFileName .= "({$from->format('Y.m.d-H\hi\'s\'\'')} to {$to->format('Y.m.d-H\hi\'s\'\'')})";
        } else {
            $exportFileName .= '(ALL)';
        }

        if($gv_id) {
            $exportFileName .= "(gv_id={$gv_id})";
        } else {
            $exportFileName .= "(gv_id=ALL)";
        }

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$exportFileName}eye-tracking.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($duration, $gv_id) {
            $handle = fopen('php://output', 'w');
    
            $headers = ['id', 'gv_id', 'target', 'timestamp', 'x', 'y'];
            fputcsv($handle, $headers);

            $parser = $this->influxDBService->queryStream(
                duration: $duration,
                gv_id: $gv_id
            );

            foreach ($parser->each() as $record)
            {  
                $row = array_map(
                    function($n) use ($record) 
                    {
                        if ($n === 'timestamp') {
                            return data_get($record->values, '_time', null);
                        }

                        return data_get($record->values, $n, null); 
                    },
                    $headers
                );

                fputcsv($handle, $row);
            }
    
            fclose($handle);
        };
    
        return new StreamedResponse($callback, 200, $headers);
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

        $columns = array("Video ID", "User Email", "Event", "Target", "Video Time", "Info", "Event Time", "Ref ID", "Ref Type", "Session ID");

        $callback = function () use ($trackings, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($trackings as $tracking) {
                $row['Video ID']  = $tracking->group_video_id;
                $row['User Email'] = isset($tracking->user) ? $tracking->user->email : '';
                $row['Event']    = $tracking->event;
                $row['Target']  = $tracking->target;
                $row['Video Time'] = $tracking->video_time;
                $row['Info']  = $tracking->info;
                $row['Event Time']  = $tracking->event_time;
                $row['Ref ID']  = $tracking->ref_id;
                $row['Ref Type']  = $tracking->ref_type;
                $row['Session ID']  = $tracking->session_id;

                fputcsv($file, array(
                    $row['Video ID'],
                    $row['User Email'],
                    $row['Event'],
                    $row['Target'],
                    $row['Video Time'],
                    $row['Info'],
                    $row['Event Time'],
                    $row['Ref ID'],
                    $row['Ref Type'],
                    $row['Session ID'],
                ));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function mapDuration(string $duration): string {
        $mapping = [
            'h' => 'hour',
            'd' => 'day',
            'w' => 'week',
            'm' => 'minute',
            'y' => 'year'
        ];
    
        $unit = substr($duration, -1);
        $amount = substr($duration, 0, -1);
    
        if (isset($mapping[$unit])) {
            return $amount . ' ' . $mapping[$unit];
        } else {
            throw new \InvalidArgumentException("Invalid time unit in modifier: $unit");
        }
    }
}

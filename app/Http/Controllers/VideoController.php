<?php

namespace oval\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp;
use oval\Classes\YoutubeDataHelper;
use oval\Jobs\AnalyzeTranscript;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $req)
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $client = new GuzzleHttp\Client();
        $url = 'https://www.googleapis.com/youtube/v3/videos?part=snippet%2CcontentDetails&id=' . $request->video_id . '&key=' . config('youtube.api_key');
        try {
            $response = $client->get($url);
        } catch (GuzzleHttp\Exception\ClientException $e) {
            return ['error' => "Unable to get video info from youtube."];
        }

        $result = json_decode($response->getBody());

        $v = new \oval\Models\Video([
            'identifier' => $request->video_id,
            'media_type' => $request->media_type
        ]);

        $v->title = $result->items[0]->snippet->title;
        $desc = $result->items[0]->snippet->description;
        $v->description = strlen($desc) > 507 ? substr($desc, 0, 510) : $desc;
        $v->thumbnail_url = "https://img.youtube.com/vi/" . $request->video_id . "/1.jpg";
        $v->duration = ISO8601ToSeconds($result->items[0]->contentDetails->duration);

        $v->added_by = \Auth::user()->id;
        $v->save();

        $course_id = intval($request->course_id);
        if (!empty($course_id)) {
            $group = \oval\Models\Course::find($course_id)
                ->defaultGroup();
            $v->assignToGroup($group);
        }

        if ($v->keywords->count() == 0 && $v->media_type == "youtube") {
            $ar = new \oval\Models\AnalysisRequest();
            $ar->video_id = $v->id;
            $ar->user_id = $v->added_by;
            $ar->save();
            $this->process_youtube_text_analysis($ar);
        }

        return ['course_id' => $course_id, 'video_id' => $v->id];
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $result = \oval\Models\Video::destroy($id);
        return ['result' => $result];
    }

    /**
     * Private method to process Youtube video's text analysis
     * for AnalysisRequest object passed in as parameter.
     * TODO: move this somewhere ... This was copied from another controller.
     *
     * @param \oval\Models\AnalysisRequest $analysis_request
     * @return void
     */
    private function process_youtube_text_analysis(\oval\Models\AnalysisRequest $analysis_request)
    {
        //--exit if this video already as result--
        $requests = $analysis_request->requestsForSameVideo();
        foreach ($requests as $r) {
            if ($r->status == "processed") {
                return;
            }
        }

        $video = $analysis_request->video;

        // Change status to 'processing'
        \oval\Models\AnalysisRequest::where(array('video_id' => $video->id))->update(['status' => 'processing']);

        $user_ids = $analysis_request->requestorsIds();
        array_push($user_ids, Auth::user()->id);

        $caption_text = $this->download_youtube_caption($video);
        $text = "";
        $video = $video->fresh();

        if (!empty($caption_text)) {
            $text = $caption_text;
        } elseif (!empty($video->transcript)) {
            $transcript_json = json_decode($video->transcript->transcript);
            foreach ($transcript_json as $t) {
                $obj = json_decode($t);
                $text .= $obj->transcript . " ";
            }
        } else {
            // Change status to 'processed'
            \oval\Models\AnalysisRequest::where(array('video_id' => $video->id))->update(['status' => 'processed']);
            return "no transcript";
        }

        // Send analyse transcript job to queue
        $this->dispatch(new AnalyzeTranscript([
            'videoId' => $video->id,
            'transcript' => $text,
            'userIds' => $user_ids
        ]));
    }

    private function download_youtube_caption(\oval\Models\Video $video)
    {
        $text = "";
        $transcript = $video->transcript;
        if (empty($transcript)) {
            $transcript = new \oval\Models\Transcript();
            $transcript->video_id = $video->id;
        }

        $langs = config('youtube.transcript_lang');
        $credentials = \oval\Models\GoogleCredential::all();
        $track_id = null;
        $caption_array = null;
        if (!empty($credentials) && count($credentials) > 0) {
            foreach ($credentials as $cred) {
                $helper = new YoutubeDataHelper($cred->client_id, $cred->client_secret);
                $helper->handle_access_token_refresh($cred);

                $track_id = $helper->get_caption_track_id($video->identifier);
                if (!empty($track_id)) {
                    $caption_array = $helper->download_caption($track_id);
                }
                if (!empty($caption_array)) {
                    break;
                }
            }
        }
        if (empty($caption_array)) {
            $response = "";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //curl_setopt($ch, CURLOPT_PROXY, 'proxy.example.com:8080');
            foreach ($langs as $l) {
                curl_setopt($ch, CURLOPT_URL, 'http://video.google.com/timedtext?lang=' . $l . '&v=' . $video->identifier);
                $response = curl_exec($ch);
                if (!empty($response)) {
                    $cc = simplexml_load_string($response);
                    $caption_array = [];
                    $text = "";
                    foreach ($cc->text as $item) {
                        $line = "{";
                        $time = 0;
                        foreach ($item->attributes() as $key => $val) {
                            if ($key == "start") {
                                $time = floatval($val);
                                $line .= '"start":' . $time . ', ';
                            } elseif ($key == "dur") {
                                $time += floatval($val);
                                $line .= '"end":' . $time . ', ';
                                $time = 0;
                            }
                        }
                        $text .= $item;
                        $line .= '"transcript":"' . $item . '"}';
                        $caption_array[] = $line;
                    }
                    break;
                }
            }
        }
        if (!empty($caption_array)) {
            $transcript->transcript = json_encode($caption_array);
            $transcript->save();
        }
        return $text;
    }
}

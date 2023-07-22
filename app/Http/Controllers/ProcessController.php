<?php

namespace oval\Http\Controllers;

use Illuminate\Http\Request;
use oval;
use DB;
use oval\Classes\YoutubeDataHelper;
use Illuminate\Support\Facades\Auth;
use oval\Models\AnalysisRequest;
use oval\Jobs\AnalyzeTranscript;
use Illuminate\Support\Facades\Storage;

use IMSGlobal\LTI\ToolProvider;
use IMSGlobal\LTI\ToolProvider\DataConnector;

/**
 * This class handles POST requests from php forms.
 */
class ProcessController extends Controller
{
	/** @var string $please_wait Message to show when API request for text analysis is sent successfully.  */
	private $please_wait = 'Request has been sent. Data processing can take some time. Please check back later...';

	/** @var string $no_transcript Error message to show when there is no captions available. */
	private $no_transcript = 'The YouTube video you requested analysis doesn\'t have transcript available for us to use.';

	/**
	 * TODO: Move this to helper class - this is copied from AjaxController
	 */
	private function ISO8601ToSeconds($ISO8601) {
		preg_match('/\d{1,2}[H]/', $ISO8601, $hours);
		preg_match('/\d{1,2}[M]/', $ISO8601, $minutes);
		preg_match('/\d{1,2}[S]/', $ISO8601, $seconds);

		$duration = [
			'hours'   => $hours ? $hours[0] : 0,
			'minutes' => $minutes ? $minutes[0] : 0,
			'seconds' => $seconds ? $seconds[0] : 0,
		];

		$hours   = intval(substr($duration['hours'], 0, -1));
		$minutes = intval(substr($duration['minutes'], 0, -1));
		$seconds = intval(substr($duration['seconds'], 0, -1));

		$toltalSeconds = ($hours * 60 * 60) + ($minutes * 60) + $seconds;

		return $toltalSeconds;
	}

	/**
	 * Private method to download caption for Youtube video.
	 *
	 * Uses Google credentials stored in database if any exist, to call YoutubeData API to get caption.
	 * If no caption can be obtained, check if there are public caption for the video.
	 * Method then saves transcript and returns the text.
	 *
	 * @param Video $video Video oject with media_type='youtube'
	 * @return string String containing transcript text.
	 */
	private function download_youtube_caption ($video) {
		$text = "";
		$transcript = $video->transcript;
        if (empty($transcript)) {
            $transcript = new oval\Models\Transcript;
            $transcript->video_id = $video->id;
        }

        $langs = config('youtube.transcript_lang');
		$credentials = oval\Models\GoogleCredential::all();
		$track_id = null;
        $caption_array = null;
        if (!empty($credentials) && count($credentials)>0) {
            foreach ($credentials as $cred) {
                $helper = new YoutubeDataHelper($cred->client_id, $cred->client_secret);
                $helper->handle_access_token_refresh($cred);

				$track_id = $helper->get_caption_track_id($video->identifier);
				if(!empty($track_id)) {
					$caption_array = $helper->download_caption($track_id);
				}
                if (!empty($caption_array)) {
					break;
                }
            }
        }
        if (empty($caption_array)) {
            $response = "";
            $proxy_url = env('CURL_PROXY_URL', '');
            $proxy_user = env('CURL_PROXY_USER', '');
            $proxy_pass = env('CURL_PROXY_PASS', '');
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            if (!empty($proxy_url)) {
                curl_setopt($ch, CURLOPT_PROXY, $proxy_url);
            }
            if (!empty($proxy_user)) {
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy_user . ':' . $proxy_pass);
            }
            foreach ($langs as $l) {
                curl_setopt($ch, CURLOPT_URL, 'http://video.google.com/timedtext?lang='.$l.'&v='.$video->identifier);
				$response = curl_exec($ch);
                if (!empty($response)) {
                    $cc = simplexml_load_string($response);
					$caption_array = [];
					$text = "";
                    foreach ($cc->text as $item) {
                        $line = "{";
                        $time = 0;
                        foreach ($item->attributes() as $key=>$val) {
                            if ($key == "start") {
                                $time = floatval($val);
                                $line .= '"start":'.$time.', ';
                            }
                            elseif ($key == "dur") {
                                $time += floatval($val);
                                $line .= '"end":'.$time.', ';
                                $time = 0;
                            }
						}
						$text .= $item;
                        $line .= '"transcript":"'.$item.'"}';
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

	/**
	 * Private method to process text analysis of YouTube video.
	 *
	 * Get the caption for the video, then dispatch analysis job to queue
	 * and returns message letting user know it is being processed.
	 * If no transcript is available, returns eerror message.
	 * @uses download_youtube_caption()
	 * @param Video $video Video object with media_type='youtube'
	 * @param array $user_ids Array of int containing ids of users who requested for this analysis
	 * @return string message to display
	 */
	private function process_youtube_analysis ($video, $user_ids) {
		$caption_text = $this->download_youtube_caption($video);
		$text = "";
		$video = $video->fresh();

		if(!empty($caption_text)) {
			$text = $caption_text;
		}
		elseif(!empty($video->transcript)) {
			$transcript_json = json_decode($video->transcript->transcript);
			foreach ($transcript_json as $t) {
				$obj = json_decode($t);
				$text .= $obj->transcript." ";
			}
		}
		else {
			// Change status to 'processed'
			AnalysisRequest::where(array('video_id' => $video->id))->update(['status' => 'processed']);

			return $this->no_transcript;
		}

		// Send analyse transcript job to queue
		$this->dispatch(new AnalyzeTranscript([
			'videoId'    => $video->id,
			'transcript' => $text,
			'userIds'    => $user_ids
		]));

		return  $this->please_wait;
	}

	/**
	 * Method called from route /request_text_analysis
	 * (php form on /manage-analysis-request page)
	 *
	 * This method sets the status of the AnalysisRequest to "processing",
	 * fetches ids of user who requested it,
	 * then calls private method to process the request depending on media_type of the video,
	 * which returns message to display letting user know what is happening,
	 * then redirect back to /manage-analysis-requests page showing the message.
	 *
	 * @param Request $req Request contains analysis_request_id
	 * @return Illuminate\Http\RedirectResponse
	 */
	public function request_text_analysis(Request $req) {
		$analysis_request = oval\Models\AnalysisRequest::find(intval($req->analysis_request_id));
		$video = $analysis_request->video;

		// Change status to 'processing'
		AnalysisRequest::where(array('video_id' => $video->id))->update(['status' => 'processing']);

		$user_ids = $analysis_request->requestorsIds();
		array_push($user_ids, Auth::user()->id);
		$msg = $this->process_youtube_analysis($video, $user_ids);
    return back()->with('msg', $msg);
	}

	/**
	 * Method called from route /reject_text_analysis_request
	 * (php form on /manage-analysis-request page)
	 *
	 * Updates status of AnalysisRequest for the video whose ID passed in
	 * to "rejected", then redirects back to /manage-analysis-request page.
	 *
	 * @param Request $req Request contains video_id
	 * @return Illuminate\Http\RedirectResponse
	 */
	public function reject_text_analysis_request (Request $req) {
		$video_id = intval($req->video_id);
		oval\Models\AnalysisRequest::where('video_id', $video_id)
			->update(['status'=>'rejected']);
		return back();
	}

	/**
	 * Method called from /recover_text_analysis_request route.
	 * (php form on /manage-analysis-request page)
	 *
	 * Updates status of AnalysisRequest for the video whose ID passed in
	 * to "pending", then redirects back to /manage-analysis-request page.
	 *
	 * @param Request $req Request contains video_id
	 * @return Illuminate\Http\RedirectResponse
	 */
	public function recover_text_analysis_request (Request $req) {
		$video_id = intval($req->video_id);
		oval\Models\AnalysisRequest::where('video_id', $video_id)
			->update(['status'=>'pending']);
		return back();
	}

	/**
	 * Method called from route /delete_text_analysis_request
	 * (php form on /manage-analysis-request page)
	 *
	 * Updates status of AnalysisRequest for the video whose ID passed in
	 * to "deleted", then redirects back to /manage-analysis-request page.
	 *
	 * @param Request $req Request contains video_id
	 * @return Illuminate\Http\RedirectResponse
	 */
	public function delete_text_analysis_request (Request $req) {
		$video_id = intval($req->video_id);
		oval\Models\AnalysisRequest::where('video_id', $video_id)
			->update(['status'=>'deleted']);
		return back();
	}

	/**
	 * Method called from route /send_all_text_analysis_requests
	 * (php form on /manage-analysis-request page that just has one button)
	 *
	 * Find videos that have AnanlysisRequests with status "pending",
	 * and call private method to process analysis request
	 * depending on media_type for each video,
	 * then redirect back to /manage-analysis-request page
	 *
	 * @param Request $req
	 * @return Illuminate\Http\RedirectResponse
	 */
	public function send_all_text_analysis_requests(Request $req) {
		$video_ids = oval\Models\AnalysisRequest::where('status', 'pending')
						->pluck('video_id')
						->toArray();
		$videos = oval\Models\Video::find($video_ids);
		foreach ($videos as $v) {
			$this->process_youtube_analysis($v->id);
		}
		return back();
	}

	/**
	 * Method called from route /reject_all_text_analysis_requests
	 * (php form on /manage-analysis-request page that just has one button)
	 *
	 * Change status of AnalysisRequests from "pending" to "rejected",
	 * then redirect back to /manage-analysis-request page.
	 *
	 * @param Request $req
	 * @return Illuminate\Http\RedirectResponse
	 */
	public function reject_all_text_analysis_requests(Request $req) {
		oval\Models\AnalysisRequest::where('status', 'pending')
			->update(['status'=>'rejected']);
		return back();
	}

	/**
	 * Method called from route /recover_all_rejected_text_analysis_requests
	 * (php form on /manage-analysis-request page that just has one button)
	 *
	 * Change status of AnalysisRequests from "rejected" to "pending",
	 * then redirect back to /manage-analysis-request page.
	 *
	 * @param Request $req
	 * @return Illuminate\Http\RedirectResponse
	 */
	public function recover_all_rejected_text_analysis_requests (Request $req) {
		oval\Models\AnalysisRequest::where('status', 'rejected')
			->update(['status'=>'pending']);
		return back();
	}

	/**
	 * Method called from route /delete_all_rejected_text_analysis_requests
	 * (php form on /manage-analysis-request page that just has one button)
	 *
	 * Change status of AnalysisRequests from "rejected" to "deleted",
	 * then redirect back to /manage-analysis-request page.
	 *
	 * @param Request $req
	 * @return Illuminate\Http\RedirectResponse
	 */
	public function delete_all_rejected_text_analysis_requests(Request $req) {
		oval\Models\AnalysisRequest::where('status', 'rejected')
			->update(['status'=>'deleted']);
		return back();
	}

	/**
	 * Private method to add Youtube Video by passing its identifier.
	 *
	 * This method was copied and adopted from AjaxController.
	 * TODO:: move it somewhere else
	 * Method calls YoutubeData API to get video metadata,
	 * and inserts a record in Videos table.
	 *
	 * @param string $identifier
	 * @return Video The video that was just added
	 */
	private function insert_youtube_video($identifier) {
		//TODO:: Move to helper class - this was copied (and adopted) from AjaxController
		$proxy_url = env('CURL_PROXY_URL', '');
		$proxy_user = env('CURL_PROXY_USER', '');
		$proxy_pass = env('CURL_PROXY_PASS', '');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/youtube/v3/videos?part=snippet%2CcontentDetails&id=' . $identifier . '&key=AIzaSyA3o2YPTeh2TCx3bQk_1zoJjliTw2pfeXo');
		if (!empty($proxy_url)) {
			curl_setopt($ch, CURLOPT_PROXY, $proxy_url);
		}
		if (!empty($proxy_user)) {
			curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy_user . ':' . $proxy_pass);
		}
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);

		if ($errno = curl_errno($ch))
		{
			$error_message = curl_strerror($errno);
			error_log('error ' . $errno . ': ' . $error_message);
			return ['error'=>$errno];
		}
		$result = json_decode($response);

		$v = new oval\Models\Video;
		$v->identifier = $identifier;
		$v->title = $result->items[0]->snippet->title;
		$desc = $result->items[0]->snippet->description;
		$v->description = strlen($desc)>507 ? substr($desc, 0, 510) : $desc;
		$v->thumbnail_url = "https://img.youtube.com/vi/".$identifier."/1.jpg";
		$v->duration = $this->ISO8601ToSeconds($result->items[0]->contentDetails->duration);
		$v->media_type = "youtube";
		$v->added_by = Auth::user()->id;
		$v->save();
		curl_close($ch);

		return $v;
	}

	/**
	 * Method called from /batch_data_insert to upload json file to insert transcripts data and
	 * send text analysis request.
	 *
	 * JSON being uploaded may have array containing transcript objects or video identifiers.
	 * If Transcripts are uploaded, save these then fire off text analysis jobs.
	 * If Identifiers are uploaded, get info for the video, save it then trigger text analysis.
	 * The method then redirects back to originating page with message to display.
	 *
	 * @uses insert_youtube_video()
	 * @uses AnalyzeTranscript
	 *
	 * @param Request $req Request contains file
	 * @return Illuminate\Http\RedirectResponse
	 */
	public function batch_data_insert (Request $req) {
		$file = $req->file;
		$path = $file->store('temp');
		$j = trim(Storage::get($path));
		$j = str_replace('&quot;', '"', $j);
		$json = json_decode($j);

		if (isset($json->transcripts)) {
			$transcripts = $json->transcripts;

			foreach ($transcripts as $entry) {
				//-- check for existane of video, if not in db, call data api to fetch data & insert
				$identifier = $entry->identifier;
				$v = oval\Models\Video::where('identifier', '=', $identifier)->first();
				if (empty($v)) {
					$v = $this->insert_youtube_video($identifier);
				}
				$t = oval\Models\Transcript::where('video_id', '=', $v->id)->first();
				if (empty($t)) {
					$t = new oval\Models\Transcript;
					$t->video_id = $v->id;
				}
				$t->transcript = json_encode($entry->transcript);
				$t->save();

				$text = '';
				$transcript_json = json_decode($t->transcript, true);
				foreach ($transcript_json as $tj) {
					$jsonstr = trim(preg_replace('/\s+/', ' ', $tj)); // preg_replace to remove \n character causing json parsing error
					$obj = json_decode($jsonstr);
					$text .= $obj->transcript." ";
				}

				// Send analyse transcript job to queue
				$this->dispatch(new AnalyzeTranscript([
					'videoId'    => $v->id,
					'transcript' => $text,
					'userIds'    => null
				]));
			}
			$msg = "Video(s) and/or Transcript(s) were inserted, and request(s) were sent for text analysis.";
			return back()->with('msg', $msg);
		}
		elseif (isset($json->identifiers)) {
			$identifiers = $json->identifiers;

			foreach ($identifiers as $i) {
				$v = oval\Models\Video::where('identifier', '=', $i)->first();
				if (empty($v)) {
					$v = $this->insert_youtube_video($i);
				}
				$this->process_youtube_analysis ($v, [Auth::user()->id]);
			}

			$msg = "Video(s) and/or Transcript(s) were inserted, and request(s) were sent for text analysis.";
			return back()->with('msg', $msg);
		}
	}

	/**
	 * Method called from route /add_lti_connection (manage-lti page)
	 *
	 * This method adds new LTI tool consumer with values passed in as parameter.
	 * It also adds LtiCredential if values are passed for this in parameter.
	 *
	 * @param Request $req Contains name, key, secret, db_type, host, db_name, user, password, prefix
	 * @return Illuminate\Http\RedirectResponse Redirect object with message
	 */
	public function add_lti_connection (Request $req) {
		try {
			$db_config = DB::getConfig();
			$conn_str = $db_config['driver'] . ':host=' . $db_config['host'] . ';port=' . $db_config['port'] . ';dbname=' . $db_config['database'];
			$pdo = new \PDO($conn_str, $db_config['username'], $db_config['password']);
		}
		catch (PDOException $e) {
			return 'Connection failed: ' . $e->getMessage();
		}
		$db_connector = DataConnector\DataConnector::getDataConnector('', $pdo);
		$consumer = new ToolProvider\ToolConsumer($req->key, $db_connector);
		$consumer->name = $req->name;
		$consumer->secret = $req->secret;
		$consumer->enabled = true;
		$consumer->save();

		$consumer = oval\Models\LtiConsumer::where('consumer_key256', '=', $req->key)->first();
		$success = false;

		if(!empty($req->db_type) && !empty($req->host) && !empty($req->db_name) && !empty($req->user) && !empty($req->pw)) {
			$cred = new oval\Models\LtiCredential;
			$cred->consumer_id = $consumer->consumer_pk;
			$cred->db_type = $req->db_type;
			$cred->host = $req->host;
			$cred->port = intval($req->port);
			$cred->database = $req->db_name;
			$cred->username = $req->user;
			$cred->password = $req->pw;
			$cred->prefix = $req->prefix;
			$success = $cred->save();
		}
		if($success){
			$msg = "Connection and database credential was saved.";
		}
		else {
			$msg = "LTI connection was saved but database credential was not saved.";
		}

		return back()->with(compact('msg'));
	}
}

<?php

namespace oval\Http\Controllers;

use Illuminate\Http\Request;
use oval\Classes\YoutubeDataHelper;
use Google_Client;
use Google_Service_YouTube;
use oval;
use Exception;

/**
 * This class handles requests relating to Youtube Data API
 * @uses app\Classes\YoutubeDataHelper
 */
class GoogleAPIController extends Controller
{
    /**
     * @var string $api_key Contains Google API key
     * TODO: move this to config file
     */
    private $api_key = "[google_api_key]";   //<-- needs to be moved somewhere


    /**
     * Method called from /add_google_cred web route
     *
     * This method redirects to Google's auth URL
     * @uses app/Classes/YoutubeDataHelper::get_auth_url() to get Google auth URL
     * @param Request $req Request contains client_id, secret
     * @return Illuminate\Http\RedirectResponse
     */
    public function add_google_cred (Request $req) {
		$client_id = $req->client_id;
		$secret = $req->secret;
		session(['cid' => $client_id]);
        session(['s' => $secret]);
        $helper = new YoutubeDataHelper($client_id, $secret);
		$authURL = $helper->get_auth_url();
		return redirect()->away($authURL);
	}

    /**
     * Method called from /youtube_auth_redirect path
     *
     * This is the return path after successful authentication at Google Auth page.
     * If successfully authenticated, redirects to /
     *
     * @uses oval\Classes\YoutubeDataHelper::handle_auth_redirect()
     * @param Request $req Request contains code, session with cid and s
     * @return Illuminate\Http\RedirectResponse
     */
    public function youtube_auth_redirect (Request $req) {
        if(!$req->has('code')) {
            throw new Exception('$_GET[\'code\'] is not set. Please re-authenticate.');
        }

        try {
            $client_id = $req->session()->get('cid');
            $secret = $req->session()->get('s');
            $helper = new YoutubeDataHelper($client_id, $secret);
            $helper->handle_auth_redirect($req->get('code'));
        }
        catch (Exception $ex) {
            throw $ex;
        }
        return redirect('/');
    }

    /**
     * Method called from /get_caption_track_id
     *
     * This method returns track id for video via Youtube Data API
     * using GoogleCredential stored in database.
     * @uses oval\Classes\YoutubeDataHelper::handle_access_token_refresh()
     * @uses oval\Classes\YoutubeDataHelper::get_caption+track_id()
     * @param string $video_identifier Video ID for Youtube Video
     * @return string $track_id
     */
    private function get_caption_track_id ($video_identifier) {
        $langs = config('youtube.transcript_lang');
        $credentials = oval\Models\GoogleCredential::all();
        $track_id = null;
        if (!empty($credentials) && count($credentials)>0) {
            foreach ($credentials as $cred) {
                $helper = new YoutubeDataHelper($cred->client_id, $cred->client_secret);
                $helper->handle_access_token_refresh($cred);

                $track_id = $helper->get_caption_track_id($video_identifier);
                if (!empty($track_id)) {
                    break;
                }
            }
        }
        return $track_id;
    }

    /**
     * Method called from /check_youtube_caption route
     * to check if we can get caption for the video.
     *
     * If there is google credential stored in database,
     * we check if we can get caption id with it.
     * If that is not successful, check if there is caption in language(s) set in config
     * that is publicly available.
     *
     * @param Request $req Request contains video_id(identifier)
     * @uses /config/youtube_transcript_lang.php
     * @uses GoogleAPIController::get_caption_track_id()
     * @return array key:caption_available val:true if available, false if not
     */
    public function check_youtube_caption (Request $req) {
        $video_id = $req->video_id;
        $caption_available = false;
        $langs = config('youtube.transcript_lang');

        //-- check if google credentials we have can get caption
        $caption_id = $this->get_caption_track_id($video_id);
        if (!empty($caption_id)) {
            $caption_available = true;
        }
        //--if no caption from creds we have, check if there's public non-auto-generated transcript
        else {
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
                curl_setopt($ch, CURLOPT_URL, 'http://video.google.com/timedtext?lang='.$l.'&v='.$video_id);
                $response = curl_exec($ch);
                if (!empty($response)) {
                    $caption_available = true;
                    break;
                }
            }
        }
        return compact('caption_available');

    }



}

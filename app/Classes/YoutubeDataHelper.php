<?php
namespace oval\Classes;

use oval;
use Google_Client;
use Google_Service_YouTube;
use Google_Service_Exception;

/**
 * This is a helper class to use Youtube Data API Client Library to handle the actual API calls
 */
class YoutubeDataHelper {

    /**
     * @var Google_Client Google_Client object used for API calls
     */
    private $client;

    /**
     * Constructor to set up client with ID & secret for OAuth
     *
     * Sets up the Google_Client object with the parameter passed in,
     * with scope of youtube.force-ssl
     * and redirect url (where browser is redirected after authentication with google).
     * Needs *client id* and *secret* from Google Cloud Console ({@link https://cloud.google.com/console})
     *
     * @param string $client_id
     * @param string $secret
     */
    public function __construct($client_id, $secret) {
        $proxy_url = env('CURL_PROXY_URL', '');
        $proxy_user = env('CURL_PROXY_USER', '');
        $proxy_pass = env('CURL_PROXY_PASS', '');

        if (!empty($proxy_url)) {
            putenv('HTTPS_PROXY=' . $proxy_url);
        }

        $this->client = new Google_Client();
        $this->client->setClientId($client_id);
        $this->client->setClientSecret($secret);
        $this->client->setAccessType("offline");
        $this->client->setScopes('https://www.googleapis.com/auth/youtube.force-ssl');
        $redirect = filter_var('https://' . $_SERVER['HTTP_HOST']."/youtube_auth_redirect", FILTER_SANITIZE_URL);
        $this->client->setRedirectUri($redirect);
    }

    /**
     * This method returns the auth url of the client object
     *
     * Returned URL is the path to authenticate with Google credentials.
     * Sets option to get Google permission screen
     * and receive refresh token again if the authentication was already established.
     *
     * @return string auth url (the google login page)
     */
    public function get_auth_url() {
        $this->client->setPrompt('consent');    //-->obtain refresh token
		$authURL = $this->client->createAuthUrl();
		return $authURL;
    }

    /**
     * This method saves the newly authenticated google credential to database
     *
     * @param string $code param passed when coming back after authentication by google
     */
    public function handle_auth_redirect($code) {
        $token = json_encode($this->client->authenticate($code));
        $accessToken = $this->client->getAccessToken();

        //-- get channel id & channel name from api call
        $service = new Google_Service_YouTube($this->client);
        $response = $service->channels->listChannels('id,snippet', array('mine'=>true));

        $google_cred = oval\GoogleCredential::where('client_id', '=', $this->client->getClientId())
                                            ->first();
        if (empty($google_cred)) {
            $google_cred = new oval\GoogleCredential;
            $google_cred->client_id = $this->client->getClientId();
        }
        $google_cred->client_secret = $this->client->getClientSecret();
        $google_cred->access_token = $token;
        $google_cred->channel_id = $response->items[0]->id;
        $google_cred->channel_title = $response->items[0]->snippet->title;

        $google_cred->save();
    }

    /**
     * This method checks if access token is expired for credential passed in and refreshes it if expired
     * @param oval\GoogleCredential $cred
     */
    public function handle_access_token_refresh(oval\GoogleCredential $cred) {
        $existing_token = $cred['access_token'];
        $this->client->setAccessToken($existing_token);
        if ($this->client->isAccessTokenExpired()) {
            $cred->access_token = json_encode($this->client->refreshToken(json_decode($existing_token)->refresh_token));
            $cred->save();
        }
    }

    /**
     * Method to get caption track list from Youtube Data API
     * @link https://developers.google.com/youtube/v3/docs/captions/list
     *
     * @param string $video_identifier video id of the google video
     *
     * @return array returned by Youtube Data API
     */
    public function get_captions($video_identifier) {
        $youtube = new Google_Service_Youtube($this->client);
        $captions = $youtube->captions->listCaptions("id,snippet", $video_identifier);
        return $captions;
    }

    /**
     * Method to get id of caption track with preferred language
     *
     * @uses get_captions to get array of caption tracks
     *
     * @param string $video_identifier video id of the google video
     *
     * @return string id of the caption track
     *
     */
    public function get_caption_track_id($video_identifier) {
        $captions = $this->get_captions($video_identifier);
        $langs = config('youtube.transcript_lang');
        $track_id = null;
        foreach ($captions as $c) {
            if (in_array($c['snippet']['language'], $langs)) {
                $track_id = $c['id'];
                break;
            }
        }
        return $track_id;
    }

    /**
     * This method calls Youtube Data API to download caption whose id is passed in
     *
     * Call to Youtube Data API's caption.download method returns stream.
     * The stream is converted to string using Guzzle
     * @see http://docs.guzzlephp.org/en/stable/psr7.html
     *
     * @param string $track_id caption track's id
     *
     * @return array containing json object with keys: start, end, transcript
     */
    public function download_caption($track_id) {
        $youtube = new Google_Service_Youtube($this->client);
        try{
            $response = $youtube->captions->download($track_id, array(
            'tfmt' => "srt",
            'alt' => "media"
            ));
        }
        catch(Google_Service_Exception $e) {
            return null;
        }
        $caption_string = $response->getBody()->getContents();
        $temp = explode("\n\n", $caption_string);
        $caption_array = [];
        foreach ($temp as $t) {
            $item = explode("\n", $t);
            if (count($item) > 2) {
                $number = $item[0];
                $time = $item[1];
                $times = explode(" --> ", $time);

                $start_time = $times[0];
                $start_parts = explode(":", $start_time);
                $start = intval($start_parts[0])*3600 + intval($start_parts[1])*60 + floatval(str_replace(',', '.', $start_parts[2]));

                $end_time = $times[1];
                $end_parts = explode(":", $end_time);
                $end = intval($end_parts[0])*3600 + intval($end_parts[1])*60 + floatval(str_replace(',', '.', $end_parts[2]));

                $text = "";
                for ($i=2; $i<count($item); $i++) {
                    $text .= str_replace('\n', ' ', $item[$i]);
                }
                $caption_array[] = json_encode(array('start'=>$start, 'end'=>$end, 'transcript'=>$text));
            }
        }
        return $caption_array;
    }

}//end class


?>

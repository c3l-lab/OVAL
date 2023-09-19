<?php

namespace oval\Services;

class YoutubeService
{
    private \GuzzleHttp\Client $client;
    private string $apiKey;

    public const YOUTUBE_API_URL = 'https://www.googleapis.com/youtube/v3/';

    // constructor
    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client();
        $this->apiKey = config('youtube.api_key');
    }

    public function fetchContentDetails(string $identifier)
    {
        $url = static::YOUTUBE_API_URL . 'videos?part=snippet%2CcontentDetails&id=' . $identifier . '&key=' . config('youtube.api_key');
        $response = $this->client->get($url);
        return json_decode($response->getBody());
    }
}

<?php

namespace App\Services;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Support\Facades\Log;

class TweetService
{
    private $client;

    public function __construct($access_token = '', $access_secret = '')
    {
        $consumer_key = env('TWITTER_CONSUMER_KEY', '');
        $consumer_secret = env('TWITTER_CONSUMER_SECRET', '');
        $this->client = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_secret);
    }

    public function uploadMedias(array $mediaPaths): array
    {
        $mediaIds = [];

        foreach ($mediaPaths as $path) {
            if (filter_var($path, FILTER_VALIDATE_URL)) {
                $fileContent = file_get_contents($path);
                if ($fileContent === false) {
                    continue; // Bỏ qua nếu tải không thành công
                }
                $tempPath = tempnam(sys_get_temp_dir(), 'twitter_media_');
                file_put_contents($tempPath, $fileContent);
                $path = $tempPath; // Cập nhật đường dẫn thành file cục bộ
            }
    
            // Upload file lên Twitter
            $uploadedMedia = $this->client->upload("media/upload", ["media" => $path]);
    
            // Nếu upload thành công, lưu media_id
            if (isset($uploadedMedia->media_id_string)) {
                $mediaIds[] = $uploadedMedia->media_id_string;
            }
    
            // Xóa file tạm
            if (isset($tempPath) && file_exists($tempPath)) {
                unlink($tempPath);
            }
        }

        return $mediaIds;
    }

    public function store(string $message, array $mediaPaths = [])
    {
        $mediaIds = $this->uploadMedias($mediaPaths);

        Log::info('Media ids', $mediaIds);
        
        $parameters = ["text" => $message];
        if (!empty($mediaIds)) {
            $parameters["media"] = ["media_ids" => $mediaIds];
        }

        $response = $this->client->post("tweets", $parameters);

        Log::info('Publish response:', [
            'httpCode' => $this->client->getLastHttpCode()
        ]);

        if ($this->client->getLastHttpCode() == 201) {
            return [
                'httpCode' => $this->client->getLastHttpCode(),
                'response' => $response->data->id
            ];
        } else {
            return [
                'httpCode' => $this->client->getLastHttpCode(),
                'response' =>  null
            ];
        }
    }

    public function destroy($id)
    {
        $response = $this->client->delete("tweets/{$id}");
        return [
            'httpCode' => $this->client->getLastHttpCode(),
            'response' => $response
        ];
    }

    public function show($id)
    {
        $response = $this->client->get("tweets/{$id}");
        return [
            'httpCode' => $this->client->getLastHttpCode(),
            'response' => $response
        ];
    }

    public function myTweets()
    {
        $user = $this->client->get("users/me");

        if (!isset($user->data->id)) {
            return [
                'httpCode' => 400,
                'response' => "Failed to fetch user details: " . json_encode($user)
            ];
        }

        $userId = $user->data->id;
        $response = $this->client->get("users/{$userId}/tweets");

        return [
            'httpCode' => $this->client->getLastHttpCode(),
            'response' => $response
        ];
    }

    public function tweetInteractions($tweetId)
    {
        $response = $this->client->get("tweets/{$tweetId}", [
            "tweet.fields" => "public_metrics"
        ]);
        Log::info('Get interaction response:', [
            'httpCode' => $this->client->getLastHttpCode()
        ]);
        if ($this->client->getLastHttpCode() == 200) {
            return [
                "number_of_likes" => $response->data->public_metrics->like_count,
                "number_of_shares" => $response->data->public_metrics->retweet_count,
                "number_of_comments" => $response->data->public_metrics->reply_count
            ];
        } else {
            return null;
        }
    }
}

<?php

namespace App\Services;

use App\Traits\APIResponse;
use Illuminate\Support\Facades\Config;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class GptService
{
    use APIResponse;

    public function __construct()
    {
    }

    public function enhancePost(string $userPost): string
    {
        $prompt = Config::get('prompts.enhance_post');
        $prompt = str_replace("{user_post}", $userPost, $prompt);

        $messages = [
            ['role' => 'system', 'content' => $prompt],
            ['role' => 'user', 'content' => $userPost]
        ];

        $client = new Client();
        $url = 'https://api.openai.com/v1/chat/completions';

        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            ], 
            'json' => [
                'model' => 'gpt-4',
                'messages' => $messages,
            ],
        ]);
        $result = json_decode($response->getBody()->getContents(), true);
        return $result['choices'][0]['message']['content'];
    }
}

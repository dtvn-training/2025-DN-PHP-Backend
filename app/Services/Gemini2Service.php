<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class Gemini2Service
{
    private Client $client;
    private String $model = 'gemini-2.0-flash-thinking-exp';
    public function __construct()
    {
        $this->client = new Client();
    }

    public function enhancePost(string $userPost)
    {
        $GEMINI_API_KEY = env('GEMINI_API_KEY', '');

        $prompt = Config::get('prompt.enhance_post');
        $prompt = str_replace("{user_post}", $userPost, $prompt);

        Log::info($prompt);

        $response = $this->client->post("https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent", [
            'query' => ['key' => $GEMINI_API_KEY],
            'json' => [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ],
                        'role' => 'model'
                    ],
                    [
                        'parts' => [
                            ['text' => $userPost]
                        ],
                        'role' => 'user'
                    ]
                ]
            ],
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);

        $response = json_decode($response->getBody(), true);
        $text = $response['candidates'][0]['content']['parts'][0]['text'] ?? null;
        return $text;
    }

    public function getOutput(string $text)
    {
        $GEMINI_API_KEY = env('GEMINI_API_KEY', '');

        $prompt = Config::get('prompt.get_improved_post_reasons');
        Log::info($prompt);

        $response = $this->client->post("https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent", [
            'query' => ['key' => $GEMINI_API_KEY],
            'json' => [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ],
                        'role' => 'model'
                    ],
                    [
                        'parts' => [
                            ['text' => $text]
                        ],
                        'role' => 'user'
                    ]
                ]
            ],
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);

        $response = json_decode($response->getBody(), true);
        $text = (string) $response['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if ($text) {
            $cleanText = str_replace(["```", "json"], "", $text);
            $json = json_decode($cleanText, true);
            return $json;
        }
        
        return null;
    }
}

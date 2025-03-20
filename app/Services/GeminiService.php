<?php

namespace App\Services;

use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;
use Illuminate\Support\Facades\Config;
use GeminiAPI\Resources\ModelName;

class GeminiService
{
    private Client $client;

    public function __construct()
    {
        $GEMINI_API_KEY = env('GEMINI_API_KEY', '');

        if (empty($GEMINI_API_KEY)) {
            throw new \Exception('Gemini API key is missing.');
        }

        $this->client = new Client($GEMINI_API_KEY);
    }

    public function enhancePost(string $userPost): string
    {
        $prompt = Config::get('prompts.enhance_post');
        $prompt = str_replace("{user_post}", $userPost, $prompt);

        $response = $this->client->withV1BetaVersion()
            ->generativeModel(ModelName::GEMINI_1_5_PRO_002)
            ->withSystemInstruction($prompt)
            ->generateContent(
                new TextPart($userPost),
            );

        return $response->text() ?? "No AI response received.";
    }
}

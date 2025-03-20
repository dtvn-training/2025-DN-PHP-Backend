<?php

namespace App\Http\Controllers;

use App\Services\Gemini2Service;
use App\Services\GeminiService;
use App\Services\GptService;
use App\Services\GrokService;
use App\Traits\APIResponse;
use Illuminate\Http\Request;

class ContentController extends ControllerWithGuard
{
    use APIResponse;

    private $llmService;

    public function __construct(Gemini2Service $llmService)
    {
        parent::__construct();
        $this->llmService = $llmService;
    }

    public function enhanceContent(Request $request)
    {
        $content = $request->content;
        $text = $this->llmService->enhancePost($content);
        $result = $this->llmService->getOutput($text);

        return $this->responseSuccessWithData($result);
    }
}

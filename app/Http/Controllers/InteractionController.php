<?php

namespace App\Http\Controllers;

use App\Services\InteractionService;
use App\Traits\APIResponse;

class InteractionController extends ControllerWithGuard
{
    use APIResponse;
    private $interactionService;
    public function __construct(InteractionService $interactionService)
    {
        parent::__construct();
        $this->interactionService = $interactionService;
    }
    public function getInteractionPostPlatform($id) {
        $result = $this->interactionService->getInteractionsPostPlatform($id);
        if($result == null) return $this->responseErrorWithData("Id post platform no exists");
        return $this->responseSuccessWithData($result);
    }

    public function getInteractionsPost($id) {
        $result = $this->interactionService->getInteractionsPost($id);
        return $this->responseSuccessWithData($result);
    }
}

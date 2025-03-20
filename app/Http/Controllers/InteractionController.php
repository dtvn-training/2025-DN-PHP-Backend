<?php

namespace App\Http\Controllers;

use App\Services\InteractionService;
use App\Traits\APIResponse;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isEmpty;

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
        if(empty($result)) return $this->responseErrorWithData("No data interactions");
        return $this->responseSuccessWithData($result);
    }
}

<?php

namespace App\Repositories\Interaction;


interface InteractionRepositoryInterface
{
    public function createOrUpdateInteraction($postPlatformId, $data);
    public function getInteractionsPostPlatform($id);
    public function getInteractionsPostPlatformToday($id);
}

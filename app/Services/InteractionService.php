<?php

namespace App\Services;

use App\Repositories\Interaction\InteractionRepositoryInterface;
use App\Repositories\Post\PostRepositoryInterface;
use App\Services\PostService;
use App\Services\TweetService;

class InteractionService
{
    private $interactionRepository;
    private $postRepository;
    private $postService;
    public function __construct(InteractionRepositoryInterface $interactionRepository, PostRepositoryInterface $postRepository, PostService $postService)
    {
        $this->interactionRepository = $interactionRepository;
        $this->postRepository = $postRepository;
        $this->postService = $postService;
    }

    public function createOrUpdateInteraction($postPlatformId, $data)
    {
        $this->interactionRepository->createOrUpdateInteraction($postPlatformId, $data);
    }

    public function getInteractionsPostPlatform($id)
    {
        $postPlatform = $this->postRepository->getPostPlatformById($id);
        if ($postPlatform) {
            $interactions = $this->interactionRepository->getInteractionsPostPlatform($id);
            return [
                "platform" => $postPlatform->platform,
                "interactions" => $interactions
            ];
        } else {
            return null;
        }
    }

    public function getInteractionsPost($id)
    {
        $post = $this->postService->show($id);
        $result = [];
        if ($post != null) {
            foreach ($post->postPlatforms as $postPlatform) {
                switch ($postPlatform->platform) {
                    case 'TWITTER': {
                            $socialAccount = $postPlatform->socialAccount;
                            $tweetService = new TweetService($socialAccount->access_token, $socialAccount->access_token_secret);
                            $data = $tweetService->tweetInteractions($postPlatform->post_platform_id);
                            if ($data != null) {
                                $result[] = [
                                    "id" => $postPlatform->id,
                                    "platform" => $postPlatform->platform,
                                    "interactions" => $data
                                ];

                                $this->interactionRepository->createOrUpdateInteraction($postPlatform->id, $data);
                            }
                            break;
                        }
                    case 'LINKEDIN': {
                            $data = $this->interactionRepository->getInteractionsPostPlatformToday($postPlatform->id);
                            if ($data != null) {
                                $result[] = [
                                    "id" => $postPlatform->id,
                                    "platform" => $postPlatform->platform,
                                    "interactions" => $data
                                ];
                            }
                            break;
                        }
                }
            }
        }

        return $result;
    }
}

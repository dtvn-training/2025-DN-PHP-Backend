<?php

namespace App\Jobs;

use App\Models\Post;
use App\Models\PostPlatform;
use App\Models\SocialAccount;
use App\Services\LinkedinService;
use App\Services\TweetService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as HTTPStatus;

class AsyncPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $post;
    public $platform;
    public function __construct(Post $post, $platform)
    {
        $this->post = $post;
        $this->platform = $platform;
    }

    public function handle(LinkedinService $linkedinService)
    {
        $socialAccount = SocialAccount::where('user_id', $this->post['user_id'])
            ->where('platform', $this->platform)
            ->first();
        switch ($this->platform) {
            case 'LINKEDIN':
                $result =  $linkedinService->postToLinkedIn(
                    $this->post['content'],
                    $this->post['media_urls'] ?? [],
                    $socialAccount->access_token
                );
                break;

            case 'TWITTER':
                if ($socialAccount) {
                    $tweetService = new TweetService(
                        $socialAccount->access_token,
                        $socialAccount->access_token_secret
                    );

                    $result = $tweetService->store(
                        $this->post['content'],
                        $this->post['media_urls'] ?? []
                    );
                }
                break;
        }
        
        PostPlatform::insert([
            PostPlatform::ID => Str::uuid(),
            PostPlatform::POST_PLATFORM_ID => $result['response'],
            PostPlatform::POST_ID => $this->post['id'],
            PostPlatform::PLATFORM => $this->platform,
            PostPlatform::SOCIAL_ACCOUNT_ID => $socialAccount->id,
            PostPlatform::CREATED_AT => Carbon::now(),
            PostPlatform::POST_AT => Carbon::now(),
            PostPlatform::STATUS => $result['httpCode'] == HTTPStatus::HTTP_CREATED ? 'SUCCESS' : 'FAILED'
        ]);
    }
}

<?php

namespace App\Console\Commands;

use App\Jobs\GetInteractions;
use App\Services\PostService;
use Illuminate\Console\Command;
use App\Models\Post;
use App\Jobs\PublishPost;
use Carbon\Carbon;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class ScheduleInteractions extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'interactions:get';

    /**
     * The console command description.
     */
    protected $description = 'Schedule to get interactions on platforms';

    /**
     * The post service instance.
     */
    protected $postService;

    public function __construct(PostService $postService)
    {
        parent::__construct();
        $this->postService = $postService;
    }

    public function handle()
    {
        $postPlatforms = $this->postService->getAllSuccessPostPlatforms();

        Log::info('list_platform', $postPlatforms->toArray());
        Bus::batch(
            collect($postPlatforms)->map(fn($postPlatform) => new GetInteractions($postPlatform))->toArray()
        )->dispatch();
    }
}

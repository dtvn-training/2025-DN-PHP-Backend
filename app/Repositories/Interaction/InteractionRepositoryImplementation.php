<?php

namespace App\Repositories\Interaction;

use App\Models\Interaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class InteractionRepositoryImplementation implements InteractionRepositoryInterface
{
    public function createOrUpdateInteraction($postPlatformId, $data)
    {
        Interaction::updateOrCreate(
            [
                'post_platform_id' => $postPlatformId,
                'day' => Carbon::today()
            ],
            [
                'id' => Str::uuid(),
                'number_of_likes' => $data['number_of_likes'],
                'number_of_shares' => $data['number_of_shares'],
                'number_of_comments' => $data['number_of_comments']
            ]
        );
    }

    public function getInteractionsPostPlatform($id)
    {
        $sevenDaysAgo = Carbon::today()->subDays(7);
        return Interaction::where('post_platform_id', $id)
            ->where('day', '>=', $sevenDaysAgo)
            ->orderBy('day', 'asc')
            ->get();
    }

    
    public function getInteractionsPostPlatformToday($id) {
        return Interaction::where('post_platform_id', $id)
            ->where('day', '=', Carbon::today())
            ->first();
    }
}

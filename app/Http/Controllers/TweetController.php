<?php

namespace App\Http\Controllers;

use App\Http\Requests\TweetStoreRequest;
use App\Models\SocialAccount;
use App\Services\TweetService;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TweetController extends ControllerWithGuard
{
    use APIResponse;
    public function __construct()
    {
        parent::__construct(); 
    }

    public function store(TweetStoreRequest $request)
    {    
        $account = SocialAccount::where("user_id", $request->user()->id)->where("platform", "TWITTER")->first();
        $tweetService = new TweetService($account->access_token, $account->access_token_secret);
        $result = $tweetService->store($request->message, $request->mediaPaths ? $request->mediaPaths : []);
        return $this->responseSuccessWithData($result);
    }

    public function destroy($id, Request $request)
    {
        $account = SocialAccount::where("user_id", $request->user()->id)->where("platform", "TWITTER")->first();
        $tweetService = new TweetService($account->access_token, $account->access_token_secret);
        $result = $tweetService->destroy($id);
        return $this->responseSuccessWithData($result['response']);
    }

    public function show($id, Request $request)
    {
        $account = SocialAccount::where("user_id", $request->user()->id)->where("platform", "TWITTER")->first();
        $tweetService = new TweetService($account->access_token, $account->access_token_secret);
        $result = $tweetService->show($id);
        return $this->responseSuccessWithData($result['response']);
    }

    public function myTweets(Request $request)
    {
        $account = SocialAccount::where("user_id", $request->user()->id)->where("platform", "TWITTER")->first();
        $tweetService = new TweetService($account->access_token, $account->access_token_secret);
        $result = $tweetService->myTweets();
        return $this->responseSuccessWithData($result['response']);
    }

    public function tweetInteractions($id, Request $request)
    {
        $account = SocialAccount::where("user_id", $request->user()->id)->where("platform", "TWITTER")->first();
        Log::info('Account', $account->toArray());
        $tweetService = new TweetService($account->access_token, $account->access_token_secret);
        $result = $tweetService->tweetInteractions($id);
        return $this->responseSuccessWithData($result);
    }
}

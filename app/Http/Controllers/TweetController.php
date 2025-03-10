<?php

namespace App\Http\Controllers;

use App\Services\TweetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TweetController extends Controller
{
    private $tweetService;

    public function __construct(TweetService $tweetService)
    {
        $this->middleware('auth:api');
        $this->tweetService = $tweetService;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:280',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->tweetService->store($request->message, $request->mediaPaths);

        if ($result instanceof \Illuminate\Http\JsonResponse) {
            return $result;
        }
        
        return response()->json($result['response'], $result['httpCode']);
    }

    public function destroy($id)
    {
        $result = $this->tweetService->destroy($id);
        return response()->json($result['response'], $result['httpCode']);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:280',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->tweetService->update($id, $request->message);
        return response()->json($result['response'], $result['httpCode']);
    }

    public function show($id)
    {
        $result = $this->tweetService->show($id);
        return response()->json($result['response'], $result['httpCode']);
    }

    public function myTweets()
    {
        $result = $this->tweetService->myTweets();
        return response()->json($result['response'], $result['httpCode']);
    }

    public function tweetInteractions($id)
    {
        $result = $this->tweetService->tweetInteractions($id);
        return response()->json($result['response'], $result['httpCode']);
    }
}

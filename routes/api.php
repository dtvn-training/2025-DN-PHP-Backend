<?php

use App\Http\Controllers\TweetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

# Users 
Route::resource('users', UserController::class);
Route::patch('users/{id}', [UserController::class, 'changePassword']);
Route::get('deleted-users', [UserController::class, 'getDeletedUsers']);
Route::put('deleted-users/{id}', [UserController::class, 'restore']);
Route::get('me', [UserController::class, 'me']);

# Tweets
Route::resource('tweets', TweetController::class);
Route::get('my-tweets', [TweetController::class, 'myTweets']);
Route::get('tweets-interactions/{id}', [TweetController::class, 'tweetInteractions']);

# Middleware
Route::middleware(['auth:api'])->group(function () {
    Route::resource('users', UserController::class);
    Route::patch('users/{id}', [UserController::class, 'changePassword']);
    Route::get('deleted-users', [UserController::class, 'getDeletedUsers']);
    Route::put('deleted-users/{id}', [UserController::class, 'restore']);
    Route::get('me', [UserController::class, 'me']);

    Route::resource('tweets', TweetController::class);
    Route::get('my-tweets', [TweetController::class, 'myTweets']);
    Route::get('tweets-interactions/{id}', [TweetController::class, 'tweetInteractions']);
});

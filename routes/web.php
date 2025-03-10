<?php

use Illuminate\Support\Facades\Route;
use L5Swagger\Http\Controllers\SwaggerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SocialAuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

# Swagger 
Route::get('/api/documentation', [SwaggerController::class, 'api']);

# Authen Twitter
Route::get('/login/twitter', [AuthController::class, 'redirectToTwitter'])->name('login.twitter');
Route::get('/login/twitter/callback', [AuthController::class, 'handleTwitterCallback'])->name('login.twitter.callback');

# Auth
Route::post('/login', [AuthController::class, 'login']);

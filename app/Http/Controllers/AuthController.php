<?php

namespace App\Http\Controllers;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Http\Requests\UserStoreRequest;
use App\Models\User;
use App\Services\SocialAccountService;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Services\UserService;

class AuthController extends Controller
{
    use APIResponse;
    private $userService;
    protected $socialAccountService;

    public function __construct(SocialAccountService $socialAccountService, UserService $userService)
    {
        $this->socialAccountService = $socialAccountService;
        $this->userService = $userService;
    }

    public function redirectToTwitter()
    {
        $twitter = new TwitterOAuth(
            env('TWITTER_CONSUMER_KEY', ''),
            env('TWITTER_CONSUMER_SECRET', '')
        );

        // Request token from Twitter
        $request_token = $twitter->oauth('oauth/request_token', [
            'oauth_callback' => route('login.twitter.callback') // this call the route login.twitter.callback ~ function handleTwitterCallback
        ]);

        // Store tokens in session
        Session::put('oauth_token', $request_token['oauth_token']);
        Session::put('oauth_token_secret', $request_token['oauth_token_secret']);
        Session::put('auth_token', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvbG9naW4iLCJpYXQiOjE3NDE5MjYzMzMsImV4cCI6MTc0MTkyOTkzMywibmJmIjoxNzQxOTI2MzMzLCJqdGkiOiJrMUlLd2xZeWdsdjBKTFI1Iiwic3ViIjoiOWU2OTRlMjUtZGZjZC00NTZmLTlmNjItM2EzZWFhZWRlZGJkIiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyIsImlkIjoiOWU2OTRlMjUtZGZjZC00NTZmLTlmNjItM2EzZWFhZWRlZGJkIiwiZW1haWwiOiJja3V0Y2hAZXhhbXBsZS5jb20iLCJmdWxsX25hbWUiOiJJc2FiZWxsIEdyYW50Iiwicm9sZSI6IlVTRVIifQ.qn3iG7m6weDC-j6u3kFvG_gXUIf0kgZ8QuuQXDfhrVo'); // access token to laravel project (bearer token)

        // Redirect user to Twitter for authentication
        return redirect($twitter->url('oauth/authorize', [
            'oauth_token' => $request_token['oauth_token']
        ]));
    }

    public function handleTwitterCallback(Request $request)
    {
        $oauth_token = session('oauth_token');
        $oauth_token_secret = session(key: 'oauth_token_secret');
        $auth_token = session('auth_token');

        if (!$oauth_token || !$oauth_token_secret) {
            return $this->responseError('Request token missing', 400);
        }

        if ($auth_token) {
            try {
                $decoded = JWT::decode($auth_token, new Key(env('JWT_SECRET'), 'HS256'));
                $user = User::find($decoded->sub);

                if ($user) {
                    Auth::login($user);
                }
            } catch (\Exception $e) {
                return $this->responseError('Invalid JWT Token', 401);
            }
        }

        $twitter = new TwitterOAuth(
            env('TWITTER_CONSUMER_KEY', ''),
            env('TWITTER_CONSUMER_SECRET', ''),
            $oauth_token,
            $oauth_token_secret
        );

        $access_token = $twitter->oauth("oauth/access_token", [
            "oauth_verifier" => $request->oauth_verifier
        ]);

        $user = auth()->user();

        if ($user) {
            $socialAccount = $this->socialAccountService->store($user->getAuthIdentifier(), $access_token, 'TWITTER');
            return $this->responseSuccessWithData([
                'user' => $user,
                'social_account' => $socialAccount
            ]);
        }

        return $this->responseError('User not authenticated', 401);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Auth::attempt => query the db with email, password => get the user with these credentials
        if (Auth::attempt($credentials)) {
            $user = Auth::user(); // => get the current authenticated user

            try {
                $access_token = JWTAuth::fromUser($user);

                return $this->responseSuccessWithData([
                    'user' => $user,
                    'access_token' => $access_token
                ]);
            } catch (JWTException $e) {
                return $this->responseError('Could not create token', 500);
            }
        }

        return $this->responseError('Unauthorized', 401);
    }

    public function register(UserStoreRequest $request)
    {
        $user = $this->userService->store($request->full_name, $request->email, $request->password);
        $access_token = JWTAuth::fromUser($user); // generate jwt from user data
        $result = [
            'user' => $user,
            'access_token' => $access_token
        ];

        return $this->responseSuccessWithData($result, true);
    }
}

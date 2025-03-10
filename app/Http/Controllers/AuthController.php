<?php

namespace App\Http\Controllers;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function redirectToTwitter()
    {
        $twitter = new TwitterOAuth(
            env('TWITTER_CONSUMER_KEY'),
            env('TWITTER_CONSUMER_SECRET')
        );

        // Request token from Twitter
        $request_token = $twitter->oauth('oauth/request_token', [
            'oauth_callback' => route('login.twitter.callback') // this call the route login.twitter.callback ~ function handleTwitterCallback
        ]);

        // Store tokens in session
        Session::put('oauth_token', $request_token['oauth_token']);
        Session::put('oauth_token_secret', $request_token['oauth_token_secret']);

        // Redirect user to Twitter for authentication
        return redirect($twitter->url('oauth/authorize', [
            'oauth_token' => $request_token['oauth_token']
        ]));
    }

    public function handleTwitterCallback(Request $request)
    {
        $oauth_token = Session::get('oauth_token');
        $oauth_token_secret = Session::get('oauth_token_secret');

        $twitter = new TwitterOAuth(
            env('TWITTER_CONSUMER_KEY'),
            env('TWITTER_CONSUMER_SECRET'),
            $oauth_token,
            $oauth_token_secret
        );

        // Get access tokens
        $access_token = $twitter->oauth("oauth/access_token", [
            "oauth_verifier" => $request->oauth_verifier
        ]);

        // Write data to CSV
        $data = [
            $access_token['screen_name'], // username
            $access_token['user_id'],
            $access_token['oauth_token'],
            $access_token['oauth_token_secret']
        ];

        $filePath = storage_path('twitter_users.csv');
        if (!file_exists($filePath)) {
            $file = fopen($filePath, 'w');
            fputcsv($file, ['Username', 'User ID', 'Access Token', 'Access Secret']);
        } else {
            $file = fopen($filePath, 'a');
        }

        fputcsv($file, $data);
        fclose($file);

        return redirect('/api/users')->with('success', 'Twitter linked successfully!');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Auth::attempt => query the db with email, password => get the user with these credentials
        if (Auth::attempt($credentials)) {
            $user = Auth::user(); // => get the current authenticated user

            try {
                $access_token = JWTAuth::fromUser($user);

                return response()->json([
                    'user' => $user,
                    'access_token' => $access_token
                ]);
            }

            catch (JWTException $e) {
                return response()->json(['error' => 'Could not create token'], 500);
            }
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;


class GoogleAuthController extends Controller
{
    /**
     * Bounces the user to the Google auth page
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Triggered when the user is bounced back from Google
     */
    public function callback()
    {

        if ($googleUser = Socialite::driver('google')->user())
        {
            // Here if google trusts the user!
            
            
            // Ensure we have a local user that matches the given details. If not, create one!
            // $user = User::updateOrCreate([
            //     'email' => $googleUser->email,
            // ], [
            //     'name' => $googleUser->name,
            //     'email' => $googleUser->email,
            //     'password' => Hash::make(Str::random())
            // ]);

            // If the user is coming to us from a Web APP perspective, log them in!
            Auth::login($user);

            // Create a new personal access token!
            
            $token = $user->createToken('app');
            return $token->plainTextToken;
        }
        
        abort(401, 'Could not authenticate with Google');
    }

}

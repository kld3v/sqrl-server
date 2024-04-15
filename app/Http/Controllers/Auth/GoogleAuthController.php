<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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

    public function handleGoogleSignIn(Request $request)
    {
        $googleUser = Socialite::driver('google')->stateless()->userFromToken($request->accessToken);
        if ($googleUser) {
            $user = User::updateOrCreate([
                'email' => $googleUser->email,
            ], [
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'password' => Hash::make(Str::random())
            ]);

            Auth::login($user);

            $token = $user->createToken('GoogleSignInToken')->plainTextToken;

            return response()->json(['token' => $token], 200);
        }
        return response()->json(['error' => 'Invalid Google token'], 401);
    }

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
            $user = User::updateOrCreate([
                'email' => $googleUser->email,
            ], [
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'password' => Hash::make(Str::random()) 
            ]);
    
            Auth::login($user);
    
            $token = $user->createToken('GoogleSignInToken')->plainTextToken;
    
            $urlSuffix = !is_null($user->username) ? "username={$user->username}&token={$token}" : "token={$token}";
    
            return redirect("/auth/google/?{$urlSuffix}");
        }
        abort(401, 'Could not authenticate with Google');
    }    

}
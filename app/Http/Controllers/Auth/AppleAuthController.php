<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AppleAuthController extends Controller
{
    public function handleAppleSignIn(Request $request)
    {
        if (!$request->has('identityToken')) {
            return response()->json(['message' => 'Identity token is required'], 400);
        }

        $code = $request->input('identityToken');

        try {
            $appleUser = Socialite::driver('apple')->userFromToken($code);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid identity token provided'], 401);
        }

        $user = User::updateOrCreate([
            'email' => $appleUser->email,
        ], [
            'name' => $appleUser->name,
            'email' => $appleUser->email,
            'password' => Hash::make(Str::random())
        ]);

        Auth::login($user);

        $token = $user->createToken('AppleSignInToken')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }


    // public function redirect()
    // {
    //     return Socialite::driver('apple')->redirect();
    // }

    // /**
    //  * Triggered when the user is bounced back from Google
    //  */
    // public function callback()
    // {

    //     if ($appleUser = Socialite::driver('apple')->user())
    //     {
            
    //         return json_encode($appleUser);

    //     }
        
    //     abort(401, 'Could not authenticate with Apple');
    // }
}

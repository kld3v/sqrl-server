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
        $code = $request->authorizationCode;

        // Use Socialite to get the user from Apple
        $appleUser = Socialite::driver('apple')->userFromToken($code);

        // Find or create the user in your database
        // $user = User::updateOrCreate([
        //     'email' => $appleUser->email,
        // ], [
        //     'name' => $appleUser->name, // Handle name appropriately
        //     'apple_id' => $appleUser->id, // Use a column to store Apple ID if you want
        // ]);

        // // Authenticate the user
        // Auth::login($user);

        // // Return a response, such as a token or user data
        // // This is a placeholder, adjust based on your auth system
        // return response()->json($user);
        return response()->json($appleUser);
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

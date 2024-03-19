<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AppleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('apple')->redirect();
    }

    /**
     * Triggered when the user is bounced back from Google
     */
    public function callback()
    {

        if ($appleUser = Socialite::driver('apple')->user())
        {
            
            return $appleUser;

        }
        
        abort(401, 'Could not authenticate with Apple');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Snipe\BanBuilder\CensorWords;

class UserController extends Controller
{
    public function updateUsername(Request $request)
    {
        $user = Auth::user();
        $newUsername = $request->input('username');

        if (empty($newUsername)) {
            return response()->json(['error' => 'Username cannot be empty'], 400);
        }

        if (User::where('username', $newUsername)->exists()) {
            return response()->json(['error' => 'Username is unavailable'], 409);
        }

        $censor = new CensorWords();
        $censoredResult = $censor->censorString($newUsername);
        if ($censoredResult['orig'] != $censoredResult['clean']) {
            return response()->json(['error' => 'Username contains forbidden words'], 400);
        }

        $user->username = $newUsername;
        $user->save();

        return response()->json(['message' => 'Username updated successfully'], 200);
    }
}

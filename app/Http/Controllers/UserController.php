<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Snipe\BanBuilder\CensorWords;
use App\Rules\ValidUsername;

class UserController extends Controller
{
    public function updateUsername(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'username' => ['required', new ValidUsername],
        ]);
    
        if ($validator->fails()) {
            $firstErrorMessage = $validator->errors()->first();
            return response()->json(['error' => $firstErrorMessage], 422);
        }
    
        $user = Auth::user();
        $newUsername = $request->username;
    
        $user->username = $newUsername;
        $user->save();
    
        return response()->json(['message' => 'Username updated successfully'], 200);
    }
    
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Snipe\BanBuilder\CensorWords;
use App\Rules\ValidUsername;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function updateUsername(Request $request)
    {
        $lowercaseUsername = strtolower($request->username);
  
        $validator = \Validator::make(['username' => $lowercaseUsername], [
            'username' => ['required', new ValidUsername],
        ]);
    
        if ($validator->fails()) {
            $firstErrorMessage = $validator->errors()->first();
            return response()->json(['error' => $firstErrorMessage], 422);
        }
    
        $user = Auth::user();
        
        $user->username = $lowercaseUsername;
        $user->save();
    
        return response()->json(['message' => 'Username updated successfully'], 200);
    }

    public function delete(Request $request)
    {
        $user = $request->user();

        $deviceUuids = $user->scans()->distinct()->get(['device_uuid'])->pluck('device_uuid');

        DB::transaction(function () use ($user, $deviceUuids) {
            DB::table('scans')->whereIn('device_uuid', $deviceUuids)->update(['device_uuid' => '69420']);

            $user->delete();
        });

        return response()->json(['message' => 'User and relevant data removed successfully'], 200);
    }
    
    
}

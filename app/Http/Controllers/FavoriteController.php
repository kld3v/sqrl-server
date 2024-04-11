<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use App\Models\URL;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use App\Services\ScanProcessingService;
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function getFavorites(Request $request)
    {
        $user = auth()->user();
    
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        $favoriteUrls = $user->favoriteUrls()
            ->with(['scans' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->get()
            ->map(function ($favoriteUrl) {
                $favoriteUrl->is_favourite = true;
                $favoriteUrl->date_and_time = $favoriteUrl->pivot->created_at ?? null;
                $lastScan = $favoriteUrl->scans->first();
    
                return [
                    'url_id' => $favoriteUrl->id,
                    'url' => $favoriteUrl->url,
                    'date_and_time' => $favoriteUrl->date_and_time,
                    'trust_score' => $favoriteUrl->trust_score,
                    'is_favourite' => $favoriteUrl->is_favourite,
                    'scan_type' => $lastScan ? $lastScan->scan_type : null,
                ];
            });
    
        return response()->json($favoriteUrls);
    }
    
    

}

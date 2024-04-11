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

    // Fetch the user's favorite URLs with the current trust_score
    $favoriteUrls = $user->favoriteUrls()
        ->with(['scans' => function ($query) {
            $query->orderBy('created_at', 'desc')->limit(1);
        }])
        ->get(['urls.url', 'urls.trust_score'])
        ->map(function ($favoriteUrl) {
            // Assuming there's at least one scan, otherwise you might need additional checks
            $lastScan = $favoriteUrl->scans->first();
            
            // Add last scan details if available
            if ($lastScan) {
                $favoriteUrl->last_scan_type = $lastScan->scan_type;
                $favoriteUrl->last_scan_time = $lastScan->created_at;
            } else {
                // Default values or handling in case there's no scan
                $favoriteUrl->last_scan_type = null;
                $favoriteUrl->last_scan_time = null;
            }

            // You may unset the scans attribute if you don't want it in your final response
            unset($favoriteUrl->scans);

            return $favoriteUrl;
        });

    return response()->json($favoriteUrls);
}

}

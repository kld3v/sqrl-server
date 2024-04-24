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
                $query->latest('created_at')->first();
            }])
            ->get()
            ->map(function ($favoriteUrl) {
                $favoriteUrl->is_favorite = true;
                $favoriteUrl->date_and_time = $favoriteUrl->pivot->created_at ?? null;
                $lastScan = $favoriteUrl->scans;
    
                return [
                    'url_id' => $favoriteUrl->id,
                    'url' => $favoriteUrl->url,
                    'date_and_time' => $favoriteUrl->date_and_time,
                    'trust_score' => $favoriteUrl->trust_score,
                    'is_favorite' => $favoriteUrl->is_favorite,
                    'scan_type' => $lastScan ? $lastScan->scan_type : null,
                ];
            });
    
        return response()->json($favoriteUrls);
    }
    
    
    public function addFavorite(Request $request)
    {
        $data = $request->validate([
            'url' => 'sometimes|required|url',
            'url_id' => 'sometimes|required|integer',
        ]);
    
        // Ensure that either url or url_id is provided
        if (empty($data['url']) && empty($data['url_id'])) {
            return response()->json(['error' => 'Either URL or URL ID is required'], 422);
        }
    
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        try {
            if (!empty($data['url'])) {
                $url = URL::where('url', $data['url'])->firstOrFail();
            } elseif (!empty($data['url_id'])) {
                $url = URL::findOrFail($data['url_id']);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'URL not found'], 404);
        }
    
        if ($user->favoriteUrls()->find($url->id)) {
            return response()->json(['message' => 'URL is already a favorite'], 409);
        }
    
        $user->favoriteUrls()->attach($url->id);
    
        return response()->json(['message' => 'URL added to favorites successfully'], 200);
    }

    public function removeFavorite(Request $request)
    {
        $data = $request->validate([
            'url' => 'sometimes|required|url',
            'url_id' => 'sometimes|required|integer',
        ]);

        // Ensure that either url or url_id is provided
        if (empty($data['url']) && empty($data['url_id'])) {
            return response()->json(['error' => 'Either URL or URL ID is required'], 422);
        }

        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            if (!empty($data['url'])) {
                $url = URL::where('url', $data['url'])->firstOrFail();
            } elseif (!empty($data['url_id'])) {
                $url = URL::findOrFail($data['url_id']);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'URL not found'], 404);
        }

        if (!$user->favoriteUrls()->find($url->id)) {
            return response()->json(['message' => 'URL is not a favorite'], 404);
        }

        $user->favoriteUrls()->detach($url->id);

        return response()->json(['message' => 'URL removed from favorites successfully'], 200);
    }

    

}

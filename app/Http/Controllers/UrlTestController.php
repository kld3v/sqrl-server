<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UrlTestController extends Controller
{
    public function receiveUrlData(Request $request)
    {
        // Get the 'url' from request body
        $url = $request->input('url');
        $location = $request->input('location');

        // Accessing longitude, latitude, and altitude
        $longitude = isset($location['longitude']) ? $location['longitude'] : null;
        $latitude = isset($location['latitude']) ? $location['latitude'] : null;
        $altitude = isset($location['altitude']) ? $location['altitude'] : null;

        return response()->json([
            'trust_score' => 100
        ], 200);
    }
}

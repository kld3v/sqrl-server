<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VenueController extends Controller
{
    public function getVenuesByLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        // Query to find venues where the lat and long fall into their area
        $venues = Venue::with('url') // Eager load the related URL data
        ->select('id', 'company', 'chain', 'url_id', 'tel', 'address', 'postcode', 'google_maps')
        ->whereRaw("ST_Contains(area, ST_GeomFromText(CONCAT('POINT(', ?, ' ', ?, ')')))", [$latitude, $longitude])
        ->get();

        return response()->json($venues);
    }

    public function getNearbyVenues(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'nullable|numeric'
        ]);

        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $accuracy = $request->input('accuracy', 1000);

        // Query to find nearby venues within the specified distance
        $venues = Venue::with('url')
            ->select('id', 'company', 'chain', 'url_id', 'tel', 'address', 'postcode', 'google_maps')
            ->whereRaw("ST_Distance(
                            area, 
                            ST_GeomFromText(CONCAT('POINT(', ?, ' ', ?, ')'))
                        ) <= ?", [$longitude, $latitude, $accuracy])
            ->get();
    

        return response()->json($venues);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VenueController extends Controller
{
    public function getVenuesByLocation(Request $request)
    {
        // Validate request
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        // Extract latitude and longitude from the request
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        // Query to find venues where the lat and long fall into their area
        $venues = Venue::select('id', 'company', 'chain', 'url_id', 'tel', 'address', 'postcode', 'google_maps') // list all fields except 'area'
        ->whereRaw("ST_Contains(area, ST_GeomFromText(CONCAT('POINT(', ?, ' ', ?, ')')))", [$latitude, $longitude])
        ->get();

        // Return the results as JSON
        return response()->json($venues);
    }
}

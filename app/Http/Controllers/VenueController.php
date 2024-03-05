<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

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
        ->where('status', '=', 'active')
        ->get();

        return response()->json($venues);
    }

    public function getNearbyVenues(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|numeric'
        ]);

        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $radius = $request->input('radius', 1000);

        // Query to find nearby venues within the specified distance
        $venues = Venue::with('url') // Eager load the related URL data
            ->select('id', 'company', 'chain', 'url_id', 'tel', 'address', 'postcode', 'google_maps', 
                \DB::raw("ST_Distance_Sphere(
                    midpoint,
                    ST_GeomFromText('POINT($latitude $longitude)')
                ) as distance"))
            ->havingRaw("distance <= ?", [$radius])
            ->where('status', '=', 'active')
            ->orderBy('distance', 'asc')
            ->get();

        return response()->json($venues);
    }


    public function fetchVenues()
    {
        $venues = Venue::with('url')->get();

        // Convert each venue's area from a polygon to an array of [lat, long] pairs
        // and convert midpoint from a point to [lat, long]
        $venues->each(function ($venue) {
            // Convert area (polygon)
            $geoJsonArea = DB::table('venues') // Make sure 'venues' matches your table name
                        ->select(DB::raw('ST_AsGeoJSON(area) as geojson')) // 'area' is the polygon column
                        ->where('id', $venue->id)
                        ->first()->geojson;

            $polygonArray = json_decode($geoJsonArea, true);

            $latLongPairs = [];
            if (isset($polygonArray['coordinates'][0])) {
                foreach ($polygonArray['coordinates'][0] as $coordinate) {
                    $latLongPairs[] = [$coordinate[1], $coordinate[0]]; // [latitude, longitude]
                }
            }

            // Check if the last lat-long pair is the same as the first one, and drop it if true
            if (!empty($latLongPairs) && $latLongPairs[0] == end($latLongPairs)) {
                array_pop($latLongPairs); // Remove the last element
            }

            $venue->area = $latLongPairs;

            // Convert midpoint (point)
            $geoJsonMidpoint = DB::table('venues')
                                ->select(DB::raw('ST_AsGeoJSON(midpoint) as geojson'))
                                ->where('id', $venue->id)
                                ->first()->geojson;

            $pointArray = json_decode($geoJsonMidpoint, true);

            if (isset($pointArray['coordinates'])) {
                // GeoJSON coordinates are in [longitude, latitude] order
                $midpoint = [$pointArray['coordinates'][1], $pointArray['coordinates'][0]]; // [latitude, longitude]
            } else {
                $midpoint = [null, null]; // Default or error handling
            }
            $venue->midpoint = $midpoint;

        });
    
        return $venues;

    }

}

<?php

namespace App\Services\CalculateVenues;

use App\Models\Venue;
use Illuminate\Support\Facades\DB;

class SaveVenueService
{

    public static function save(int $urlId, array $coordinates)
    {
        $midpoint = self::calculateMidpoint($coordinates);
        $polygon = "POLYGON((" . implode(", ", array_map(fn($coordinate) => implode(" ", $coordinate), $coordinates)) . "))";

        $venues = Venue::query()
            ->whereRaw("ST_CONTAINS(area, ST_GeomFromText(CONCAT('POINT(', ?, ' ', ?, ')')))", [$midpoint[0], $midpoint[1]])
            ->get();

        if ($venues->isNotEmpty()) {
            foreach ($venues as $venue) {
                if ($venue->url_id === $urlId) {
                    // Match found, update the geofence data for each relevant venue
                    $venue->area = DB::raw("ST_GeomFromText('$polygon')");
                    $venue->midpoint = DB::raw("ST_GeomFromText('POINT($midpoint[0] $midpoint[1])')");
                    $venue->save();
                }
            }
        } else {
            // If there's no match, insert a new venue with the information
            $newVenue = new Venue();
            $newVenue->url_id = $urlId;
            $newVenue->area = DB::raw("ST_GeomFromText('$polygon')");
            $newVenue->midpoint = DB::raw("ST_GeomFromText('POINT($midpoint[0] $midpoint[1])')");
            $newVenue->status = 'pending';
            $newVenue->complete = false;
            $newVenue->save();
        }
    }

    private static function calculateMidpoint(array $coordinates): array
    {
        $latitudeSum = 0;
        $longitudeSum = 0;
        $count = count($coordinates);

        foreach ($coordinates as $coordinate) {
            $latitudeSum += $coordinate[0];
            $longitudeSum += $coordinate[1];
        }

        return [
            round($latitudeSum / $count, 7), // Midpoint latitude, rounded to 7 decimal places
            round($longitudeSum / $count, 7) // Midpoint longitude, rounded to 7 decimal places
        ];
    }
}

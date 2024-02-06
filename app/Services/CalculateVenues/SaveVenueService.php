<?php

namespace App\Services\CalculateVenues;

use App\Models\Venue;
use Illuminate\Support\Facades\DB;

class SaveVenueService
{
    public function saveOrUpdateVenue(array $newGeofence, int $urlId)
    {
        // Calculate the midpoint of the new geofence.
        $midpoint = $this->calculateMidpoint($newGeofence);

        // Check if the midpoint falls within any existing venue's geofence.
        $existingVenues = Venue::whereRaw("ST_Contains(area, ST_GeomFromText(?))", [/* Here, insert the WKT representation of the midpoint */])
                               ->get();

        foreach ($existingVenues as $venue) {
            // Check conditions and take actions based on the task description.
            if ($venue->url_id === $urlId) {
                if ($venue->status === 'pending') {
                    $venue->status = 'complete';
                    $venue->save();
                } elseif ($venue->status !== 'complete') {
                    // Update the geofence coordinate with the new one.
                    $venue->area = $newGeofence; // This might need to be adjusted based on your geofence storage format.
                    $venue->save();
                }
            } elseif (is_null($venue->url_id)) {
                $venue->url_id = $urlId;
                if ($this->isNewPolygonBigger($venue->area, $newGeofence)) {
                    $venue->area = $newGeofence;
                }
                $venue->save();
            }
        }

        // If it's a new venue, create a new venue entry.
        if ($existingVenues->isEmpty()) {
            Venue::create([
                'url_id' => $urlId,
                'area' => $newGeofence, // Adjust based on your storage format.
                'status' => 'active',
            ]);
        }
    }

    private function calculateMidpoint(array $geofence): array
    {
        // Implement the logic to calculate the midpoint of the geofence.
        // This is a placeholder implementation.
        return [/* midpoint latitude */, /* midpoint longitude */];
    }
    
    private function isNewPolygonBigger(string $oldPolygon, string $newPolygon): bool
    {
        // Implement the logic to compare the areas of the two polygons.
        // This is a placeholder implementation.
        return true;
    }
}

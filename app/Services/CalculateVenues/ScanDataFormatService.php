<?php

namespace App\Services\CalculateVenues;

use Illuminate\Support\Facades\DB;

class ScanDataFormatService {

    public function formatScansForUrlId($urlId) {
        $scans = $this->getScansForUrlId($urlId);
        return $this->formatScans($scans);
    }

    public function formatScans($scans) {
        $formattedScans = [];
        foreach ($scans as $scan) {
            $formattedScans[] = [$scan->latitude, $scan->longitude];
        }

        return $formattedScans;
    }

    private function getScansForUrlId($urlId) {
        // Logic to fetch scans data for a specific URL ID
        return DB::table('scans')
                 ->where('url_id', $urlId)
                 ->get();
    }
}

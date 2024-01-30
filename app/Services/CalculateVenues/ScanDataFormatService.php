<?php

namespace App\Services\CalculateVenues;

use App\Models\Scan;
use Illuminate\Database\Eloquent\Collection;

class ScanDataFormatService {

    public function formatScansForUrlId($urlId) {
        $scans = $this->getScansForUrlId($urlId);
        return $this->formatScans($scans);
    }

    public function formatScans(Collection $scans) {
        return $scans->map(function ($scan) {
            return [$scan->latitude, $scan->longitude];
        });
    }

    private function getScansForUrlId($urlId) {
        return Scan::where('url_id', $urlId)->get();
    }
}

<?php

namespace App\Services\CalculateVenues;

use App\Models\Scan;
use Illuminate\Database\Eloquent\Collection;

class ScanDataFormatService {

    public function formatScansForUrlId($urlId) {
        $scans = $this->getScansForUrlId($urlId);
        return $this->formatScans($scans)->toArray();
    }

    public function formatScans(Collection $scans) {
        $coordinates = $scans->map(function ($scan) {
            return [$scan->latitude, $scan->longitude];
        });

        return $this->removeDuplicates($coordinates);
    }

    private function getScansForUrlId($urlId) {
        return Scan::where('url_id', $urlId)->get();
    }

    private function removeDuplicates(\Illuminate\Support\Collection $coordinates) {
        return $coordinates->unique(function ($item) {
            return json_encode($item);
        })->values();
    }
}

<?php

namespace App\Services\CalculateVenues;

use App\Models\Scan;
use App\Services\CalculateVenues\ScanDataFormatService;
use Illuminate\Database\Eloquent\Collection;

class CalculateTriggerService {

    protected $scanDataFormatService;

    public function __construct(ScanDataFormatService $scanDataFormatService) {
        $this->scanDataFormatService = $scanDataFormatService;
    }

    public function checkAndTriggerClustering() {
        $urlId = $this->getUrlIdForClustering();

        if ($urlId !== null) {
            $this->scanDataFormatService->formatScansForUrlId($urlId);
        }
    }

    private function getUrlIdForClustering() {
        $urlId = Scan::select('url_id')
                     ->groupBy('url_id')
                     ->havingRaw('COUNT(*) >= 20')
                     ->first();

        return $urlId ? $urlId->url_id : null;
    }
}
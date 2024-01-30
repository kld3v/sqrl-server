<?php

namespace App\Services\CalculateVenues;

use Illuminate\Support\Facades\DB;

class CalculateTriggerService {

    public function checkAndTriggerClustering() {
        $urlId = $this->getUrlIdForClustering();

        if ($urlId !== null) {
            $scanDataFormatService = new ScanDataFormatService();
            $scanDataFormatService->formatScansForUrlId($urlId);

        }
    }
    private function getUrlIdForClustering() {
        // Logic to find a URL ID with 20 or more scans
        $urlId = DB::table('scans')
                    ->select('url_id')
                    ->groupBy('url_id')
                    ->having(DB::raw('count(*)'), '>=', 20)
                    ->first();

        return $urlId ? $urlId->url_id : null;
    }
}

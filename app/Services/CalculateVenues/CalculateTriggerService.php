<?php

namespace App\Services\CalculateVenues;

use App\Models\Scan;
use App\Services\CalculateVenues\ScanDataFormatService;
use App\Services\CalculateVenues\ClusteringService;
use App\Services\CalculateVenues\BorderCalculationService;
use Illuminate\Database\Eloquent\Collection;

class CalculateTriggerService {

    protected $scanDataFormatService;

    protected $clusteringService;

    protected $borderCalculationService;

    public function __construct(ScanDataFormatService $scanDataFormatService, ClusteringService $clusteringService, BorderCalculationService $borderCalculationService) {
        $this->scanDataFormatService = $scanDataFormatService;
        $this->clusteringService = $clusteringService;
        $this->borderCalculationService = $borderCalculationService;
    }

    public function checkAndTriggerClustering() {
        // $urlId = $this->getUrlIdForClustering();
        $urlId = 499;
        if ($urlId !== null) {
            $formattedScans = $this->scanDataFormatService->formatScansForUrlId($urlId);
            $clusters = $this->clusteringService->clusterScans($formattedScans);
            foreach ($clusters as $cluster) {
                // Calculate the border for each cluster
                $border = $this->borderCalculationService->calculateConcaveHull($cluster, 0.00010);

                echo "border; ";
                var_dump($border);
            }
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
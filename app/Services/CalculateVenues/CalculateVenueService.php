<?php

namespace App\Services\CalculateVenues;

use App\Models\Scan;
use App\Services\CalculateVenues\ScanDataFormatService;
use App\Services\CalculateVenues\ClusteringService;
use App\Services\CalculateVenues\BorderCalculationService;
use App\Services\CalculateVenues\BorderOptimisationService;
use Illuminate\Database\Eloquent\Collection;

class CalculateVenueService {

    protected $scanDataFormatService;

    protected $clusteringService;

    protected $borderCalculationService;

    protected $borderOptimisationService;

    public function __construct(ScanDataFormatService $scanDataFormatService, ClusteringService $clusteringService, BorderCalculationService $borderCalculationService, BorderOptimisationService $borderOptimisationService) {
        $this->scanDataFormatService = $scanDataFormatService;
        $this->clusteringService = $clusteringService;
        $this->borderCalculationService = $borderCalculationService;
        $this->borderOptimisationService = $borderOptimisationService;
    }

    public function VenueCalculationMain() {
        // $urlId = $this->getUrlIdForClustering();
        $urlId = 499;
        if ($urlId !== null) {
            $formattedScans = $this->scanDataFormatService->formatScansForUrlId($urlId);
            echo "formattedScans; ";
            var_dump($formattedScans);
            $clusters = $this->clusteringService->clusterScans($formattedScans);
            echo "clusters; ";
            var_dump($clusters);
            foreach ($clusters as $cluster) {
                $border = $this->borderCalculationService->calculateBorders($cluster, 0.85);
                echo "border; ";
                var_dump($border);
                $optimisedBorder = $this->borderOptimisationService->RamerDouglasPeucker2d($border, 0.00007);
                echo "optimisedBorder; ";
                var_dump($optimisedBorder);
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
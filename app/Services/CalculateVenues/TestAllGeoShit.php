<?php

namespace App\Services\CalculateVenues;

use App\Models\Scan;
use App\Models\Url; // Assuming you have a Url model
use Illuminate\Support\Facades\File; // For file operations

class TestAllGeoShit {

    protected $scanDataFormatService;
    protected $clusteringService;
    protected $borderCalculationService;
    protected $borderOptimisationService;

    public function __construct(
        ScanDataFormatService $scanDataFormatService,
        ClusteringService $clusteringService,
        BorderCalculationService $borderCalculationService,
        BorderOptimisationService $borderOptimisationService
    ) {
        $this->scanDataFormatService = $scanDataFormatService;
        $this->clusteringService = $clusteringService;
        $this->borderCalculationService = $borderCalculationService;
        $this->borderOptimisationService = $borderOptimisationService;
    }

    public function VenueCalculationMain($urlId) {
        $logPath = storage_path('logs\GeoLog.txt'); // Ensure the logs directory exists
        File::append($logPath, "[" . now() . "] Starting VenueCalculationMain for URL ID: $urlId\n");
    
        if ($urlId !== null) {
            try {
                // Log formattedScans input and output
                File::append($logPath, "[" . now() . "] Calling formatScansForUrlId with URL ID: $urlId\n");
                $formattedScans = $this->scanDataFormatService->formatScansForUrlId($urlId);
                File::append($logPath, "[" . now() . "] formatScansForUrlId output: " . json_encode($formattedScans) . "\n");
    
                // Log clusters input and output
                File::append($logPath, "[" . now() . "] Calling clusterScans with formattedScans\n");
                $clusters = $this->clusteringService->clusterScans($formattedScans);
                File::append($logPath, "[" . now() . "] clusterScans output: " . json_encode($clusters) . "\n");
    
                $borders = [];
                $optimisedBorders = [];
                foreach ($clusters as $cluster) {
                    File::append($logPath, "[" . now() . "] cluster working on: " . json_encode($cluster) . "\n");
                    // Log border calculation input and output
                    File::append($logPath, "[" . now() . "] Calling calculateBorders with cluster\n");
                    $border = $this->borderCalculationService->calculateBorders($cluster, 0.85);
                    File::append($logPath, "[" . now() . "] calculateBorders output: " . json_encode($border) . "\n");
    
                    // Log optimised border calculation input and output
                    File::append($logPath, "[" . now() . "] Calling RamerDouglasPeucker2d with border\n");
                    $optimisedBorder = $this->borderOptimisationService->RamerDouglasPeucker2d($border, 0.00003);
                    File::append($logPath, "[" . now() . "] RamerDouglasPeucker2d output: " . json_encode($optimisedBorder) . "\n");
    
                    $borders[] = $border;
                    $optimisedBorders[] = $optimisedBorder;
                }
    
                // Log method completion
                File::append($logPath, "[" . now() . "] VenueCalculationMain completed for URL ID: $urlId\n");
    
                return [
                    'formattedScans' => $formattedScans,
                    'clusters' => $clusters,
                    'borders' => $borders,
                    'optimisedBorders' => $optimisedBorders,
                ];
            } catch (\Exception $e) {
                // Log any exceptions
                File::append($logPath, "[" . now() . "] Error in VenueCalculationMain for URL ID: $urlId - " . $e->getMessage() . "\n");
                throw $e; // Re-throw the exception if you need to handle it further or just log it
            }
        }
    
        return null;
    }

    public function getAllUrlIds() {
        return Url::pluck('id');
    }

    private function createPointFeature($coordinates) {
        return [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'Point',
                'coordinates' => [(float)$coordinates[1], (float)$coordinates[0]],
            ],
            'properties' => new \stdClass()
        ];
    }

    

    private function isCounterClockwise($coordinates) {
        $sum = 0;
        $count = count($coordinates);
        for ($i = 0; $i < $count - 1; $i++) {
            $sum += ($coordinates[$i+1][0] - $coordinates[$i][0]) * ($coordinates[$i+1][1] + $coordinates[$i][1]);
        }
        return $sum > 0;
    }
    
    private function createLineOrPolygonFeature($coordinates, $isPolygon = false) {
        if ($isPolygon) {
            // Ensure the polygon closes by repeating the first coordinate at the end if not already done
            if ($coordinates[0] !== end($coordinates)) {
                $coordinates[] = $coordinates[0];
            }
            
            // Check and ensure counter-clockwise orientation for the outer boundary
            if (!$this->isCounterClockwise($coordinates)) {
                $coordinates = array_reverse($coordinates);
            }
        }
    
        // Adjust for GeoJSON format (longitude, latitude order)
        $adjustedCoordinates = $isPolygon ? [array_map(function($coord) { return [(float)$coord[1], (float)$coord[0]]; }, $coordinates)] : array_map(function($coord) { return [(float)$coord[1], (float)$coord[0]]; }, $coordinates);
    
        return [
            'type' => 'Feature',
            'geometry' => [
                'type' => $isPolygon ? 'Polygon' : 'LineString',
                'coordinates' => $adjustedCoordinates,
            ],
            'properties' => new \stdClass()
        ];
    }
    

    private function wrapFeaturesIntoGeoJSON($features) {
        return [
            'type' => 'FeatureCollection',
            'features' => $features
        ];
    }

    public function processAllUrlIds() {
        $urlIds = $this->getAllUrlIds();

        $formattedScansFeatures = [];
        $clustersFeatures = [];
        $bordersFeatures = [];
        $optimisedBordersFeatures = [];

        //JUST FOR TESTING
        // $urlIds = array_slice($urlIds->toArray(), 0, 20);

        foreach ($urlIds as $urlId) {
            $data = $this->VenueCalculationMain($urlId);
            if ($data) {
                foreach ($data['formattedScans'] as $scan) {
                    $formattedScansFeatures[] = $this->createPointFeature($scan);
                }
                foreach ($data['clusters'] as $cluster) {
                    foreach ($cluster as $point) {
                        $clustersFeatures[] = $this->createPointFeature($point);
                    }
                }
                foreach ($data['borders'] as $border) {
                    $bordersFeatures[] = $this->createLineOrPolygonFeature($border, true);
                }
                foreach ($data['optimisedBorders'] as $optimisedBorder) {
                    $optimisedBordersFeatures[] = $this->createLineOrPolygonFeature($optimisedBorder, true);
                }
            }
        }

        File::put(public_path('formattedScans.geojson'), json_encode($this->wrapFeaturesIntoGeoJSON($formattedScansFeatures)));
        File::put(public_path('clusters.geojson'), json_encode($this->wrapFeaturesIntoGeoJSON($clustersFeatures)));
        File::put(public_path('borders.geojson'), json_encode($this->wrapFeaturesIntoGeoJSON($bordersFeatures)));
        File::put(public_path('optimisedBorders.geojson'), json_encode($this->wrapFeaturesIntoGeoJSON($optimisedBordersFeatures)));
    }
}

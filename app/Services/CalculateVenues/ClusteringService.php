<?php


namespace App\Services\CalculateVenues;

use Illuminate\Support\Facades\App;
use Phpml\Clustering\DBSCAN;

class ClusteringService {
    public function clusterScans(array $scans) {
        //Definitely will need adjusting
        $metres = 15;

        //this is really simplistic and doesnt account for the fact that
        //the earth is not a perfect sphere but maybe fuck it, I think it works
        $epsilon = $metres / 111320;

        $minSamples = 3;

        $dbscan = new DBSCAN($epsilon, $minSamples);
        return $dbscan->cluster($scans);
    }
}
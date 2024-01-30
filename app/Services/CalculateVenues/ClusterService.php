<?php


namespace App\Services\CalculateVenues;

use Illuminate\Support\Facades\App;
use Phpml\Clustering\DBSCAN;

class ClusterService {

    public function formatScans($scans) {
        //format the scan data from the database layout, to one that DBSCAN can accept
    }
    public function cluster($scans) {
        $dbscan = new DBSCAN($epsilon = 2, $minSamples = 3);
        $clusters = $dbscan->cluster($scans);
    }
    public function calculateBorder($cluster) {

    }
    public function reduceBorderPoints($border) {
        //something like Ramer–Douglas–Peucker algorithm
    }
    public function saveBorder($border) {

    }
}
<?php


namespace App\Services;

use App\Http\Controllers\URLController;
use App\Http\Controllers\ScanController;
use Illuminate\Support\Facades\App;

class EvaluateTrustService {
    public function evaluateTrust($url) {
        $trustScore = 80;
        return($trustScore);
    }
}
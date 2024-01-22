<?php


namespace App\Services;

use App\Http\Controllers\URLController;
use App\Http\Controllers\ScanController;
use Illuminate\Support\Facades\App;

class evaluateTrustService {
    public function evaluateTrust($url) {
        $trustScore = 50;
        return($trustScore);
    }
}
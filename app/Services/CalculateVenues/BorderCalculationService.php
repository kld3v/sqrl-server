<?php


namespace App\Services\CalculateVenues;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;

class BorderCalculationService {

    public function calculateBorders($pts, $alfa) {

        $scriptPath = base_path('app/Scripts/CalculateVenue/BorderCalculation/alfa_main.py');

        $transformedPts = array_map(function($point) {
            return [(float)$point[0], (float)$point[1]]; // convert to float
        }, $pts);

        $args = escapeshellarg(json_encode($transformedPts)) . ' ' . escapeshellarg($alfa);

        exec("py " . escapeshellarg($scriptPath) . " " . $args, $output, $return_var);

        if ($return_var !== 0) {
            // Instead of throwing an exception, log the error and continue
            $logPath = storage_path('logs/GeoLog.txt'); // Adjust path as needed
            File::append($logPath, "[" . now() . "] Python script execution failed for cluster: " . json_encode($transformedPts) . "\n");
            return []; // Return an empty array or another appropriate default value
        }

        $result = json_decode(implode("\n", $output), true);

        if (!empty($result) && is_array($result[0])) {
            return $result[0][0];
        }
        return [];
    }
    
}
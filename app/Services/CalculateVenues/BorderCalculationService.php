<?php


namespace App\Services\CalculateVenues;

use Illuminate\Support\Facades\App;

class BorderCalculationService {

    public function calculateBorders($pts, $alfa) {

        $scriptPath = base_path('app/Scripts/CalculateVenue/BorderCalculation/alfa_main.py');

        $transformedPts = array_map(function($point) {
            return [(float)$point[1], (float)$point[0]]; // Flip coordinates and convert to float
        }, $pts);

        $args = escapeshellarg(json_encode($transformedPts)) . ' ' . escapeshellarg($alfa);

        exec("py " . escapeshellarg($scriptPath) . " " . $args, $output, $return_var);

        if ($return_var !== 0) {
            throw new \Exception("Python script execution failed");
        }

        $result = json_decode(implode("\n", $output), true);

        if (!empty($result) && is_array($result[0])) {
            return $result[0][0];
        }
        return [];
    }
    
}
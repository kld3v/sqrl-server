<?php

namespace App\Services\ScanLayers;

class LevenshteinAlgorithm
{
    public static function levenshteinCompare($str1, $str2)
    {
        $len1 = strlen($str1);
        $len2 = strlen($str2);

        $matrix = array();

        for ($i = 0; $i <= $len1; $i++) {
            $matrix[$i] = array();
            for ($j = 0; $j <= $len2; $j++) {
                if ($i == 0) {
                    $matrix[$i][$j] = $j;
                } elseif ($j == 0) {
                    $matrix[$i][$j] = $i;
                } else {
                    $cost = ($str1[$i - 1] != $str2[$j - 1]) ? 1 : 0;
                    $matrix[$i][$j] = min(
                        $matrix[$i - 1][$j] + 1,
                        $matrix[$i][$j - 1] + 1,
                        $matrix[$i - 1][$j - 1] + $cost
                    );
                }
            }
        }

        return $matrix[$len1][$len2];
    }

    public static function compareDomains($urlToCompare)
    {
        // Fixed path to the JSON file
        $domainsJsonPath = public_path('famousDomains.json');
        $domainsJson = file_get_contents($domainsJsonPath);
        $domainsData = json_decode($domainsJson, true);

        // Extract host name from URL
        $parsedUrl = parse_url($urlToCompare);
        $hostName = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
        $minDistance = PHP_INT_MAX;
        $similarDomain = '';
        $mainDomain = '';

        $exactMatch = false;

        foreach ($domainsData as $domain) {
            $distance = self::levenshteinCompare($hostName, $domain);

            if ($hostName === $domain) {
                $exactMatch = true;
                break;
            }

            // Check for similarity but not exact match
            if ($distance < $minDistance && $distance > 0) {
                $minDistance = $distance;
                $similarDomain = $hostName;
                $mainDomain = $domain;
            }
        }

        return $exactMatch ? false : ($minDistance < 3);
    }

}

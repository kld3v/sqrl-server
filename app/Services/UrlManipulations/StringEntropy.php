<?php
namespace App\Services\UrlManipulations;
//this must be improved right now its not accurate at all

class StringEntropy
{
    function calculateEntropy($url)
    {
      
        //removing https://
        $prefix = 'https://';
        $noHttps = substr($url, strlen($prefix));
        $shape = [];
        for ($i = 0; $i < strlen($noHttps); $i++) {
            $character = $noHttps[$i];
            if (!array_key_exists($character, $shape)) {
                $shape[$character] = 1;
            } else {
                $shape[$character]++;
            }
        }
        $result = 0.0;
        $len = strlen($noHttps);

        foreach ($shape as $item) {
            $frequency = $item / $len;
            $result -= $frequency * (log($frequency) / log(2));
        }

        return $result;
    }

}
<?php

namespace App\Services\ShortURL\Resolvers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

use Illuminate\Support\Facades\Log;

class Generic
{
    public function unshort($url, $timeout = null)
    {   
        //POINTLESS COMMENT JUST FOR A GIT COMMIT

        $client = new Client([
            // Set a default timeout if not provided
            'timeout'  => $timeout ?? 10,
            'allow_redirects' => true // Enable redirection
        ]);


        try {
            $finalUrl = null;
            $response = $client->get($url, [
                'on_stats' => function (\GuzzleHttp\TransferStats $stats) use (&$finalUrl) {
                    $finalUrl = (string) $stats->getEffectiveUri();
                }
            ]);
            return $finalUrl ?: $url;
        } catch (GuzzleException $e) {
            Log::error('Error unshortening URL:', ['message' => $e->getMessage()]);
            return null;
        }

    }
}

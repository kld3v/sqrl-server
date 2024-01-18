<?php

namespace App\Services\ShortUrl\resolvers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Generic
{
    public function unshort($url, $timeout = null)
    {
        $client = new Client([
            // Set a default timeout if not provided
            'timeout'  => $timeout ?? 10,
            'allow_redirects' => true // Enable redirection
        ]);

        try {
            $response = $client->get($url);
            // Retrieve the redirect history
            $redirects = $response->getHeader('X-Guzzle-Redirect-History');
            // If there are redirects, the final URL is the last item in the history
            if (!empty($redirects)) {
                $finalUrl = end($redirects);
            } else {
                // No redirects occurred, use the original URL
                $finalUrl = $url;
            }
            return $finalUrl;
        } catch (GuzzleException $e) {
            // Handle exceptions, log errors, etc.
            return null;
        }
    }
}

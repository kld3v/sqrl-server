<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PhishingLinkUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $url = 'https://phishunt.io/feed.txt';
            $new_domains_content = file_get_contents($url);

            if ($new_domains_content === false) {
                throw new \Exception("Failed to download the new domains file.");
            }
            $url_without_protocol = str_replace(array('http://', 'https://'), '', $new_domains_content);
            $new_domains = array_filter(array_map('trim', explode("\n", $url_without_protocol)));
            //Read and decode the existing JSON file
            $json_file_path = base_path('bin/badDomains.json');
            if (!file_exists($json_file_path)) {
                throw new \Exception("The file baddomains.json does not exist.");
            }

            $json_data = file_get_contents($json_file_path);
            $domains_data = json_decode($json_data, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Error decoding JSON from baddomains.json: " . json_last_error_msg());
            }

            //Append the new domains to the existing data
            if (isset($domains_data['domains']) && is_array($domains_data['domains'])) {
                $domains_data['domains'] = array_merge($domains_data['domains'], $new_domains);
            } else {
                $domains_data['domains'] = $new_domains;
            }
            //Removing possible dublicate data --->dont know it this one is working correctly
            $domains_data=array_unique($domains_data);
            // Encode the updated data back to JSON and save it
            $updated_json_data = json_encode($domains_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            if (file_put_contents($json_file_path, $updated_json_data) === false) {
                throw new \Exception("Failed to save the updated baddomains.json.");
            }

            echo "File updated successfully.";
        } catch (\Exception $e) {
            echo "An error occurred: " . $e->getMessage();
        }
    }

}

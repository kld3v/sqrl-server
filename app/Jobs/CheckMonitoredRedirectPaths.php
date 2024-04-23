<?php

namespace App\Jobs;

use App\Models\MonitoredRedirectPath;
use App\Models\MonitoredRedirectCheck;
use App\Services\ShortURL\Resolvers\HeadlessBrowser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckMonitoredRedirectPaths implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $redirectPaths = MonitoredRedirectPath::all();
        $headlessBrowser = new HeadlessBrowser();

        foreach ($redirectPaths as $path) {
            $finalUrl = $headlessBrowser->interactWithPage($path->initial_url);
            $isSafe = ($finalUrl === $path->expected_url);

            MonitoredRedirectCheck::create([
                'monitored_redirect_path_id' => $path->id,
                'final_url' => $finalUrl,
                'is_safe' => $isSafe
            ]);

            if (!$isSafe) {
                Log::warning("URL Check Failed for {$path->initial_url}, expected {$path->expected_url}, got {$finalUrl}");
            }
        }
    }
}
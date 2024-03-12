<?php

namespace App\Jobs;

use App\Events\AsynchProcess;
use Illuminate\Bus\Queueable;
use App\Events\ScanJobCompleted;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use App\Events\ProcessScanJobCompleted;
use App\Services\ScanProcessingService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\ShortURL\Resolvers\HeadlessBrowser;

class ProcessScanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $url;
    public function __construct($url)
    {
        $this->url = $url;
    }

    public function handle()
    {
        Log::channel('redirectLog')->info('jobs executing');
        $headlessBrowser = new HeadlessBrowser();
        $result= $headlessBrowser->interactWithPage($this->url);

        // Dispatch an event with the result
        event(new ScanJobCompleted($result));
    }
}

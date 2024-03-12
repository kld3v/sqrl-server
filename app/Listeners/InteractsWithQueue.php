<?php

namespace App\Listeners;

use App\Events\ScanJobCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProcessScanJobCompletedListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct()
    {
        //
    }

    public function handle(ScanJobCompleted $event)
    {
        $result = $event->result;
        
        // Process the result as needed
        // For example, log it
        logger('Scan job completed with result: ' . $result);
    }
}

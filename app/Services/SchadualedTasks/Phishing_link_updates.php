<?php
namespace App\Services\SchadualedTasks;
use App\Jobs\PhishingLinkUpdate;

class Phishing_link_updates 
{
    public function downloadLinksJob()
    {
        PhishingLinkUpdate::dispatch();
        return response()->json(['success' => 'Job dispatched successfully.'], 200);
    }
}
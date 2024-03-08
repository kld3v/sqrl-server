<?php
namespace App\Services\ShortURL\Resolvers;

use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Exception\BrowserConnectionFailed;
use Illuminate\Support\Facades\Log;

class HeadlessBrowser
{
    public function interactWithPage($url)
    {
        Log::channel('redirectLog')->info("Starting browser to interact with URL: {$url}");

        $browserFactory = new BrowserFactory();
        $browser = $browserFactory->createBrowser([
            'headless' => true,
            'disableNotifications'=>true
        ]);
        try {
            $page = $browser->createPage();
            $pageNav = $page->navigate($url);
            $pageNav->waitForNavigation();
            $lastUrl = $page->getCurrentUrl();

            Log::channel('redirectLog')->info("Navigation completed. Final URL: {$lastUrl}");


            return $lastUrl;
        }catch (BrowserConnectionFailed $e) {

            Log::channel('redirectLog')->error("Browser connection failed: " . $e->getMessage());

            // The browser was probably closed, start it again
            $factory = new BrowserFactory();
            $browser = $factory->createBrowser([
                'keepAlive' => false,
            ]);  
            
            Log::channel('redirectLog')->info("Browser restarted after failure.");
        }

    }
}
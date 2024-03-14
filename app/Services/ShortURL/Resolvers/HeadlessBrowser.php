<?php
namespace App\Services\ShortURL\Resolvers;

use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Exception\BrowserConnectionFailed;
use Illuminate\Support\Facades\Log;
use Exception; 
class HeadlessBrowser
{
    public function interactWithPage($url)
    {
        // Log::channel('redirectLog')->info("Starting browser to interact with URL: {$url}");
        try {
            $browserFactory = new BrowserFactory();
            // Log::channel('redirectLog')->info("Browser Factory Created Successfully");
            $browser = $browserFactory->createBrowser([
                'headless' => true,
                'disableNotifications'=>true
            ]);

            try {
                $page = $browser->createPage();
                // Log::channel('redirectLog')->info("Creating Page");
                $pageNav = $page->navigate($url);
                // Log::channel('redirectLog')->info("Navigating");
                $pageNav->waitForNavigation();
                // Log::channel('redirectLog')->info("Waiting");
                $lastUrl = $page->getCurrentUrl();

                Log::channel('redirectLog')->info("Navigation completed (headless file). Final URL {$lastUrl}");

                return $lastUrl;
            } catch (BrowserConnectionFailed $e) {
                // Log::channel('redirectLog')->error("Browser connection failed during navigation: " . $e->getMessage());

            }
        } catch (Exception $e) {
            // Log::channel('redirectLog')->error("Failed to create browser instance: " . $e->getMessage());
        }
    }
}

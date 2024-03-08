<?php
namespace App\Services\ShortURL\Resolvers;

use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Exception\BrowserConnectionFailed;
use Illuminate\Support\Facades\Log;
use Exception; // Make sure to include the general Exception class for catching any type of exceptions

class HeadlessBrowser
{
    public function interactWithPage($url)
    {
        Log::channel('redirectLog')->info("Starting browser to interact with URL: {$url}");
        
        try {
            $browserFactory = new BrowserFactory();
            Log::channel('redirectLog')->info("Browser Factory Created Successfully");
        } catch (Exception $e) {
            Log::channel('redirectLog')->error("Failed to initialize Browser Factory: " . $e->getMessage());
            return;
        }

        try {
            $browser = $browserFactory->createBrowser([
                'headless' => true,
                'disableNotifications' => true
            ]);
        } catch (Exception $e) {
            Log::channel('redirectLog')->error("Failed to create browser instance: " . $e->getMessage());
            return;
        }

        try {
            $page = $browser->createPage();
            Log::channel('redirectLog')->info("Creating Page: {$page}");
        } catch (Exception $e) {
            Log::channel('redirectLog')->error("Failed to create page: " . $e->getMessage());
            return;
        }

        try {
            $pageNav = $page->navigate($url);
            Log::channel('redirectLog')->info("Navigating: {$pageNav}");
        } catch (Exception $e) {
            Log::channel('redirectLog')->error("Navigation failed: " . $e->getMessage());
            return;
        }

        try {
            $pageNav->waitForNavigation();
            Log::channel('redirectLog')->info("Waiting: {$pageNav}");
        } catch (Exception $e) {
            Log::channel('redirectLog')->error("Failed to wait for navigation: " . $e->getMessage());
            return;
        }

        try {
            $lastUrl = $page->getCurrentUrl();
            Log::channel('redirectLog')->info("Final Destination: {$lastUrl}");
        } catch (Exception $e) {
            Log::channel('redirectLog')->error("Failed to get current URL: " . $e->getMessage());
            return;
        }

        Log::channel('redirectLog')->info("Navigation completed. Final URL: {$lastUrl}");

        return $lastUrl;
    }
}

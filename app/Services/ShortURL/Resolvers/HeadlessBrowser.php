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
        $userDataDir = null;
        try {
            $browserFactory = new BrowserFactory();

            // Create a unique directory for the browser instance
            $userDataDir = storage_path('app/chromium_data') . DIRECTORY_SEPARATOR . uniqid('chrome_data_dir_', true);
            // Ensure the directory exists
            if (!file_exists($userDataDir)) {
                mkdir($userDataDir, 0775, true); // Create the directory with read/write permissions for the owner and group
            }

            $browser = $browserFactory->createBrowser([
                'headless' => true,
                'disableNotifications' => true,
                'userDataDir' => $userDataDir // Set the unique directory here
            ]);

            try {
                $page = $browser->createPage();
                $pageNav = $page->navigate($url);
                $pageNav->waitForNavigation();
                $lastUrl = $page->getCurrentUrl();

                // Log::channel('redirectLog')->info("Navigation completed. Final URL: " . $lastUrl);

                return $lastUrl;
            } catch (BrowserConnectionFailed $e) {
                Log::channel('redirectLog')->error("Browser connection failed during navigation: " . $e->getMessage());
            } finally {
                $browser->close(); // Ensure the browser is closed to free resources
            }
        } catch (Exception $e) {
            Log::channel('redirectLog')->error("Failed to create browser instance: " . $e->getMessage());
        } 
        
        finally {
            if ($userDataDir) {
                $this->deleteDirectory($userDataDir); // Delete the user data directory to clean up
            }
        }
    }

    private function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($dir);
    }
}

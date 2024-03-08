<?php
namespace App\Services\ShortURL\Resolvers;

use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Exception\BrowserConnectionFailed;

class HeadlessBrowser
{
    public function interactWithPage($url)
    {
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
            return $lastUrl;
        }catch (BrowserConnectionFailed $e) {
            // The browser was probably closed, start it again
            $factory = new BrowserFactory();
            $browser = $factory->createBrowser([
                'keepAlive' => false,
            ]);           
        }

    }
}
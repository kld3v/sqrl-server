<?php


namespace App\Services\ShortURL;

use App\Http\Controllers\URLController;
use App\Http\Controllers\ScanController;
use Illuminate\Support\Facades\App;

class ShortURLMain {
    public function isShortURL($url) {
        return(false);
    }
    public function expandURL($url) {
        return(false);
    }
    
}
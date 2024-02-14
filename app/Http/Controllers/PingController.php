<?php

namespace App\Http\Controllers;

use App\Services\ScanProcessingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PingController extends Controller
{
    public function ping() {

        app(ScanProcessingService::class)->processRequest('http://example.com');
        return [
            'time' => now(),
            'auth' => Auth::user(),
        ];
    }
}

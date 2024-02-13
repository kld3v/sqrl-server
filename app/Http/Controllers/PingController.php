<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PingController extends Controller
{
    public function ping() {
        return [
            'time' => now(),
            'auth' => Auth::user(),
        ];
    }
}

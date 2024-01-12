<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UrlTestController extends Controller
{
    public function receiveUrl(Request $request)
    {
        // Get the 'url' from request body
        $url = $request->input('url');

        return response()->json(['url' => $url], 200);
    }
}

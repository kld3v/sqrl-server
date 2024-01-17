<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UrlTestController extends Controller
{
    public function receiveUrlData(Request $request)
    {
        // Get the 'url' from request body
        $url = $request->input('url');

        return response()->json(['Database_Response' => $url], 200);
    }
}

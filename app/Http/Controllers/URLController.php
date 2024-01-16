<?php

namespace App\Http\Controllers;

use App\Models\URL;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class URLController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $this->validateURL($request);

        // Create a new URL instance
        $url = URL::create($validatedData);

        // Return the created URL instance as JSON with a 201 status code
        return response()->json($url, 201);
    }

    public function show($id)
    {
        // Retrieve an existing URL instance by its ID
        $url = URL::find($id);

        // If URL instance not found, return a 404 error
        if (!$url) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        // Return the found URL instance as JSON
        return response()->json($url);
    }

    private function validateURL(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'URL' => 'required|url|max:2048',
            'trust_score' => 'required|integer|min:0|max:1000',
        ]);

        return $validatedData;
    }
}

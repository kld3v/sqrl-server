<?php

namespace App\Http\Controllers;

use App\Models\URL;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Illuminate\Support\Facades\Log;

class URLController extends Controller
{
    private function validateURL(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'URL' => 'required|url|max:2048',
            'trust_score' => 'required|integer|min:0|max:1000',
        ]);

        return $validatedData;
    }
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

    public function findUrlByString($urlString)
    {
        Log::info("Finding URL by string: $urlString");
        $result = URL::where('URL', $urlString)->first();
        Log::info("URL find complete for string: $urlString");
        return $result;
    }
    
    public function updateTrustScore($urlId, $newTrustScore)
    {
        try {
            $url = URL::findOrFail($urlId);
            $url->trust_score = $newTrustScore;
            $url->save();

            return response()->json($url);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'URL not found'], 404);
        } catch (\Exception $e) {
            // Handle any other exceptions
            return response()->json(['message' => 'An error occurred'], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use App\Models\URL;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ScanController extends Controller
{
    // Create a new Scan instance
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'url_id' => 'required|exists:urls,id',
            'trust_score' => 'required|numeric',
            'user_id' => 'required|exists:users,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $scan = Scan::create($validatedData);
        return response()->json($scan, 201);
    }

    // Retrieve a specific Scan instance
    public function show($id)
    {
        try {
            $scan = Scan::findOrFail($id);
            return response()->json($scan);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Scan not found'], 404);
        }
    }

    // Update a specific Scan instance
    public function update(Request $request, $id)
    {
        try {
            $scan = Scan::findOrFail($id);

            $validatedData = $request->validate([
                'trust_score' => 'sometimes|numeric',
            ]);

            $scan->update($validatedData);
            return response()->json($scan);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Scan not found'], 404);
        }
    }

    // Retrieve all Scan instances
    public function index()
    {
        $scans = Scan::all();
        return response()->json($scans);
    }

    // Retrieve the URL associated with a Scan
    public function getUrl($scanId)
    {
        try {
            $scan = Scan::findOrFail($scanId);
            $url = $scan->url;
            return response()->json($url);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Scan or URL not found'], 404);
        }
    }

    // Retrieve the User associated with a Scan
    public function getUser($scanId)
    {
        try {
            $scan = Scan::findOrFail($scanId);
            $user = $scan->user;
            return response()->json($user);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Scan or User not found'], 404);
        }
    }

}

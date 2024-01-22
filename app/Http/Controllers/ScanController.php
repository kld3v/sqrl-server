<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use App\Models\URL;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use App\Services\ScanProcessingService;

class ScanController extends Controller
{   
    protected $ScanProcessingService;

    public function __construct(ScanProcessingService $ScanProcessingService)
    {
        $this->ScanProcessingService = $ScanProcessingService;
    }

    public function processScan(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

    
        $url = $request->input('url');
        $coordinates = ['latitude' => $request->input('latitude'), 'longitude' => $request->input('longitude')];
    
        $scanData = $this->ScanProcessingService->processRequest($url);
    
        // Create scan record using the store method
        $scanRequest = new Request(array_merge($scanData, $coordinates));
        $scan = $this->store($scanRequest);

        return response()->json($scan);
    }

    private function validateData(Request $request)
    {
        $rules = [
            'url_id' => 'required|exists:urls,id',
            'trust_score' => 'required|numeric|min:0|max:1000',
            'user_id' => 'required|exists:users,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ];

        return $request->validate($rules);
    }

    
    // Create a new Scan instance
    public function store(Request $request)
    {
        try {
            $validatedData = $this->validateData($request);
            $scan = Scan::create($validatedData);
            return response()->json($scan, 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'The given data was invalid.'], 422);
        }
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
    

    // Delete a specific Scan instance
    public function destroy($id)
    {
        try {
            $scan = Scan::findOrFail($id);
            $scan->delete();
            return response()->json(null, 204);
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

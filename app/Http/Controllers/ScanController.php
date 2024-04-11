<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use App\Models\URL;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use App\Services\ScanProcessingService;
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Facades\Auth;

class ScanController extends Controller
{   
    protected $ScanProcessingService;

    public function __construct(ScanProcessingService $ScanProcessingService)
    {
        $this->ScanProcessingService = $ScanProcessingService;
        $this->middleware('auth:sanctum')->only(['getHistory']);
    }

    public function processRequest(Request $request)
    {   

        $request->validate([
            'url' => 'required',
            'device_uuid' => 'required',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'scan_type' => 'nullable|string'            
        ]);
        
    
        $url = $request->input('url');
    
        $scanData = $this->ScanProcessingService->processScan($url);
        
        
        // Format data for the 'scans' table
        $formattedScanData = [
            'url_id' => $scanData['id'],
            'trust_score' =>  $scanData['trust_score'],
            'test_version' => $scanData['test_version'],
            'device_uuid' => $request->input('device_uuid'),
            'user_id' => Auth::id(),
        ];

        if ($request->filled('scan_type')) {
            $formattedScanData['scan_type'] = $request->input('scan_type');
        }

        if ($request->has('latitude')) {
            $formattedScanData['latitude'] = $request->input('latitude');
        }
            
        if ($request->has('longitude')) {
                $formattedScanData['longitude'] = $request->input('longitude');
        }

        $scanRequest = new Request($formattedScanData);
        $this->store($scanRequest);
        
        return response()->json(['trust_score' => $scanData['trust_score']]);

    }

    private function validateData(Request $request)
    {
        $rules = [
            'url_id' => 'required|exists:urls,id',
            'trust_score' => 'required|numeric|min:0|max:1000',
            'test_version' => 'required|string|regex:/^[0-9]+\.[0-9]+\.[0-9]+$/',
            'device_uuid' => 'required',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'scan_type' => 'nullable|string',
            'user_id' => 'nullable',
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

    public function getHistory(Request $request)
    {
        $user = auth()->user();
    
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        $favoriteUrlIds = $user->favoriteUrls()->pluck('url_id')->toArray();
    
        $history = Scan::with('url')
                    ->where('user_id', $user->id)
                    ->get([
                        'url_id', 
                        'created_at as date_and_time', 
                        'trust_score',
                        'scan_type' 
                    ])
                    ->map(function ($scan) use ($favoriteUrlIds) {
                        $isFavourite = in_array($scan->url_id, $favoriteUrlIds);
    
                        return (object)[
                            'url_id' => $scan->url_id,
                            'url' => $scan->url->url,
                            'date_and_time' => $scan->date_and_time,
                            'trust_score' => $scan->trust_score,
                            'is_favourite' => $isFavourite,
                            'scan_type' => $scan->scan_type,
                        ];
                    });
    
        return $history;
    }

    public function removeScanHistory(Request $request, $scanId)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $scan = Scan::where('id', $scanId)->where('user_id', $user->id)->first();

        if (!$scan) {
            return response()->json(['error' => 'Scan history not found or access denied'], 404);
        }

        $scan->delete();

        return response()->json(['message' => 'Scan history removed successfully'], 200);
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


    public function getScans(Request $request)
    {
        $urlId = $request->input('url_id');
    
        if ($urlId) {
            $scans = Scan::where('url_id', $urlId)
                         ->whereRaw('LENGTH(device_uuid) = 16')
                         ->get();
        } else {
            $scans = Scan::whereRaw('LENGTH(device_uuid) = 16')->get();
        }
    
        return response()->json($scans);
    }
    


    public function testProcessRequest(Request $request)
    {
        $request->validate([
            'url' => 'required',         
        ]);
    
        $url = $request->input('url');
    
        $scanData = $this->ScanProcessingService->testProcessScan($url);
        
        return response()->json($scanData);
    }
    

}

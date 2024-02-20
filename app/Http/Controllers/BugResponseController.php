<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BugResponse;

class BugResponseController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'device_uuid' => 'required|string|max:36',
            'bug_description' => 'required|string',
        ]);
    
        $bugResponse = BugResponse::create([
            'device_uuid' => $request->device_uuid,
            'bug_description' => $request->bug_description,
            'status' => 'Open', // Default status
            'report_date' => now(), // Set report date to current time
            'resolution_date' => null, // Default to null
        ]);
    
        return response()->json($bugResponse, 201);
    }
    
}

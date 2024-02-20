<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuestionResponse;

class QuestionResponseController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'question_id' => 'required|integer|exists:questions,id',
            'device_uuid' => 'required|string|max:36',
            'response_answer' => 'required|string',
            'response_text' => 'string',
        ]);
    
        $questionResponse = QuestionResponse::create($request->all());
    
        return response()->json($questionResponse, 201);
    }
    
}

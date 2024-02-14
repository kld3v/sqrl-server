<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EvaluateTrustService;

class TestEvaluateTrustController extends Controller
{
    protected $evaluateTrustService;

    public function __construct(EvaluateTrustService $evaluateTrustService)
    {
        $this->evaluateTrustService = $evaluateTrustService;
    }

    public function testEvaluation(Request $request)
    {
        $url = $request->input('url');
        
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return response()->json(['error' => 'Invalid URL format'], 400);
        }

        $evaluationResult = $this->evaluateTrustService->evaluateTrust($url);

        return response()->json($evaluationResult);
    }
}
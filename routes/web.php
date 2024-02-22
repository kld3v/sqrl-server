<?php

use Illuminate\Support\Facades\Route;
use App\Services\EvaluateTrustService;
use App\Http\Controllers\URLmainController;
use App\Http\Controllers\RiskEvaluationController;
use App\Http\Controllers\TestEvaluateTrustController;
use Inertia\Inertia;
use App\Models\Venue;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
   return view('welcome');
});

Route::get('/check-web-risk', [EvaluateTrustService::class, 'evaluateTrust']);
Route::get('/test-evaluate-trust', function () {
   $url = 'https://transfer.sh/get/ITCnLojVnm/derrick.txt';
   $evaluateTrustService = app(EvaluateTrustService::class);
   $result = $evaluateTrustService->evaluateTrust($url);
   return response()->json($result);
});

Route::get('/venues', function () {
   $venues = Venue::all(); // Replace with your actual query logic
   return Inertia::render('VenuePage', ['venues' => $venues]);
});
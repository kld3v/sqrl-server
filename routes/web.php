<?php

use Illuminate\Support\Facades\Route;
use App\Services\EvaluateTrustService;
use App\Http\Controllers\URLmainController;
use App\Http\Controllers\RiskEvaluationController;
use App\Http\Controllers\TestEvaluateTrustController;

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
   $url = 'https://www.unblocktheship.org/';
   $evaluateTrustService = app(EvaluateTrustService::class);
   $result = $evaluateTrustService->evaluateTrust($url);
   return response()->json($result);
});
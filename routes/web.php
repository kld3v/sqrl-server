<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Services\EvaluateTrustService;
use App\Http\Controllers\URLmainController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\AppleAuthController;
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

   return Auth::user();

   return view('welcome');
});

//Google Login
Route::get('/login/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
Route::get('/login/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

//Apple Login
Route::get('/login/apple', [AppleAuthController::class, 'redirect'])->name('auth.apple');
Route::get('/login/apple/callback', [AppleAuthController::class, 'callback'])->name('auth.apple.callback');


Route::get('/check-web-risk', [EvaluateTrustService::class, 'evaluateTrust']);
Route::get('/test-evaluate-trust', function () {
   $url = "http://59.89.3.109:58853/i";
   $evaluateTrustService = app(EvaluateTrustService::class);
   $result = $evaluateTrustService->evaluateTrust($url);
   return response()->json($result);
});


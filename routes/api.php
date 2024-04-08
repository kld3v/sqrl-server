<?php

use App\Http\Controllers\PingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\VenueController;
use App\Http\Controllers\UserAgreementController;
use App\Http\Controllers\FakeLeaderboardController;
use App\Http\Controllers\Auth\AppleAuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


// Route for all closed API endpoints (i.e., routes that require a user to be authenticated)
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/scan', [ScanController::class, 'processRequest']);

});


//SCAN
Route::post('/scan', [ScanController::class, 'processRequest']);
Route::get('/scan-history', [ScanController::class, 'getHistory']);

//GEO
Route::get('/venues/location', [VenueController::class, 'getVenuesByLocation']);
Route::get('/venues/nearby', [VenueController::class, 'getNearbyVenues']);


Route::get('/ping', [PingController::class, 'ping']);


//Leaderboard
Route::get('/random-leaderboard', [FakeLeaderboardController::class, 'index']);

//Apple Sign In
Route::post('/auth/apple/signin', [AppleAuthController::class, 'handleAppleSignIn']);

//AGREEMENTS
// Check if user has agreed to active documents
Route::get('/agreements/check', [UserAgreementController::class, 'checkAgreements']);

// Record a user's agreement
Route::post('/agreements/sign', [UserAgreementController::class, 'signDocument']);


//DO NOT GO PUBLIC WITH THIS ROUTE
// Route::get('/getscans', [ScanController::class, 'getScans']);
Route::post('/test/scan', [ScanController::class, 'testProcessRequest'])->middleware('check.basic.phrase');
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VenueController;
use App\Http\Controllers\PingController;
use App\Http\Controllers\FakeLeaderboardController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\UserAgreementController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route for all closed API endpoints (i.e., routes that require a user to be authenticated)
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    
    Route::get('/scans/history', [ScanController::class, 'getHistory']);
    Route::delete('/scans/history/{scanId}', [ScanController::class, 'removeScanHistory']);

    Route::get('/favorites', [FavoriteController::class, 'getFavorites']);
    Route::post('/favorites', [FavoriteController::class, 'addFavorite']);
    Route::delete('/favorites', [FavoriteController::class, 'removeFavorite']);

    Route::post('/user/update-username', [UserController::class, 'updateUsername']);

});


//SCAN
Route::post('/scan', [ScanController::class, 'processRequest']);

//GEO
Route::get('/venues/location', [VenueController::class, 'getVenuesByLocation']);
Route::get('/venues/nearby', [VenueController::class, 'getNearbyVenues']);


Route::get('/ping', [PingController::class, 'ping']);


//Leaderboard
Route::get('/random-leaderboard', [FakeLeaderboardController::class, 'index']);


//AGREEMENTS
// Check if user has agreed to active documents
Route::get('/agreements/check', [UserAgreementController::class, 'checkAgreements']);

// Record a user's agreement
Route::post('/agreements/sign', [UserAgreementController::class, 'signDocument']);


//DO NOT GO PUBLIC WITH THIS ROUTE
// Route::get('/getscans', [ScanController::class, 'getScans']);
Route::post('/test/scan', [ScanController::class, 'testProcessRequest'])->middleware('check.basic.phrase');
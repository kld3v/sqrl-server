<?php

use App\Http\Controllers\PingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\VenueController;

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


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


//SCAN
Route::post('scan', [ScanController::class, 'processScan']);


//GEO
Route::get('/venues/location', [VenueController::class, 'getVenuesByLocation']);

Route::get('/venues/nearby', [VenueController::class, 'getNearbyVenues']);


Route::post('scan', [ScanController::class, 'processRequest']);

Route::get('/ping', [PingController::class, 'ping']);


//DO NOT GO PUBLIC WITH THIS ROUTE
Route::get('/getscans', [ScanController::class, 'getScans']);

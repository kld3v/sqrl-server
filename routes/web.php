<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

    return Auth::user();
 
    return view('welcome');
 });
  
 
 Route::get('/check-web-risk', [EvaluateTrustService::class, 'evaluateTrust']);
 Route::get('/test-evaluate-trust', function () {
    $url = "http://59.89.3.109:58853/i";
    $evaluateTrustService = app(EvaluateTrustService::class);
    $result = $evaluateTrustService->evaluateTrust($url);
    return response()->json($result);
 });

require __DIR__.'/auth.php';

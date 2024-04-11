<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AppleAuthController;
use App\Http\Controllers\Auth\GoogleAuthController;

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
  
//Google Login
Route::get('/auth/google/signin', [GoogleAuthController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
  
//Apple Login
Route::get('/auth/apple/signin', [AppleAuthController::class, 'redirect'])->name('auth.apple');
Route::post('/auth/apple/callback', [AppleAuthController::class, 'callback'])->name('auth.apple.callback');

 
 Route::get('/check-web-risk', [EvaluateTrustService::class, 'evaluateTrust']);
 Route::get('/test-evaluate-trust', function () {
    $url = "http://59.89.3.109:58853/i";
    $evaluateTrustService = app(EvaluateTrustService::class);
    $result = $evaluateTrustService->evaluateTrust($url);
    return response()->json($result);
 });

require __DIR__.'/auth.php';

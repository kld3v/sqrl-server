<?php

use Illuminate\Http\Request;
use App\Jobs\PhishingLinkUpdate;
use Illuminate\Support\Facades\Route;
use App\Services\EvaluateTrustService;
use App\Http\Controllers\Auth\AppleAuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Services\SchadualedTasks\Phishing_link_updates;

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

Route::get('/auth/google/', function (Request $request) {
   //THIS ROUTE IS JUST THE BLANK PAGE SHOWN ONCE A USER HAS LOGGED IN
   return ''; 
});

 
 Route::get('/check-web-risk', [EvaluateTrustService::class, 'evaluateTrust']);
 Route::get('/test-evaluate-trust', function () {
    $url = "https://www.docs.google.com/presentation/d/e/2PACX-1vSeANQ81f61Ij-lss1JqZYg0iFgZRitGseSNfmFwCK4wVFWYhbFKN602ZjnBP-Wdtokh9Nvsg17wlco/pub?start=false&amp;loop=false&amp;delayms=3000";
    $evaluateTrustService = app(EvaluateTrustService::class);
    $result = $evaluateTrustService->evaluateTrust($url);
    return response()->json($result);
 });


Route::get('/task', [Phishing_link_updates::class, 'downloadLinksJob']);
require __DIR__.'/auth.php';

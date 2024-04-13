<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AppleAuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use Illuminate\Http\Request;

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
   $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Close Window</title>
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.close();
            }, 1000); // Close after 1 second
        };
    </script>
</head>
<body>
    <p>If the window does not close automatically, you may close it manually.</p>
</body>
</html>
HTML;
   return response($html)->header('Content-Type', 'text/html');
});


 
 Route::get('/check-web-risk', [EvaluateTrustService::class, 'evaluateTrust']);
 Route::get('/test-evaluate-trust', function () {
    $url = "http://59.89.3.109:58853/i";
    $evaluateTrustService = app(EvaluateTrustService::class);
    $result = $evaluateTrustService->evaluateTrust($url);
    return response()->json($result);
 });

require __DIR__.'/auth.php';

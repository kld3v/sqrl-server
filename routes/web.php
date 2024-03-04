<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Venue;
use App\Http\Controllers\VenueController;

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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Route::get('/joel', function () {
//     return Inertia::render('Joel', [
//         'message' => "message",
//     ]);
// })->name('joel-route-name');

Route::get('/check-web-risk', [EvaluateTrustService::class, 'evaluateTrust']);
Route::get('/test-evaluate-trust', function () {
    $url = 'http://182.127.71.143:33800/Mozi.m';
    $evaluateTrustService = app(EvaluateTrustService::class);
    $result = $evaluateTrustService->evaluateTrust($url);
    return response()->json($result);
 });


Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/venues', function (VenueController $venueController) {
    $venues = $venueController->fetchVenues();
    return Inertia::render('VenuePage', ['venues' => $venues]);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

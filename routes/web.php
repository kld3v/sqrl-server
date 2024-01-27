<?php

use Illuminate\Support\Facades\Route;

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
Route::get('/check-ssl/{url}', function ($url) {
    $scriptPath = base_path('/app/Scripts/Sslkey.sh');
    $command = "$scriptPath $url";
    $output = shell_exec($command);
    return response()->json(['result' => $output]);
});

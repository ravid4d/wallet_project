<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


// Registration Api 
Route::post('/create',[App\Http\Controllers\MainController::class,'create']);
// Login Api
Route::post('/login',[App\Http\Controllers\MainController::class,'login']);
// add money to wallet and buy api
Route::middleware('auth:sanctum')->group(function()
{
    Route::put('/addWallet',[App\Http\Controllers\MainController::class,'addWallet']);
    Route::get('/buy/cookie',[App\Http\Controllers\MainController::class,'buycookies']);
});

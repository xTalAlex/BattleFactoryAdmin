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

Route::get('/squads', [App\Http\Controllers\SquadController::class , 'index'] );
Route::post('/squads', [App\Http\Controllers\SquadController::class , 'store'] );

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

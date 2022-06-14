<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ToursController;

//Route::middleware('auth:sanctum')->get('/test', function (Request $request) {
//    return $request->user();
//});

Route::get('/tours/get', [ToursController::class, 'getTours']);
Route::get('/tours/get/{id}', [ToursController::class, 'getTours']);

Route::get('/tours/get-by-keyword', [ToursController::class, 'searchTours']);
Route::get('/tours/get-by-condition', [ToursController::class, 'getConditionalTours']);

Route::get('/tour-ratings/get/{id}', [ToursController::class, 'getRatings']);

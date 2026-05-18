<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/test', function () {
    return response()->json([
        'mesazhi' => ' API i Laravel po funksionon!',
        'statusi' => 200
    ]);
});

Route::get('/tasks', [TaskController::class, 'index']); 
Route::post('/tasks', [TaskController::class, 'store']); 
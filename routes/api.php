<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Api\V1\FirstController ;
use App\Http\Controllers\AuthController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
Route::post('/login', [AuthController::class, 'login']);


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/hello', function () {
    return ["message"=> "Hello from Blerina"];
});

Route::prefix('v1')->group(function(){
    Route::apiResource('/firsts',FirstController::class);
});




Route::get('/tasks', [TaskController::class, 'index']); 
Route::post('/tasks', [TaskController::class, 'store']); 


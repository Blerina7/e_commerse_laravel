<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Api\V1\FirstController ;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

//Login+Register
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
Route::post('/login', [AuthController::class, 'login']);

//Menaxhimi i userave
Route::get('/users',[UserController::class,'index']);
Route::get('/user/{user}',[UserController::class,'show']);
Route::post('/update-user',[UserController::class,'update']);
Route::delete('/delete-user',[UserController::class,'destroy']);


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/hello', function () {
    return ["message"=> "Hello from Blerina"];
});







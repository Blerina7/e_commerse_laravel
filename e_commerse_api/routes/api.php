<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Api\V1\FirstController ;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;

//Login+Register
Route::post('/register', [AuthController::class,'register']);
Route::post('/verify-email', [AuthController::class,'verifyEmail']);
Route::post('/login', [AuthController::class,'login']);
Route::post('/logut', [AuthController::class,'logut']);
Route::post('/reset-password', [AuthController::class,'resetPassword']);
Route::post('/forgot-password', [AuthController::class,'forgotPassword']);

//Menaxhimi i userave
Route::get('/users',[UserController::class,'index']);
Route::get('/user/{user}',[UserController::class,'show']);
Route::post('/update-user',[UserController::class,'update']);
Route::delete('/delete-user',[UserController::class,'destroy']);
Route::get('/filter',[UserController::class,'filter']);

//Products
Route::get('/products',[ProductController::class,'index']);
Route::get('/products/{product}',[ProductController::class,'show']);
Route::post('/update-product',[ProductController::class,'update']);
Route::delete('/delete-product',[ProductController::class,'destroy']);
Route::post('/store-product',[ProductController::class,'store']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');








<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;

//publike
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Produktet dhe kategorite shihen nga te gjithe(publike)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/category', [CategoryController::class, 'index']);
Route::get('/brand', [BrandController::class, 'index']);

// Variantet 
Route::get('/products/{productId}/variants', [InventoryController::class, 'index']);
Route::get('/variants/{id}', [InventoryController::class, 'show']);


//authenticated duhet login 
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']); 
    Route::get('/user', fn(Request $r) => $r->user());

    // Porosite e userit -vetem useri i sheh dhe i ben 
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);

    // Stock — ulet kur bën porosi useri
    Route::patch('/variants/{id}/stock', [InventoryController::class, 'decrementStock']);


   //admin only
    Route::middleware('role:admin')->group(function () {

        // Menaxhimi i userave
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
        Route::get('/users/filter', [UserController::class, 'filter']);

        // Produktet — CRUD
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{product}', [ProductController::class, 'update']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);

        // Inventory/Variante — CRUD
        Route::post('/variants', [InventoryController::class, 'store']);
        Route::put('/variants/{id}', [InventoryController::class, 'update']);
        Route::delete('/variants/{id}', [InventoryController::class, 'destroy']);

        // Kategori & Brand — menaxhimi
        Route::post('/category', [CategoryController::class, 'store']);
        Route::post('/brand', [BrandController::class, 'store']);

        // Porositë 
        Route::delete('/orders/{order}', [OrderController::class, 'destroy']);
    });
});



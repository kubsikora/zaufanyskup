<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FormController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/ping', function () {
    return response()->json(['ok' => true]);
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgotPassword', [AuthController::class, 'forgotPassword']);
Route::get('/logout', [AuthController::class, 'logout']);

Route::get('/token', [AuthController::class, 'token']);
Route::post('/submit', [FormController::class, 'store']);
Route::get('/products', [ProductController::class, 'get']);

Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/products/update', [ProductController::class, 'update']);
    Route::post('/products/store', [ProductController::class, 'store']);
    Route::get('/products/index', [ProductController::class, 'index']);
    Route::post('/products/destroy', [ProductController::class, 'destroy']);

    Route::post('/users/update', [UserController::class, 'update']);
    Route::post('/users/store', [UserController::class, 'store']);
    Route::get('/users/index', [UserController::class, 'index']);
    Route::post('/changepassword', [UserController::class, 'changepassword']);
    Route::post('/users/destroy', [UserController::class, 'destroy']);

    Route::get('/form/index', [FormController::class, 'index']);

    Route::post('/productpicture/destroy', [ProductController::class, 'destroyPicture']);

});
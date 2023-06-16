<?php

use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\BookController;
use App\Http\Controllers\Api\v1\ImportBookDataController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('import-books/{quantity}', [ImportBookDataController::class, 'index']);

    // Admin login vrj022@gmail.com, pass: vivekjoshi
    Route::post('admin/login', [AuthController::class, 'adminLogin']);

    Route::apiResource('book', BookController::class);

    Route::post('token', [AuthController::class, 'validateToken'])->middleware('auth:sanctum');
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'admin/book'], function () {
        Route::post('/', [BookController::class, 'addBook']);
        Route::patch('{id}', [BookController::class, 'updateBook']);
        Route::delete('{id}', [BookController::class, 'deleteBook']);
        Route::post('cover-upload/{book}', [BookController::class, 'uploadCover']);
    });
});

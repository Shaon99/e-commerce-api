<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
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

Route::middleware('auth:sanctum')->group(function () {
    // Fetch authenticated user
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Categories routes
    Route::get('/categories', [CategoryController::class, 'index']); // Fetch all categories
    Route::post('/categories-store', [CategoryController::class, 'store']); // Store a new category
    Route::put('/categories/{category}', [CategoryController::class, 'update']); // Update an existing category
    Route::put('/categories-status-update/{id}', [CategoryController::class, 'statusUpdate']); // Update an existing category
    Route::delete('/categories-delete/{category}', [CategoryController::class, 'destroy']); // Delete a category
    Route::post('/multiple-category-delete', [CategoryController::class, 'multipleCategoryDelete']); // Delete a category

    //Brand routes
    Route::resource('brands', BrandController::class);
    Route::put('/brand-status-update/{id}', [BrandController::class, 'statusUpdate']); // Update an existing category
    Route::post('/multiple-brand-delete', [BrandController::class, 'multipleBrandDelete']); // Delete a category
    
    
    Route::post('/store-reviews', [CategoryController::class, 'storeReviews']);

});

Route::get('/reviews/{id}', [CategoryController::class, 'reviews']);


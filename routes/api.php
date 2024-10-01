<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\ProductController;
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

    Route::post('/store-reviews', [CategoryController::class, 'storeReviews']);
});
Route::get('/categories', [ProductController::class, 'fetchCategories']);
Route::get('/brands', [ProductController::class, 'fetchBrands']);

Route::get('/reviews/{id}', [CategoryController::class, 'reviews']);

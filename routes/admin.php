<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\{
    CategoryController,
    BrandController
};

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

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    //category route
    Route::get('/categories', [CategoryController::class, 'index']); // Fetch all categories
    Route::post('/categories-store', [CategoryController::class, 'store']); // Store a new category
    Route::post('/categories/{category}', [CategoryController::class, 'update']); // Update an existing category
    Route::put('/categories-status-update/{id}', [CategoryController::class, 'statusUpdate']); // Update an existing category
    Route::delete('/categories-delete/{category}', [CategoryController::class, 'destroy']); // Delete a category
    Route::post('/multiple-category-delete', [CategoryController::class, 'multipleCategoryDelete']); // Delete a category

    //brand routes
    Route::resource('brands', BrandController::class);
    Route::put('/brand-status-update/{id}', [BrandController::class, 'statusUpdate']); // Update an existing category
    Route::post('/multiple-brand-delete', [BrandController::class, 'multipleBrandDelete']); // Delete a category





    Route::post('/logout', [AuthController::class, 'logout']);
});

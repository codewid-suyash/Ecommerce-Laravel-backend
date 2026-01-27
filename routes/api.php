<?php

use App\Http\Controllers\admin\AuthController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\SizeController;
use App\Http\Controllers\front\AccountController;
use App\Http\Controllers\front\OrderController;
use App\Http\Controllers\front\ProductController as FrontProductController;
use App\Http\Controllers\TempImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/admin/login', [AuthController::class, 'Authenticate']);
Route::get('/get-latest-product', [FrontProductController::class, 'latestProducts']);
Route::get('/get-featured-product', [FrontProductController::class, 'featuredProducts']);
Route::get('/get-categories', [FrontProductController::class, 'getCategories']);
Route::get('/get-brands', [FrontProductController::class, 'getBrands']);
Route::get('/get-products', [FrontProductController::class, 'getProducts']);
Route::get('/get-product/{id}', [FrontProductController::class, 'getProduct']);
Route::post('/register', [AccountController::class, 'register']);
Route::post('/login', [AccountController::class, 'Authenticate']);

Route::group(['middleware' => ['auth:sanctum', 'checkUserRole']], function () {
    Route::post('/save-order', [OrderController::class, 'saveOrder']);
    Route::get('/get-order-details/{id}', [AccountController::class, 'getOrderDetails']);
});

Route::group(['middleware' => ['auth:sanctum', 'checkAdminRole']], function () {
    // Protected routes go here
    // Route::get('categories',[CategoryController::class,'index']);
    // Route::get('category/{id}',[CategoryController::class,'show']);
    // Route::post('categories',[CategoryController::class,'store']);
    // Route::put('category/{id}',[CategoryController::class,'update']);
    // Route::delete('category/{id}',[CategoryController::class,'destroy']);

    Route::resource('categories', CategoryController::class);
    Route::resource('brands', BrandController::class);
    Route::resource('products', ProductController::class);


    Route::post('temp-images', [TempImageController::class, 'store']);
    Route::get('sizes', [SizeController::class, 'index']);
    Route::post('save-product-image', [ProductController::class, 'saveProductImage']);
    Route::get('change-product-default-image', [ProductController::class, 'UpdateDefaultImage']);
    Route::delete('delete-product-image/{id}', [ProductController::class, 'deleteProductImage']);
});

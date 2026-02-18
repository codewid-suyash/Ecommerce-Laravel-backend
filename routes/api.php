<?php

use App\Http\Controllers\admin\AuthController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\SizeController;
use App\Http\Controllers\front\AccountController;
use App\Http\Controllers\front\OrderController;
use App\Http\Controllers\admin\OrderController as AdminOrderController;
use App\Http\Controllers\admin\ShippingController;
use App\Http\Controllers\front\ProductController as FrontProductController;
use App\Http\Controllers\front\ShippingController as FrontShippingController;
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
Route::get('get-shipping-front', [FrontShippingController::class, 'getShipping']);

Route::group(['middleware' => ['auth:sanctum', 'checkUserRole']], function () {
    Route::post('/save-order', [OrderController::class, 'saveOrder']);
    Route::get('/get-order-details/{id}', [AccountController::class, 'getOrderDetails']);
    Route::get('/get-orders', [AccountController::class, 'getOrders']);
    Route::post('/update-profile', [AccountController::class, 'updateProfile']);
    Route::get('/get-account-details', [AccountController::class, 'getAccountDetails']);
    Route::post('/create-payment-intent', [OrderController::class, 'createPaymentIntent']);
});

Route::group(['middleware' => ['auth:sanctum', 'checkAdminRole']], function () {

    Route::resource('categories', CategoryController::class);
    Route::resource('brands', BrandController::class);
    Route::resource('products', ProductController::class);


    Route::post('temp-images', [TempImageController::class, 'store']);
    Route::get('sizes', [SizeController::class, 'index']);
    Route::post('save-product-image', [ProductController::class, 'saveProductImage']);
    Route::get('change-product-default-image', [ProductController::class, 'UpdateDefaultImage']);
    Route::delete('delete-product-image/{id}', [ProductController::class, 'deleteProductImage']);

    Route::get('orders', [AdminOrderController::class, 'index']);
    Route::get('order/{id}', [AdminOrderController::class, 'detail']);
    Route::post('update-order/{id}', [AdminOrderController::class, 'updateOrder']);

    Route::get('get-shipping', [ShippingController::class, 'getShipping']);
    Route::post('save-shipping', [ShippingController::class, 'updateShipping']);
});

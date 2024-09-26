<?php

use App\Http\Controllers\CartsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ProductsController;
use App\Http\Middleware\CheckUsertype;
use App\Models\Carts;
use App\Models\Orders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [CustomerController::class, 'login']);

// //categories
// Route::prefix('categories')->group(function(){
// Route::get('/',[CategoriesController::class,'fetch']);
// Route::get('/{id}',[CategoriesController::class,'fetchID']);
// Route::post('/',[CategoriesController::class,'create']);
// Route::put('/{id}',[CategoriesController::class,'update']);
// Route::delete('/{id}',[CategoriesController::class,'delete']);
// });


// //products
// Route::prefix('products')->group(function(){
// Route::get('/',[ProductsController::class,'fetch']);
// Route::get('/{id}',[ProductsController::class,'fetchID']);
// Route::post('/',[ProductsController::class,'create']);
// Route::put('/{id}',[ProductsController::class,'update']);
// Route::delete('/{id}',[ProductsController::class,'delete']);
// });

// //carts
// Route::prefix('cart')->group(function(){
// Route::get('/{customer_id}',[CartsController::class,'fetch']);
// Route::post('/add',[CartsController::class,'create']);
// Route::put('/update',[CartsController::class,'update']);
// Route::delete('/remove',[CartsController::class,'delete']);
// });

// //orders
// Route::prefix('orders')->group(function(){
// Route::post('/',[OrdersController::class,'createOrder']);
// Route::get('/{customer_id}',[OrdersController::class,'fetch']);
// Route::get('/customer/{order_id}',[OrdersController::class,'fetchSingleOrder']);
// //retrive shipping address for specific order
// Route::get('/{order_id}/shipping_address',[OrdersController::class,'fetchShippingAddress']);
// });


Route::middleware([CheckUsertype::class . ':admin'])->group(function () {
    //categories
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoriesController::class, 'fetch']);
        Route::get('/{id}', [CategoriesController::class, 'fetchID']);
        Route::post('/', [CategoriesController::class, 'create']);
        Route::put('/{id}', [CategoriesController::class, 'update']);
        Route::delete('/{id}', [CategoriesController::class, 'delete']);
    });


    //products
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductsController::class, 'fetch']);
        Route::get('/{id}', [ProductsController::class, 'fetchID']);
        Route::post('/', [ProductsController::class, 'create']);
        Route::put('/{id}', [ProductsController::class, 'update']);
        Route::delete('/{id}', [ProductsController::class, 'delete']);
    });

    //orders
    Route::prefix('orders')->group(function () {
        Route::get('/{customer_id}', [OrdersController::class, 'fetch']);
        Route::get('/customer/{order_id}', [OrdersController::class, 'fetchSingleOrder']);
        //retrive shipping address for specific order
        Route::get('/{order_id}/shipping_address', [OrdersController::class, 'fetchShippingAddress']);
    });
});


Route::middleware([CheckUsertype::class . ':user'])->group(function () {
    //categories
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoriesController::class, 'fetch']);
        Route::get('/{id}', [CategoriesController::class, 'fetchID']);
    });

    //products
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductsController::class, 'fetch']);
        Route::get('/{id}', [ProductsController::class, 'fetchID']);
    });

    //carts
    Route::prefix('cart')->group(function () {
        Route::get('/{customer_id}', [CartsController::class, 'fetch']);
        Route::post('/add', [CartsController::class, 'create']);
        Route::put('/update', [CartsController::class, 'update']);
        Route::delete('/remove', [CartsController::class, 'delete']);
    });
});


//orders
Route::prefix('orders')->group(function () {
    Route::post('/', [OrdersController::class, 'createOrder']);
    Route::get('/{customer_id}', [OrdersController::class, 'fetch']);
    Route::get('/customer/{order_id}', [OrdersController::class, 'fetchSingleOrder']);
});

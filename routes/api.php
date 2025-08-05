
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\StoreController;


Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    // Authentication Routes
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

   Route::middleware('auth:sanctum')->group(function () {
    
        Route::get('user', [AuthController::class, 'user']);
        Route::post('logout', [AuthController::class, 'logout']);

        // User Management Routes (CRUD API)
        /*  | HTTP Method | Endpoint             | Action  | Controller Method |
            | ----------- | -------------------- | ------- | ----------------- |
            | GET         | `/api/v1/users`      | Index   | `index()`         |
            | GET         | `/api/v1/users/{id}` | Show    | `show()`          |
            | POST        | `/api/v1/users`      | Store   | `store()`         |
            | PUT/PATCH   | `/api/v1/users/{id}` | Update  | `update()`        |
            | DELETE      | `/api/v1/users/{id}` | Destroy | `destroy()`       | */
        Route::apiResource('users', UserController::class);

        // Brand Management Routes (CRUD API)
        /*  | HTTP Method | Endpoint               | Action  | Controller Method |
            | ----------- | ----------------------| ------- | ----------------- |
            | GET         | `/api/v1/brands`      | Index   | `index()`         |
            | GET         | `/api/v1/brands/{id}` | Show    | `show()`          |
            | POST        | `/api/v1/brands`      | Store   | `store()`         |
            | PUT/PATCH   | `/api/v1/brands/{id}` | Update  | `update()`        |
            | DELETE      | `/api/v1/brands/{id}` | Destroy | `destroy()`       | */
        Route::apiResource('brands', BrandController::class);

        // Category Management Routes (CRUD API)
        /*  | HTTP Method | Endpoint                  | Action  | Controller Method |
            | ----------- | -------------------------| ------- | ----------------- |
            | GET         | `/api/v1/categories`      | Index   | `index()`         |
            | GET         | `/api/v1/categories/{id}` | Show    | `show()`          |
            | POST        | `/api/v1/categories`      | Store   | `store()`         |
            | PUT/PATCH   | `/api/v1/categories/{id}` | Update  | `update()`        |
            | DELETE      | `/api/v1/categories/{id}` | Destroy | `destroy()`       | */
        Route::apiResource('categories', CategoryController::class);

        // Product Management Routes (CRUD API)
        /*  | HTTP Method | Endpoint                  | Action  | Controller Method |
            | ----------- | -------------------------| ------- | ----------------- |
            | GET         | `/api/v1/products`      | Index   | `index()`         |
            | GET         | `/api/v1/products/{id}` | Show    | `show()`          |
            | POST        | `/api/v1/products`      | Store   | `store()`         |
            | PUT/PATCH   | `/api/v1/products/{id}` | Update  | `update()`        |
            | DELETE      | `/api/v1/products/{id}` | Destroy | `destroy()`       | */
        Route::apiResource('products', ProductController::class);

        // Coupon Management Routes (CRUD API)
        /*  | HTTP Method | Endpoint                  | Action  | Controller Method |
            | ----------- | -------------------------| ------- | ----------------- |
            | GET         | `/api/v1/coupons`      | Index   | `index()`         |
            | GET         | `/api/v1/coupons/{id}` | Show    | `show()`          |
            | POST        | `/api/v1/coupons`      | Store   | `store()`         |
            | PUT/PATCH   | `/api/v1/coupons/{id}` | Update  | `update()`        |
            | DELETE      | `/api/v1/coupons/{id}` | Destroy | `destroy()`       | */
        Route::apiResource('coupons', CouponController::class);

        // Customer Management Routes (CRUD API)
        /*  | HTTP Method | Endpoint                  | Action  | Controller Method |
            | ----------- | -------------------------| ------- | ----------------- |
            | GET         | `/api/v1/customers`      | Index   | `index()`         |
            | GET         | `/api/v1/customers/{id}` | Show    | `show()`          |
            | POST        | `/api/v1/customers`      | Store   | `store()`         |
            | PUT/PATCH   | `/api/v1/customers/{id}` | Update  | `update()`        |
            | DELETE      | `/api/v1/customers/{id}` | Destroy | `destroy()`       | */
        Route::apiResource('customers', CustomerController::class);

        // Order Management Routes (CRUD API)
        /*  | HTTP Method | Endpoint                  | Action  | Controller Method |
            | ----------- | -------------------------| ------- | ----------------- |
            | GET         | `/api/v1/orders`      | Index   | `index()`         |
            | GET         | `/api/v1/orders/{id}` | Show    | `show()`          |
            | POST        | `/api/v1/orders`      | Store   | `store()`         |
            | PUT/PATCH   | `/api/v1/orders/{id}` | Update  | `update()`        |
            | DELETE      | `/api/v1/orders/{id}` | Destroy | `destroy()`       | */
        Route::apiResource('orders', OrderController::class);

        // Favorite Management Routes (CRUD API)
        /*  | HTTP Method | Endpoint                  | Action  | Controller Method |
            | ----------- | -------------------------| ------- | ----------------- |
            | GET         | `/api/v1/favorites`      | Index   | `index()`         |
            | GET         | `/api/v1/favorites/{id}` | Show    | `show()`          |
            | POST        | `/api/v1/favorites`      | Store   | `store()`         |
            | PUT/PATCH   | `/api/v1/favorites/{id}` | Update  | `update()`        |
            | DELETE      | `/api/v1/favorites/{id}` | Destroy | `destroy()`       | */
        Route::apiResource('favorites', FavoriteController::class);

        // Supplier Management Routes (CRUD API)
        /*  | HTTP Method | Endpoint                  | Action  | Controller Method |
            | ----------- | -------------------------| ------- | ----------------- |
            | GET         | `/api/v1/suppliers`      | Index   | `index()`         |
            | GET         | `/api/v1/suppliers/{id}` | Show    | `show()`          |
            | POST        | `/api/v1/suppliers`      | Store   | `store()`         |
            | PUT/PATCH   | `/api/v1/suppliers/{id}` | Update  | `update()`        |
            | DELETE      | `/api/v1/suppliers/{id}` | Destroy | `destroy()`       | */
        Route::apiResource('suppliers', SupplierController::class);

        // Review Management Routes (CRUD API)
        /*  | HTTP Method | Endpoint                  | Action  | Controller Method |
            | ----------- | -------------------------| ------- | ----------------- |
            | GET         | `/api/v1/reviews`      | Index   | `index()`         |
            | GET         | `/api/v1/reviews/{id}` | Show    | `show()`          |
            | POST        | `/api/v1/reviews`      | Store   | `store()`         |
            | PUT/PATCH   | `/api/v1/reviews/{id}` | Update  | `update()`        |
            | DELETE      | `/api/v1/reviews/{id}` | Destroy | `destroy()`       | */
        Route::apiResource('reviews', ReviewController::class);

        // Store Management Routes (CRUD API)
        /*  | HTTP Method | Endpoint                  | Action  | Controller Method |
            | ----------- | -------------------------| ------- | ----------------- |
            | GET         | `/api/v1/stores`      | Index   | `index()`         |
            | GET         | `/api/v1/stores/{id}` | Show    | `show()`          |
            | POST        | `/api/v1/stores`      | Store   | `store()`         |
            | PUT/PATCH   | `/api/v1/stores/{id}` | Update  | `update()`        |
            | DELETE      | `/api/v1/stores/{id}` | Destroy | `destroy()`       | */
        Route::apiResource('stores', StoreController::class);
        // offer Management Routes (CRUD API)
        /*  | HTTP Method | Endpoint                  | Action  | Controller Method |
            | ----------- | -------------------------| ------- | ----------------- |
            | GET         | `/api/v1/offers`      | Index   | `index()`         |
            | GET         | `/api/v1/offers/{id}` | Show    | `show()`          |
            | POST        | `/api/v1/offers`      | Store   | `Store()`         |
            | PUT/PATCH   | `/api/v1/offers/{id}` | Update  | `update()`        |
            | DELETE      | `/api/v1/offers/{id}` | Destroy | `destroy()`       | */
        Route::apiResource('offers', OfferController::class);
    });
});

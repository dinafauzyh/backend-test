<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    //Admin
    Route::group(['middleware' => 'role:Admin', 'prefix' => 'admin'], function () {
        //CRUD User
        Route::apiResource('users', UserController::class);
        //CRUD Product
        Route::apiResource('products', ProductController::class)->except('update');
        Route::post('/products/{product}', [ProductController::class, 'update']);
    });

    //User
    Route::group(['middleware' => 'role:Customer'], function () {
        //Cart
        Route::apiResource('carts', CartController::class)->except('destroy');
        Route::delete('carts/{cart?}', [CartController::class, 'destroy']);
        //Transaction
        Route::apiResource('transactions', TransactionController::class)->except(['index', 'destroy']);
    });
});

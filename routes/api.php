<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('bling')->group(function () {
    Route::get('/', [App\Http\Controllers\BlingController::class, 'blingApi']);
    Route::get('/get/produtos', [App\Http\Controllers\BlingController::class, 'getProducts']);
    Route::get('/create/produto/{codigo}', [App\Http\Controllers\BlingController::class, 'getProductById']);
    Route::post('/create/product', [App\Http\Controllers\BlingController::class, 'createProduct']);

    Route::post('/callback/inventory', [App\Http\Controllers\BlingController::class, 'inventoryCallback']);
    Route::post('/callback/product', [App\Http\Controllers\BlingController::class, 'productCallback']);

    Route::post('/create/order', [App\Http\Controllers\OrdersController::class, 'createOrder']);
    Route::post('/update/order', [App\Http\Controllers\OrdersController::class, 'updateOrder']);
    Route::post('/callback/order', [App\Http\Controllers\OrdersController::class, 'orderCallback']);
});
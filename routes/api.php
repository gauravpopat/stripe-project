<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


//Customer :

Route::controller(CustomerController::class)->prefix('customer')->group(function () {
    Route::post('create', 'create');
    Route::post('update/{customer_id}', 'update');
    Route::post('delete/{customer_id}', 'delete');
});

// Product :

Route::controller(ProductController::class)->prefix('product')->group(function () {
    Route::post('create', 'create');
    Route::post('update/{product_id}', 'update');
    Route::post('delete/{product_id}', 'delete');
});

// Plan :

Route::controller(PlanController::class)->prefix('plan')->group(function(){
    Route::post('create', 'create');
    Route::post('update/{plan_id}', 'update');
    Route::post('delete/{plan_id}', 'delete');
});

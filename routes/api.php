<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\IncomeGroupController;


//Public routes
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

//Protected routes
Route::middleware('auth:sanctum')->group(function() {
    Route::post('/logout', [UserController::class, 'logout']);

    Route::post('/accounts', [AccountController::class, 'store']);
    Route::get('/accounts', [AccountController::class, 'index']);

    //Protected routes with check are you owner of account
    Route::middleware('can:view,account')->group(function() {
        //Account routes
        Route::apiResource('/accounts', AccountController::class)->except(['store', 'index']);
      
        //Income groups routes
        Route::apiResource('/accounts/{account}/income-groups', IncomeGroupController::class);
        Route::apiResource('/accounts/{account}/income-groups/{income_group}/incomes', IncomeController::class);
    });
});
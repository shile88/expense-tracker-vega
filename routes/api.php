<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\ExpenseGroupController;
use App\Http\Controllers\Api\IncomeController;
use App\Http\Controllers\Api\IncomeGroupController;


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
      
        //Incomes routes
        Route::apiResource('/accounts/{account}/income-groups', IncomeGroupController::class);
        Route::apiResource('/accounts/{account}/income-groups/{income_group}/incomes', IncomeController::class);

        //Expenses routes
        Route::apiResource('/accounts/{account}/expense-groups', ExpenseGroupController::class);
        Route::apiResource('/accounts/{account}/expense-groups/{expense_group}/expenses', ExpenseController::class);
    });
});


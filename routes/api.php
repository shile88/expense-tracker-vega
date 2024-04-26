<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\BalanceReportController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\ExpenseGroupController;
use App\Http\Controllers\Api\IncomeController;
use App\Http\Controllers\Api\IncomeGroupController;
use App\Http\Controllers\Api\SavingController;
use App\Http\Controllers\Api\UserController;
use App\Http\Middleware\CheckIsPremiumUser;
use Illuminate\Support\Facades\Route;

//Public routes
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

//Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);

    Route::post('/accounts', [AccountController::class, 'store']);
    Route::get('/accounts', [AccountController::class, 'index']);

    //Protected routes with check are you owner of account
    Route::middleware('can:view,account')->group(function () {
        //Account routes
        Route::apiResource('/accounts', AccountController::class)->except(['store', 'index']);

        //Active reminder route
        Route::post('/accounts/{account}/reminders', [UserController::class, 'activateReminder']);

        //Incomes routes
        Route::apiResource('/accounts/{account}/income-groups', IncomeGroupController::class);
        Route::apiResource('/accounts/{account}/income-groups/{income_group}/incomes', IncomeController::class);

        //Expenses routes
        Route::apiResource('/accounts/{account}/expense-groups', ExpenseGroupController::class);
        Route::apiResource('/accounts/{account}/expense-groups/{expense_group}/expenses', ExpenseController::class);

        //Account report routes
        Route::get('/accounts/{account}/report', [BalanceReportController::class, 'balanceReport']);

        //Savings account
        Route::post('/accounts/{account}/savings', [SavingController::class, 'create'])->middleware(CheckIsPremiumUser::class);
    });
});

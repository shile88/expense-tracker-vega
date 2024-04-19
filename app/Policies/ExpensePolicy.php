<?php

namespace App\Policies;

use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class ExpensePolicy
{
    public function create(User $user, StoreExpenseRequest $request)
    {
       return $this->checkIncomeStoreOrUpdatePermission($user, $request);
    }

    public function update(User $user, Expense $expense, UpdateExpenseRequest $request)
    {
        return $this->checkIncomeStoreOrUpdatePermission($user, $request);
    }

    public function checkIncomeStoreOrUpdatePermission($user, $request)
    {
        if($user->type === 'basic' && ($request->input('schedule_id') != 1)) {
            Log::info('User type basic can only schedule expense with schedule_id: 1', ['user_id' => $user->id]);
            return Response::deny('User can only schedule expense with schedule_id: 1');
        }

        // if($user->type === 'premium' && $request->has('schedule_id') && $request->has('expense_date')) {
        //     return Response::deny('Choose one value between schedule_id and expense_date');
        // }
        Log::info('User allowed to schedule with any schedule_id', ['user_id' => $user->id]);
        return true;
    }
}

<?php

namespace App\Policies;

use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ExpensePolicy
{
    public function create(User $user, StoreExpenseRequest $request)
    {
       return $this->checkIncomeStoreOrUpdatePermission($user, $request);
    }

    public function update(User $user, Expense $income, UpdateExpenseRequest $request)
    {
        return $this->checkIncomeStoreOrUpdatePermission($user, $request);
    }

    public function checkIncomeStoreOrUpdatePermission($user, $request)
    {
        if($user->type === 'basic' && ($request->has('schedule_id') || $request->has('expense_date'))) {
            return Response::deny('You do not have permission to schedule or set date for expense');
        }

        if($user->type === 'premium' && $request->has('schedule_id') && $request->has('expense_date')) {
            return Response::deny('Choose one value between schedule_id and expense_date');
        }

        return true;
    }
}

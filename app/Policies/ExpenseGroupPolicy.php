<?php

namespace App\Policies;

use App\Http\Requests\StoreExpenseGroupRequest;
use App\Http\Requests\UpdateExpenseGroupRequest;
use App\Models\ExpenseGroup;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class ExpenseGroupPolicy
{
    public function create(User $user, StoreExpenseGroupRequest $request)
    {
       return $this->checkExpenseGroupStoreOrUpdatePermission($user, $request);
    }

    public function update(User $user, ExpenseGroup $expenseGroup, UpdateExpenseGroupRequest $request)
    {
        return $this->checkExpenseGroupStoreOrUpdatePermission($user, $request);
    }

    public function checkExpenseGroupStoreOrUpdatePermission($user, $request)
    {
        if($user->type === 'basic' && ($request->input('group_budget'))) {
            Log::info('User type basic cant add group budget', ['user_id' => $user->id]);
            return Response::deny('User cant add group budget');
        }

        // if($user->type === 'premium' && $request->has('schedule_id') && $request->has('expense_date')) {
        //     return Response::deny('Choose one value between schedule_id and expense_date');
        // }
        Log::info('Premium user allowed to add group budget', ['user_id' => $user->id]);
        return true;
    }
}

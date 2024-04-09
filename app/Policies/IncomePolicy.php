<?php

namespace App\Policies;

use App\Http\Requests\StoreIncomeRequest;
use App\Http\Requests\UpdateIncomeRequest;
use App\Models\Income;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class IncomePolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, StoreIncomeRequest $request)
    {
       return $this->checkIncomeStoreOrUpdatePermission($user, $request);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Income $income, UpdateIncomeRequest $request)
    {
        return $this->checkIncomeStoreOrUpdatePermission($user, $request);
    }

    public function checkIncomeStoreOrUpdatePermission($user, $request)
    {
        if($user->type === 'basic' && ($request->has('schedule_id') || $request->has('income_date'))) {
            return Response::deny('You do not have permission to schedule or set date for income');
        }

        if($user->type === 'premium' && $request->has('schedule_id') && $request->has('income_date')) {
            return Response::deny('Choose one value between schedule_id and income_date');
        }

        return true;
    }
}

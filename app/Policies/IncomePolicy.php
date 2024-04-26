<?php

namespace App\Policies;

use App\Http\Requests\StoreIncomeRequest;
use App\Http\Requests\UpdateIncomeRequest;
use App\Models\Income;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

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
        if ($user->type === 'basic' && ($request->input('schedule_id') != 1)) {
            Log::info('User type basic can only schedule income with schedule_id: 1', ['user_id' => $user->id]);

            return Response::deny('User can only schedule income with schedule_id: 1');
        }

        // if($user->type === 'premium' && $request->has('schedule_id') && $request->has('end_date')) {
        //     return Response::deny('Choose one value between schedule_id and income_date');
        // }
        Log::info('User allowed to schedule with any schedule_id', ['user_id' => $user->id]);

        return true;
    }
}

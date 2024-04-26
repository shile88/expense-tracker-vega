<?php

namespace App\Policies;

use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\Account;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class AccountPolicy
{
    public function before(): ?bool
    {
        if (auth()->user()->is_admin) {
            return true;
        }

        return null;
    }

    public function view(User $user, Account $account)
    {
        if ($user->id === $account->user_id) {
            Log::info('User allowed to see this account');

            return Response::allow();
        } else {
            Log::info('User not allowed to see this account', ['user_id' => $user->id]);

            return Response::deny('This is not your account');
        }
    }

    public function create(User $user, StoreAccountRequest $request)
    {
        return $this->checkAccountStoreOrUpdatePermission($user, $request);
    }

    public function update(User $user, Account $account, UpdateAccountRequest $request)
    {
        return $this->checkAccountStoreOrUpdatePermission($user, $request);
    }

    public function checkAccountStoreOrUpdatePermission($user, $request)
    {
        if ($user->type === 'basic' && ($request->has('expense_end_date') || $request->has('expense_budget'))) {
            Log::info('User type basic do not have permission to add expense_end_date or expense_budget fields', ['user_id' => $user->id]);

            return Response::deny('You do not have permission to add expense_end_date or expense_budget fields');
        }

        Log::info('User type premium can add expense_end_date or expense_budget fields', ['user_id' => $user->id]);

        return true;
    }
}

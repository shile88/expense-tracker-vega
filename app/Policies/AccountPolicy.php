<?php

namespace App\Policies;

use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\Account;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AccountPolicy
{
    public function before(): bool|null
    {
        if (auth()->user()->is_admin) {
            return true;
        }

        return null;
    }

    public function view(User $user, Account $account)
    {
        return $user->id === $account->user_id
            ? Response::allow() : Response::deny('This is not your account');
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
        if($user->type === 'basic' && ($request->has('expense_end_date') || $request->has('expense_budget'))) {
            return Response::deny('You do not have permission to add expense_end_date or expense_budget fields');
        }

        return true;
    }
}

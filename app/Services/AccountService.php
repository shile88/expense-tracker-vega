<?php

namespace App\Services;

use App\Models\Account;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class AccountService
{

    public function index(): LengthAwarePaginator
    {
        return Account::where('user_id', auth()->id())->paginate(5);
    }

    public function store(array $validatedRequest): Account
    {
        $newAccount = Account::create([
            'user_id' => auth()->id(),
            ...$validatedRequest
        ]);

        Log::info('User successfully created account', ['user' => auth()->id(), 'account' => $newAccount->id]);

        return $newAccount;
    }

    public function show(Account $account): Account
    {
        return $account;
    }

    public function update(array $validatedRequest, Account $account): Account
    {
        $account->update($validatedRequest);

        return $account;
    }

    public function delete($account): void
    {
        $account->delete();

        Log::info('User deleted account', ['user' => auth()->id(), 'account' => $account->id]);
    }
}

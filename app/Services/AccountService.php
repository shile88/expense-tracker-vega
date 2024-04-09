<?php

namespace App\Services;

use App\Models\Account;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AccountService {

    public function index(): LengthAwarePaginator
    {
       return Account::where('user_id', auth()->id())->paginate(5);
    }

    public function store(array $validatedRequest): Account
    {
        return Account::create($validatedRequest);
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
    }
}
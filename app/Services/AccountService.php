<?php

namespace App\Services;

use App\Models\Account;

class AccountService {

    public function index()
    {
       return Account::where('user_id', auth()->id())->paginate(5);
    }

    public function store($request)
    {
        return Account::create($request->validated());
    }

    public function show($account)
    {
        return $account;
    }

    public function update($request, $account)
    {
        $account->update($request->validated());

        return $account;
    }

    public function delete($account)
    {
        $account->delete();
    }
}
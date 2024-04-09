<?php

namespace App\Services;

use App\Models\Account;
use App\Models\ExpenseGroup;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ExpenseGroupService
{
    public function index(Account $account): LengthAwarePaginator
    {
        return ExpenseGroup::where('account_id', $account->id)->paginate(5);
    }

    public function store(array $validatedRequest, Account $account): ExpenseGroup
    {
        return ExpenseGroup::create([
            'name' => $validatedRequest['name'],
            'group_budget' => $validatedRequest['group_budget'],
            'account_id' => $account->id
        ]);
    }

    public function show($expenseGroup): ExpenseGroup
    {
        return $expenseGroup;
    }

    public function update($validatedRequest, $expenseGroup): ExpenseGroup
    {
        $expenseGroup->update($validatedRequest);

        return $expenseGroup;
    }

    public function delete($expenseGroup): void
    {
        $expenseGroup->delete();
    }
}
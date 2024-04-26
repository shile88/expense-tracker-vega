<?php

namespace App\Services;

use App\Models\Account;
use App\Models\ExpenseGroup;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class ExpenseGroupService
{
    public function index(Account $account): LengthAwarePaginator
    {
        return ExpenseGroup::where('account_id', $account->id)->paginate(5);
    }

    public function store(array $validatedRequest, Account $account): ExpenseGroup
    {
        $expenseGroup = ExpenseGroup::create([
            'account_id' => $account->id,
            ...$validatedRequest,
        ]);

        Log::info('New expense group created', ['user_id' => auth()->id(), 'data' => $expenseGroup]);

        return $expenseGroup;
    }

    public function show(ExpenseGroup $expenseGroup): ExpenseGroup
    {
        return $expenseGroup;
    }

    public function update(array $validatedRequest, ExpenseGroup $expenseGroup): ExpenseGroup
    {
        $expenseGroup->update($validatedRequest);

        return $expenseGroup;
    }

    public function delete(ExpenseGroup $expenseGroup): void
    {
        $expenseGroup->delete();

        Log::info('User deleted expense group', ['user_id' => auth()->id(), 'expense_group_id' => $expenseGroup->id]);
    }
}

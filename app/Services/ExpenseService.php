<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Expense;
use App\Models\ExpenseGroup;
use Illuminate\Pagination\LengthAwarePaginator;

class ExpenseService
{
    public function index(Account $account, ExpenseGroup $expenseGroup): LengthAwarePaginator
    {
        return Expense::where('expense_group_id', $expenseGroup->id)->paginate(5);
    }

    public function store($validatedRequest, $account, $expenseGroup): Expense
    {
        return Expense::create([
            'amount' => $validatedRequest['amount'],
            'schedule_id' => $validatedRequest['schedule_id'] ?? null,
            'expense_date' => $validatedRequest['expense_date'] ?? null,
            'account_id' => $account->id,
            'expense_group_id' => $expenseGroup->id
        ]);
    }

    public function show($expense): Expense
    {
        return $expense;
    }

    public function update($validatedRequest, $expense)
    {
        $expense->update($validatedRequest);

        return $expense;
    }

    public function delete($expense): void
    {
        $expense->delete();
    }
}
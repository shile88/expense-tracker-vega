<?php

namespace App\Services;

use App\Events\ExpenseGroupBudgetCap;
use App\Models\Account;
use App\Models\Expense;
use App\Models\ExpenseGroup;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class ExpenseService
{
    public function index(Account $account, ExpenseGroup $expenseGroup): LengthAwarePaginator
    {
        return Expense::where('expense_group_id', $expenseGroup->id)->paginate(5);
    }

    public function store(array $validatedRequest, ExpenseGroup $expenseGroup): Expense
    {
        $expense = Expense::create([
            'expense_group_id' => $expenseGroup->id,
            ...$validatedRequest,
        ]);

        Log::info('New expense created', ['user_id' => auth()->id(), 'data' => $expense]);

        ExpenseGroupBudgetCap::dispatch($expense);

        return $expense;
    }

    public function show(Expense $expense): Expense
    {
        return $expense;
    }

    public function update(array $validatedRequest, Expense $expense)
    {
        $expense->update($validatedRequest);

        return $expense;
    }

    public function delete(Expense $expense): void
    {
        $expense->delete();

        Log::info('User deleted expense', ['user_id' => auth()->id(), 'expense_id' => $expense->id]);
    }

    public function checkExpenseBudget($expense)
    {
        $account = $expense->incomeGroup->account;
        $expenseBudgetStartDate = $account->expense_start_date;
        $expenseBudgetEndDate = $account->expense_end_date;
        $user = $account->user;

        $query = Expense::query()->leftJoin('expense_groups', 'expenses.expense_group_id', '=', 'expense_groups.id')
            ->where('account_id', $account->id);
    
            if ($expenseBudgetStartDate && $expenseBudgetEndDate) {
                $query->whereBetween('created_at', [$expenseBudgetStartDate, $expenseBudgetEndDate]);
            } elseif ($expenseBudgetStartDate) {
                $query->where('created_at', '>=', $expenseBudgetStartDate);
            } elseif ($expenseBudgetEndDate) {
                $query->where('created_at', '<=', $expenseBudgetEndDate);
            }

        $totalExpense = $query->sum('amount');

        if ($totalExpense && $totalExpense >= $account->expense_budget) {
            return [
                'totalExpense' => $totalExpense,
                'expenseBudgetStartDate' => $expenseBudgetStartDate,
                'expenseBudgetEndDate' => $expenseBudgetEndDate,
                'accountId' => $account->id,
                'accountBudget' => $account->expense_budget,
                'user' => $user
            ];
        } else {
            return null;
        }
    }
}

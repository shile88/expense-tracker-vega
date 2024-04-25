<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Expense;
use App\Models\Income;
use App\Models\User;

class BalanceReportService
{
    public function balanceReport($validatedRequest, Account $account)
    {
        // $url = request()->url();
        // $queryParams = request()->query();

        // ksort($queryParams);

        // $queryString = http_build_query($queryParams);

        // $fullUrl = "{$url}?{$queryString}";

        $incomes = null;
        $expenses = null;

        if (empty($validatedRequest['type'])) {
            $incomes = Income::query()->search($validatedRequest, $account)->paginate(10);
            $expenses = Expense::query()->search($validatedRequest, $account)->paginate(10);
        } else {
            if ($validatedRequest['type'] === 'income') {
                $incomes = Income::query()->search($validatedRequest, $account)->paginate(10);
            } else {
                $expenses = Expense::query()->search($validatedRequest, $account)->paginate(10);
            }
        }

        return [$incomes, $expenses];
    }

    public function calculateExpenseBudgetCap($expense)
    {
        $expensesSum = Expense::leftJoin('expense_groups', 'expenses.expense_group_id', '=', 'expense_groups.id')
            ->where('expense_groups.id', $expense->expense_group_id)->sum('amount');

        $expenseGroupBudget = $expense->expenseGroup->group_budget;

        if ($expenseGroupBudget !== 0 && $expensesSum >= $expenseGroupBudget) {
            return [$expensesSum, $expenseGroupBudget];
        }

    }

    public function sendWeeklyAndMonthlyEmail()
    {
        $startDate = now()->subWeek()->startOfWeek();
        $endDate = now()->subWeek()->endOfWeek();

        $isMonthly = now()->day == 1;
        if ($isMonthly) {
            $startDate = now()->subMonth()->startOfMonth();
            $endDate = now()->subMonth()->endOfMonth();
        }

        $allAccounts = [];

        $users = User::where('type', 'premium')->where('reminder', 1)->with('accounts.incomeGroups.incomes', 'accounts.expenseGroups.expenses')->get();

        foreach ($users as $user) {
            $userAccounts = $user->accounts->map(function ($account) use ($startDate, $endDate) {
                $totalIncome = $account->incomeGroups->flatMap->incomes->whereBetween('created_at', [$startDate, $endDate])->sum('amount');
                $totalExpense = $account->expenseGroups->flatMap->expenses->whereBetween('created_at', [$startDate, $endDate])->sum('amount');

                $account->totalIncome = $totalIncome;
                $account->totalExpense = $totalExpense;

                return $account;
            });

            $allAccounts = array_merge($allAccounts, $userAccounts->toArray());
        }

        return $allAccounts;
    }
}

<?php

namespace App\Observers;

use App\Models\Expense;
use App\Notifications\TotalExpenseBudgetExceeded;
use App\Services\ExpenseService;

class ExpenseObserver
{
    /**
     * Handle the Expense "created" event.
     */
    public function created(Expense $expense, ExpenseService $expenseService): void
    {
        $this->handleExpenseBudgetExceeded($expense, $expenseService);
    }

    /**
     * Handle the Expense "updated" event.
     */
    public function updated(Expense $expense, ExpenseService $expenseService): void
    {
        $this->handleExpenseBudgetExceeded($expense, $expenseService);
    }

    private function handleExpenseBudgetExceeded(Expense $expense, ExpenseService $expenseService): void
    {
        $data = $expenseService->checkExpenseBudget($expense);

        if ($data !== null) {
            $totalExpense = $data['totalExpense'];
            $expenseBudgetStartDate = $data['expenseBudgetStartData'];
            $expenseBudgetEndDate = $data['expenseBudgetEndDate'];
            $accountId = $data['accountId'];
            $accountBudget = $data['accountBudget'];
            $data['user']->notify(new TotalExpenseBudgetExceeded($totalExpense, $expenseBudgetStartDate, $expenseBudgetEndDate, $expense, $accountId, $accountBudget));
        }
    }
}

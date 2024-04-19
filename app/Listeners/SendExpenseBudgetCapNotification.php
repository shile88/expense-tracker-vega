<?php

namespace App\Listeners;

use App\Events\ExpenseGroupBudgetCap;
use App\Notifications\ExpenseGroupBudgetExceeded;
use App\Services\BalanceReportService;
use Illuminate\Support\Facades\Notification;

class SendExpenseBudgetCapNotification
{
    /**
     * Create the event listener.
     */
    public function __construct(protected BalanceReportService $balanceReportService)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ExpenseGroupBudgetCap $event): void
    {
        $data = $this->balanceReportService->calculateExpenseBudgetCap($event->expense);

        if ($data) {
            [$expensesSum, $expenseGroupBudget] = $data;
            Notification::send(auth()->user(), new ExpenseGroupBudgetExceeded($expensesSum, $expenseGroupBudget, $event->expense, $event->expense->expenseGroup->name));
        }
    }
}

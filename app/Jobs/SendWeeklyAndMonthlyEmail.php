<?php

namespace App\Jobs;

use App\Notifications\WeeklyOrMonthlyEmailNotification;
use App\Services\BalanceReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWeeklyAndMonthlyEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(BalanceReportService $balanceReportService): void
    {
        $allAccounts = $balanceReportService->sendWeeklyAndMonthlyEmail();

        foreach ($allAccounts as $account) {
            $account->user->notify(new WeeklyOrMonthlyEmailNotification(
                $account->user,
                $account->totalIncome,
                $account->totalExpense,
                $account->balance,
                $account->id,
                $account->type,
                $account->totalIncome - $account->totalExpense
            ));
        }
    }
}

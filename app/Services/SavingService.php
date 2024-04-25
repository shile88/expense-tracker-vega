<?php

namespace App\Services;

use App\Models\MonthlySaving;
use App\Models\Saving;
use App\Notifications\InsufficientBalanceNotification;
use App\Notifications\SaveGoalReached;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SavingService
{
    public function createSaving($validatedRequest, $account)
    {
        $endDate = $validatedRequest['save_end_date'];
        $currentDate = Carbon::now();
        $currentDate->startOfMonth()->addMonth();

        $totalIncome = $account->incomeGroups->flatMap->incomes->where('end_date', '<=',  $endDate)->sum('amount');
        $totalExpense = $account->expenseGroups->flatMap->expenses->where('end_date', '<=',  $endDate)->sum('amount');
        $totalSavingsByMonth = MonthlySaving::query()
            ->leftJoin('savings', 'monthly_savings.saving_id', '=', 'savings.id')
            ->where('account_id', $account->id)
            ->where('month', $currentDate->format('F'))->sum('amount');
        $net = $totalIncome - $totalExpense - $totalSavingsByMonth;

        $months = [];

        while ($currentDate->lessThanOrEqualTo($endDate)) {
            $months[] = $currentDate->format('F');
            $currentDate->addMonth();
        }

        if ($net > $validatedRequest['save_goal']) {
            $saveAmountByMonth = $validatedRequest['save_goal'] / count($months);
            $saving = Saving::create([
                'account_id' => $account->id,
                ...$validatedRequest
            ]);
            foreach ($months as $month) {
                MonthlySaving::create([
                    'saving_id' => $saving->id,
                    'amount' => $saveAmountByMonth,
                    'month' => $month
                ]);
            }
            return $saving;
        } else {
            return false;
        }
    }

    public function processMonthlySaving()
    {
        $currentDate = Carbon::now();
        $monthlySavings = MonthlySaving::where('is_completed', false)->where('month', $currentDate->format('F'))->get();

        foreach ($monthlySavings as $monthlySaving) {
            DB::transaction(function () use ($monthlySaving) {
                $account = $monthlySaving->relatedSaving->account;

                if ($account->balance >= $monthlySaving->amount) {
                    $account->balance -= $monthlySaving->amount;
                    $monthlySaving->is_completed = true;
                    $monthlySaving->save();
                    $savingId = $monthlySaving->relatedSaving->id;
                    $saveGoal = $monthlySaving->relatedSaving->save_goal;

                    $isMonthlySavingCompleted = MonthlySaving::where('saving_id', $savingId)->where('is_completed', false)->exists();

                    if (!$isMonthlySavingCompleted) {
                        $account->update([
                            'balance' => $account->balance + $saveGoal
                        ]);
                        $account->user->notify(new SaveGoalReached($account->id, $account->balance, $saveGoal));
                    }
                } else {
                    $difference = $monthlySaving->amount - $account->balance;
                    $remainingMonthlySavings = MonthlySaving::where('saving_id', $monthlySaving->relatedSaving->id)->where('is_completed', false)->get();
                    $numberOfRemainingMonthlySavings = $remainingMonthlySavings->count();
                    $amountToAdd = $difference / $numberOfRemainingMonthlySavings;

                    foreach ($remainingMonthlySavings as $remainingMonthlySaving) {
                        $remainingMonthlySaving->update([
                            'amount' => $remainingMonthlySaving->amount + $amountToAdd
                        ]);
                        $account->user->notify(new InsufficientBalanceNotification(
                            $account->id,
                            $remainingMonthlySaving->id,
                            $difference,
                            $numberOfRemainingMonthlySavings,
                            $amountToAdd
                        ));
                    }
                }
            });
        }
    }
}

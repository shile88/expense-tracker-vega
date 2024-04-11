<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Income;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Collection;

class JobService
{
    public function createTransaction(): void
    {
        $incomes = $this->queryModel(Income::class);

        $expenses = $this->queryModel(Expense::class);

        foreach ($incomes as $income) {
            $account = $income->incomeGroup;

            if ($this->shouldAddToTransactions($income)) {
                $this->addToTransactions($income, $account);
            }
        }

        foreach ($expenses as $expense) {
            $account = $expense->expenseGroup;

            if ($this->shouldAddToTransactions($expense)) {
                $this->addToTransactions($expense, $account);
            }
        }
    }

    private function queryModel($model): Collection
    {
        return $model->where(function ($query) {
            $query->whereHas('schedule', function ($subQuery) {
                $subQuery->where('type', 'onetime');
            })
            ->whereDate('end_date', now()->toDateString());
        })
        ->orWhereHas('schedule', function ($subQuery) {
            $subQuery->where('type', '!=', 'onetime');
        })
        ->get();
    }

    private function shouldAddToTransactions($model): bool
    {
        $scheduleType = $model->schedule->type;

        switch ($scheduleType) {
            case 'onetime':
                return true;
            case 'daily':
                return empty($model->end_date) || $model->end_date <= now()->toDateString();
            case 'weekly':
                return (empty($model->end_date) || $model->end_date <= now()->toDateString()) &&
                    (Carbon::now()->dayOfWeek == $model->transaction_start);
            case 'monthly':
                return (empty($model->end_date) || $model->end_date <= now()->toDateString()) &&
                    (Carbon::now()->dayOfWeek == $model->transaction_start);
            default:
                return false;
        }
    }

    private function addToTransactions($model, $account): void
    {
        if ($model->schedule_id == 1)
            Transaction::firstOrCreate([
                'amount' => $model->amount,
                'account_id' => $account->account_id,
                'transactionable_id' => $model->id,
                'transactionable_type' => get_class($model)
            ]);
        else
            Transaction::create([
                'amount' => $model->amount,
                'account_id' => $account->account_id,
                'transactionable_id' => $model->id,
                'transactionable_type' => get_class($model)
            ]);
    }
}

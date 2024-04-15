<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Income;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class JobService
{
    public function createTransaction(): void
    {
        Log::info('Creating transactions process started');

        try {
            $incomes = $this->queryModel(new Income());
            Log::info('Successful income query');
    
            foreach ($incomes as $income) {
                $account = $income->incomeGroup;
    
                if ($this->shouldAddToTransactions($income)) {
                    $this->addToTransactions($income, $account);
                    Log::info('Added income to transactions', ['income_id' => $income->id]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error processing incomes', ['income_id'=>$income->id, 'error' => $e->getMessage()]);
        }

        try {
            $expenses = $this->queryModel(new Expense());
            Log::info('Successful expense query');
    
            foreach ($expenses as $expense) {
                $account = $expense->expenseGroup;
    
                if ($this->shouldAddToTransactions($expense)) {
                    $this->addToTransactions($expense, $account);
                    Log::info('Added expense to transactions', ['expense_id' => $expense->id]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error processing expenses', ['expense_id' => $expense->id,'error' => $e->getMessage()]);
        }
    }

    private function queryModel($model): Collection
    {
        Log::info('Querying model', ['model' => get_class($model)]);

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

        Log::info('Checking if should add to transactions', ['model_id' => $model->id, 'schedule_type' => $scheduleType]);

        $startOfMonth = $model->transaction_start == 0 ? Carbon::now()->startOfMonth()->toDateString() : null;
        $endOfMonth = $model->transaction_start == 1 ? Carbon::now()->endOfMonth()->toDateString() : null;

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
                    ($startOfMonth !== null && Carbon::now()->startOfMonth()->toDateString() == $startOfMonth ||
                        $endOfMonth !== null && Carbon::now()->endOfMonth()->toDateString() == $endOfMonth);
            default:
            Log::warning('Unknown schedule type', ['schedule_type' => $scheduleType]);
                return false;       
        }
    }

    private function addToTransactions($model, $account): void
    {
        if ($model->schedule->type == 'onetime')
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

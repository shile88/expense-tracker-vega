<?php

namespace App\Services;

use App\Jobs\UpdateAccountBalance;
use App\Models\Account;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JobService
{
    public function createTransaction(): void
    {
        Log::info('Creating transactions process started');

        $this->processIncomeOrExpenseToTransactions(new Income(), 'income');
        $this->processIncomeOrExpenseToTransactions(new Expense(), 'expense');
    }

    public function updateAccountBalance(array $transactionIds)
    {
        DB::transaction(function () use ($transactionIds) {
            $transactions = Transaction::whereIn('transactionable_id', $transactionIds)->whereDate('created_at', now()->toDateString())->get();

            foreach ($transactions as $transaction) {
                $account = $transaction->account;

                $newBalance = ($transaction->transactionable_type === 'App\Models\Income')
                    ? $account->balance + $transaction->amount
                    : $account->balance - $transaction->amount;

                $account->update(['balance' => $newBalance]);
                $transaction->update(['is_added_to_balance' => true]);
                Log::info('Account updated successfully', ['transaction_id' => $transaction->id, 'amount' => $transaction->amount]);
            }
        });
    }

    private function processIncomeOrExpenseToTransactions(Model $model, string $type): void
    {
        $incomeOrExpenseTransactions = $this->queryIncomeOrExpense($model);

        Log::info('Successful '.$type.' query');

        foreach ($incomeOrExpenseTransactions as $incomeOrExpenseTransaction) {
            $account = $incomeOrExpenseTransaction->{$type.'Group'}->account;

            try {
                if ($this->shouldAddIncomeOrExpenseToTransactions($incomeOrExpenseTransaction)) {
                    $this->addIncomeOrExpenseToTransactions($incomeOrExpenseTransaction, $account);
                    Log::info('Added '.$type.' to transactions', ['id' => $incomeOrExpenseTransaction->id]);
                    $transactionIds[] = $incomeOrExpenseTransaction->id;
                }
            } catch (\Exception $e) {
                Log::error('Error processing '.$type.'s', ['id' => $incomeOrExpenseTransaction->id, 'error' => $e->getMessage()]);
            }
        }

        if (! empty($transactionIds)) {
            Log::info('Starting to add '.$type.' amount to balance', ['id' => $transactionIds]);
            UpdateAccountBalance::dispatch($transactionIds, $account);
        }
    }

    private function queryIncomeOrExpense(Model $model): Collection
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

    private function shouldAddIncomeOrExpenseToTransactions(Model $model): bool
    {
        $scheduleType = $model->schedule->type;

        Log::info('Checking if should add to transactions', ['model' => get_class($model), 'id' => $model->id, 'schedule_type' => $scheduleType]);

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

    private function addIncomeOrExpenseToTransactions(Model $model, Account $account): void
    {
        if ($model->schedule->type == 'onetime') {
            if ($model->transactions()->isEmpty()) {
                Transaction::create([
                    'amount' => $model->amount,
                    'account_id' => $account->id,
                    'transactionable_id' => $model->id,
                    'transactionable_type' => get_class($model),
                ]);
            }
        } else {
            Transaction::create([
                'amount' => $model->amount,
                'account_id' => $account->id,
                'transactionable_id' => $model->id,
                'transactionable_type' => get_class($model),
            ]);
        }
    }
}

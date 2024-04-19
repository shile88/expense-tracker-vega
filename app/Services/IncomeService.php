<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Income;
use App\Models\IncomeGroup;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class IncomeService
{
    public function index(Account $account, IncomeGroup $incomeGroup): LengthAwarePaginator
    {
        return Income::where('income_group_id', $incomeGroup->id)->paginate(5);
    }

    public function store(array $validatedRequest, IncomeGroup $incomeGroup): Income
    {
        $income = Income::create([
            'amount' => $validatedRequest['amount'],
            'schedule_id' => $validatedRequest['schedule_id'] ?? null,
            'end_date' => $validatedRequest['end_date'] ?? null,
            'income_group_id' => $incomeGroup->id,
            'transaction_start' => $validatedRequest['transaction_start'] ?? null,
        ]);

        Log::info('New income created', ['user' => auth()->id(), 'data' => $income]);

        return $income;
    }

    public function show(Income $income): Income
    {
        return $income;
    }

    public function update(array $validatedRequest, Income $income): Income
    {
        $income->update($validatedRequest);

        return $income;
    }

    public function delete(Income $income): void
    {
        $income->delete();

        Log::info('User deleted income', ['user_id' => auth()->id(), 'income_id' => $income->id]);
    }
}

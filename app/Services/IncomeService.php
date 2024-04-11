<?php

namespace App\Services;

use App\Events\IncomeCreated;
use App\Models\Account;
use App\Models\Income;
use App\Models\IncomeGroup;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class IncomeService {

    public function index(Account $account, IncomeGroup $incomeGroup): LengthAwarePaginator
    {
        return Income::where('income_group_id', $incomeGroup->id)->paginate(5);
    }

    public function store($validatedRequest, IncomeGroup $incomeGroup): Income
    {
        $income = Income::create([
            'amount' => $validatedRequest['amount'],
            'schedule_id' => $validatedRequest['schedule_id'] ?? null,
            'end_date' => $validatedRequest['end_date'],
            'income_group_id' => $incomeGroup->id,
            'transaction_start' => $validatedRequest['transaction_start'] ?? null
        ]);

        return $income;
    }

    public function show(Income $income): ?Income
    {
        return $income;
    }

    public function update($validatedRequest, $income): Income
    {
        $income->update($validatedRequest);

        return $income;
    }

    public function delete($income): void
    {
        $income->delete();
    }
}
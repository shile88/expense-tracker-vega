<?php

namespace App\Services;

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

    public function store($validatedRequest, Account $account, IncomeGroup $incomeGroup): IncomeGroup
    {
        return Income::create([
            'amount' => $validatedRequest['amount'],
            'schedule_id' => $validatedRequest['schedule_id'] ?? null,
            'income_date' => $validatedRequest['income_date'] ?? null,
            'account_id' => $account->id,
            'income_group_id' => $incomeGroup->id
        ]);
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
<?php

namespace App\Services;

use App\Models\Account;
use App\Models\IncomeGroup;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Log;

class IncomeGroupService
{
    public function index(Account $account): LengthAwarePaginator
    {
        return IncomeGroup::where('account_id', $account->id)->paginate(5);
    }

    public function show(IncomeGroup $incomeGroup): IncomeGroup|Exception
    {
        return $incomeGroup;
    }

    public function store(array $validatedRequest, Account $account): IncomeGroup
    {
        $incomeGroup = IncomeGroup::create([
            'name' => $validatedRequest['name'],
            'account_id' => $account->id
        ]);

        Log::info('New income group created', ['user' => auth()->id(), 'data' => $incomeGroup]);

        return $incomeGroup;
    }

    public function update(array $validatedRequest, IncomeGroup $incomeGroup): IncomeGroup
    {
        $incomeGroup->update($validatedRequest);

        return $incomeGroup;
    }

    public function delete(IncomeGroup $incomeGroup): void
    {
        $incomeGroup->delete();

        Log::info('User deleted income group', ['user' => auth()->id(), 'income_group' => $incomeGroup->id]);
    }
}

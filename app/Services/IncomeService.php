<?php

namespace App\Services;

use App\Models\Income;

class IncomeService {

    public function index($account, $incomeGroup)
    {
        return Income::where('income_group_id', $incomeGroup->id)->paginate(5);
    }

    public function store($request, $account, $incomeGroup) 
    {
        $validated = $request->validated();

        return Income::create([
            'amount' => $validated['amount'],
            'schedule_id' => $validated['schedule_id'] ?? null,
            'income_date' => $validated['income_date'] ?? null,
            'account_Id' => $account->id,
            'income_group_id' => $incomeGroup->id
        ]);
    }

    public function show($income)
    {
        return $income;
    }

    public function update($request, $income)
    {
        $income->update($request->validated());

        return $income;
    }

    public function delete($income)
    {
        $income->delete();
    }
}
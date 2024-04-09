<?php

namespace App\Services;

use App\Models\IncomeGroup;

class IncomeGroupService 
{
    public function index($account)
    {
        return IncomeGroup::where('account_id', $account->id)->paginate(5);
    }

    public function show($incomeGroup)
    {
        return $incomeGroup;
    }

    public function store($request, $account)
    {
        $validated = $request->validated();
        
        return IncomeGroup::create([
            'name' => $validated['name'],
            'account_id' => $account->id
       ]);
    }

    public function update($request, $incomeGroup)
    {
       $incomeGroup->update($request->validated());

        return $incomeGroup;
    }

    public function delete($incomeGroup)
    {
        $incomeGroup->delete();
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreIncomeGroupRequest;
use App\Http\Requests\UpdateIncomeGroupRequest;
use App\Http\Resources\IncomeGroupResource;
use App\Models\Account;
use App\Models\IncomeGroup;
use App\Services\IncomeGroupService;
use Illuminate\Http\Response;

class IncomeGroupController extends Controller
{
    public function __construct(protected IncomeGroupService $incomeGroupService)
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Account $account)
    {
        $incomeGroups = $this->incomeGroupService->index($account);

        if ($incomeGroups)
            return response()->json([
                'success' => true,
                'message' => 'All income groups for account.',
                'data' => [
                    'income_groups' => IncomeGroupResource::collection($incomeGroups),
                    'pagination' => [
                        'total' => $incomeGroups['total'],
                        'per_page' => $incomeGroups['perPage'],
                        'current_page' => $incomeGroups['currentPage'],
                        'last_page' => $incomeGroups['lastPage']
                    ]
                ]
            ], Response::HTTP_OK);
        else
            return response()->json([
                'success' => true,
                'message' => 'No income groups for this account.',
                'data' => []
            ], Response::HTTP_NO_CONTENT);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIncomeGroupRequest $request, Account $account)
    {
        $newIncomeGroup = $this->incomeGroupService->store($request, $account);

        return response()->json([
            'success' => true,
            'message' => 'Income groupe created.',
            'data' => $newIncomeGroup
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Account $account, IncomeGroup $incomeGroup)
    {
        $incomeGroup = $this->incomeGroupService->show($incomeGroup);

        return response()->json([
            'success' => true,
            'message' => 'Your income group.',
            'data' => IncomeGroupResource::make($incomeGroup)
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIncomeGroupRequest $request, Account $account, IncomeGroup $incomeGroup)
    {
        $updatedIncomeGroup = $this->incomeGroupService->update($request, $incomeGroup);

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully',
            'data' => IncomeGroupResource::make($updatedIncomeGroup)
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account, IncomeGroup $incomeGroup)
    {
        $this->incomeGroupService->delete($incomeGroup);

        return response()->json([
            'success' => true,
            'message' => "Income group with id:$incomeGroup->id delete successfully",
            'data' => []
        ], Response::HTTP_OK);
    }
}

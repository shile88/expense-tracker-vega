<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreExpenseGroupRequest;
use App\Http\Requests\UpdateExpenseGroupRequest;
use App\Http\Resources\ExpenseGroupResource;
use App\Models\Account;
use App\Models\ExpenseGroup;
use App\Services\ExpenseGroupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExpenseGroupController
{
    public function __construct(protected ExpenseGroupService $expenseGroupService)
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Account $account): JsonResponse
    {
        $expenseGroup = $this->expenseGroupService->index($account);

        if ($expenseGroup)
            return response()->json([
                'success' => true,
                'message' => 'All expense groups for account.',
                'data' => [
                    'income_groups' => ExpenseGroupResource::collection($expenseGroup),
                    'pagination' => [
                        'total' => $expenseGroup['total'],
                        'per_page' => $expenseGroup['perPage'],
                        'current_page' => $expenseGroup['currentPage'],
                        'last_page' => $expenseGroup['lastPage']
                    ]
                ]
            ], Response::HTTP_OK);
        else
            return response()->json([
                'success' => true,
                'message' => 'No expense groups for this account.',
                'data' => []
            ], Response::HTTP_NO_CONTENT);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExpenseGroupRequest $request, Account $account): JsonResponse
    {
        $validatedRequest = $request->validated();

        $newExpenseGroup = $this->expenseGroupService->store($validatedRequest, $account);

        return response()->json([
            'success' => true,
            'message' => 'Expense groupe created.',
            'data' => ExpenseGroupResource::make($newExpenseGroup)
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Account $account, ExpenseGroup $expenseGroup)
    {
        $expenseGroup = $this->expenseGroupService->show($expenseGroup);

        return response()->json([
            'success' => true,
            'message' => 'Your expense group.',
            'data' => ExpenseGroupResource::make($expenseGroup)
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExpenseGroupRequest $request, Account $account, ExpenseGroup $expenseGroup)
    {
        $validatedRequest = $request->validated();
       
        $updatedExpenseGroup = $this->expenseGroupService->update($validatedRequest, $expenseGroup);

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully',
            'data' => ExpenseGroupResource::make($updatedExpenseGroup)
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account, ExpenseGroup $expenseGroup)
    {
        $this->expenseGroupService->delete($expenseGroup);

        return response()->json([
            'success' => true,
            'message' => "Expense group with id:$expenseGroup->id delete successfully",
            'data' => []
        ], Response::HTTP_OK);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Account;
use App\Models\Expense;
use App\Models\ExpenseGroup;
use App\Services\ExpenseService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class ExpenseController
{

    public function __construct(protected ExpenseService $expenseService)
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Account $account, ExpenseGroup $expenseGroup, Expense $expense)
    {
        $allExpenses = $this->expenseService->index($account, $expenseGroup);

        if($allExpenses)
            return response()->json([
                'success' => true,
                'message' => 'Show all expenses.',
                'data' => [
                    'expenses' => ExpenseResource::collection($allExpenses),
                    'pagination' => [
                        'total' => $allExpenses['total'],
                        'per_page' => $allExpenses['perPage'],
                        'current_page' => $allExpenses['currentPage'],
                        'last_page' => $allExpenses['lastPage']
                    ]
                ]
            ], Response::HTTP_OK);
        else
            return response()->json([
                'success' => true,
                'message' => 'No expense data.',
                'data' => [] 
            ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExpenseRequest $request, Account $account, ExpenseGroup $expenseGroup, Expense $expense)
    {
        Gate::authorize('create', [Expense::class, $request]);

        $validatedRequest = $request->validated();

        Log::info('User is trying to create expense with validated data', ['user_id' => auth()->id(), 'data' => $validatedRequest]);
      
        $newExpense = $this->expenseService->store($validatedRequest, $expenseGroup);
       
        if($newExpense)
            return response()->json([
                'success' => true,
                'message' => 'Expense created successfully',
                'data' => ExpenseResource::make($newExpense)
            ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Account $account, ExpenseGroup $expenseGroup, Expense $expense)
    {
        $expense = $this->expenseService->show($expense);

        return response()->json([
            'success' => true,
            'message' => 'Your expense.',
            'data' => ExpenseResource::make($expense)
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExpenseRequest $request, Account $account, ExpenseGroup $expenseGroup, Expense $expense)
    {
        Gate::authorize('update', [$expense, $request]);

        $validatedRequest = $request->validated();

        $updatedExpense = $this->expenseService->update($validatedRequest, $expense);

        return response()->json([
            'success' => true,
            'message' => 'Expense updated successfully',
            'data' => ExpenseResource::make($updatedExpense)
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account, ExpenseGroup $expenseGroup, Expense $expense)
    {
        Log::info('User is trying to delete expense', ['user_id' => auth()->id(), 'expense_id' => $expense->id]);

        $this->expenseService->delete($expense);

        return response()->json([
            'success' => true,
            'message' => "Expense with id:$expense->id delete successfully"
        ], Response::HTTP_OK);
    }
}

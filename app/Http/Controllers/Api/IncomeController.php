<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreIncomeRequest;
use App\Http\Requests\UpdateIncomeRequest;
use App\Http\Resources\IncomeResource;
use App\Models\Account;
use App\Models\Income;
use App\Models\IncomeGroup;
use App\Services\IncomeService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class IncomeController extends Controller
{
    public function __construct(protected IncomeService $incomeService)
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Account $account, IncomeGroup $incomeGroup): JsonResponse
    {
        $allIncomes = $this->incomeService->index($account, $incomeGroup);

        if ($allIncomes)
            return response()->json([
                'success' => true,
                'message' => 'Show all incomes.',
                'data' => [
                    'income' => IncomeResource::collection($allIncomes),
                    'pagination' => [
                        'total' => $allIncomes['total'],
                        'per_page' => $allIncomes['perPage'],
                        'current_page' => $allIncomes['currentPage'],
                        'last_page' => $allIncomes['lastPage']
                    ]
                ]
            ], Response::HTTP_OK);
        else
            return response()->json([
                'success' => true,
                'message' => 'No income data.',
                'data' => []
            ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIncomeRequest $request, Account $account, IncomeGroup $incomeGroup): JsonResponse
    {
        Gate::authorize('create', [Income::class, $request]);

        $validatedRequest = $request->validated();

        Log::info('User is trying to create income with validated data', ['user_id' => auth()->id(), 'data' => $validatedRequest]);

        $newIncome = $this->incomeService->store($validatedRequest, $incomeGroup);

        if ($newIncome)
            return response()->json([
                'success' => true,
                'message' => 'Income created successfully',
                'data' => $newIncome
            ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Account $account, IncomeGroup $incomeGroup, Income $income): JsonResponse
    {
        $income = $this->incomeService->show($income);

        return response()->json([
            'success' => true,
            'message' => 'Your income.',
            'data' => IncomeResource::make($income)
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIncomeRequest $request, Account $account, IncomeGroup $incomeGroup, Income $income): JsonResponse
    {
        Gate::authorize('update', [$income, $request]);

        $validatedRequest = $request->validated();

        $updatedIncome = $this->incomeService->update($validatedRequest, $income);

        return response()->json([
            'success' => true,
            'message' => 'Income updated successfully',
            'data' => IncomeResource::make($updatedIncome)
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account, IncomeGroup $incomeGroup, Income $income): JsonResponse
    {
        Log::info('User is trying to delete income', ['user_id' => auth()->id(), 'income_id' => $income->id]);

        $this->incomeService->delete($income);

        return response()->json([
            'success' => true,
            'message' => "Income with id:$income->id delete successfully"
        ], Response::HTTP_OK);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ParamsReportRequest;
use App\Http\Resources\ExpenseResource;
use App\Http\Resources\IncomeResource;
use App\Models\Account;
use App\Services\BalanceReportService;

class BalanceReportController
{
    public function __construct(protected BalanceReportService $balanceReportService)
    {
    }

    public function balanceReport(ParamsReportRequest $request, Account $account)
    {
        $validatedRequest = $request->validated();

        $stats = $this->balanceReportService->balanceReport($validatedRequest, $account);

        if ($stats) {
            [$incomes, $expenses] = $stats;

            if ($incomes && empty($expenses)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Your incomes data',
                    'data' => [
                        'incomes' => $incomes->total() > 1
                            ? [IncomeResource::collection($incomes),
                                'pagination' => [
                                    'total' => $incomes->total(),
                                    'per_page' => $incomes->perPage(),
                                    'current_page' => $incomes->currentPage(),
                                    'last_page' => $incomes->lastPage(),
                                ]]
                            : $incomes[0],
                    ],
                ]);
            }

            if (empty($incomes) && $expenses) {
                return response()->json([
                    'success' => true,
                    'message' => 'Your expenses data',
                    'data' => [
                        'expenses' => $expenses->total() > 1
                            ? [ExpenseResource::collection($expenses),
                                'pagination' => [
                                    'total' => $expenses->total(),
                                    'per_page' => $expenses->perPage(),
                                    'current_page' => $expenses->currentPage(),
                                    'last_page' => $expenses->lastPage(),
                                ]]
                            : $expenses[0],
                    ],
                ]);
            }

            if ($incomes && $expenses) {
                return response()->json([
                    'success' => true,
                    'message' => 'Your incomes and expenses data',
                    'data' => [
                        'incomes' => $incomes->total() > 1
                            ? [IncomeResource::collection($incomes),
                                'pagination' => [
                                    'total' => $incomes->total(),
                                    'per_page' => $incomes->perPage(),
                                    'current_page' => $incomes->currentPage(),
                                    'last_page' => $incomes->lastPage(),
                                ]]
                            : $incomes[0],
                        'expenses' => $expenses->total() > 1
                            ? [ExpenseResource::collection($expenses),
                                'pagination' => [
                                    'total' => $expenses->total(),
                                    'per_page' => $expenses->perPage(),
                                    'current_page' => $expenses->currentPage(),
                                    'last_page' => $expenses->lastPage(),
                                ]]
                            : $expenses[0],
                    ],
                ]);
            }

        } else {
            return response()->json([
                'success' => true,
                'message' => 'No data for incomes or expenses',
                'data' => [],
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use App\Services\AccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Exception;
use Illuminate\Support\Facades\Log;

class AccountController extends Controller
{
    public function __construct(protected AccountService $accountService)
    {
    }

    public function index(): JsonResponse
    {
            $myAccounts = $this->accountService->index();

            if ($myAccounts)
                return response()->json([
                    'success' => true,
                    'message' => 'All my accounts.',
                    'data' => [
                        'accounts' => AccountResource::collection($myAccounts),
                        'pagination' => [
                            'total' => $myAccounts['total'],
                            'per_page' => $myAccounts['perPage'],
                            'current_page' => $myAccounts['currentPage'],
                            'last_page' => $myAccounts['lastPage']
                        ]
                    ]
                ], Response::HTTP_OK);
            else
                return response()->json([
                    'success' => true,
                    'message' => 'This user has no accounts.',
                    'data' => []
                ],  Response::HTTP_OK);
    }

    public function store(StoreAccountRequest $request): JsonResponse
    {
        Log::warning('User is trying to create an account', ['user' => auth()->id(), 'data' => $request]);
        
        Gate::authorize('create', [Account::class, $request]);

        Log::warning('User is allowed to create an account', ['user' => auth()->id(), 'data' => $request]);
        
        $validatedRequest = $request->validated();

        $newAccount = $this->accountService->store($validatedRequest);

        if ($newAccount)
            return response()->json([
                'success' => true,
                'message' => 'Account created successfully',
                'data' => $newAccount
            ], Response::HTTP_CREATED);

        
    }

    public function show(Account $account): JsonResponse
    {
        $myAccount = $this->accountService->show($account);

        if ($myAccount)
            return response()->json([
                'success' => true,
                'message' => 'Here is your account.',
                'data' => $myAccount
            ], Response::HTTP_OK);
    }

    public function update(UpdateAccountRequest $request, Account $account): JsonResponse
    {
        Gate::authorize('update', [$account, $request]);

        $validatedRequest = $request->validated();

        $updatedAccount = $this->accountService->update($validatedRequest, $account);

        if ($updatedAccount)
            return response()->json([
                'success' => true,
                'message' => 'Updated successfully',
                'data' => [
                    'account' => $updatedAccount
                ]
            ],  Response::HTTP_OK);
    }

    public function delete(Account $account): JsonResponse
    {
        Log::warning('User is trying to delete an account', ['user' => auth()->id(), 'account' => $account->id]);

        $this->accountService->delete($account);

        return response()->json([
            'success' => true,
            'message' => "Account with id:$account->id deleted successfully"
        ],  Response::HTTP_OK);
    }
}

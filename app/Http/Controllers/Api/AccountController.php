<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use App\Services\AccountService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class AccountController extends Controller
{
    public function __construct(protected AccountService $accountService)
    {
    }

    public function index()
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

    public function store(StoreAccountRequest $request)
    {
        Gate::authorize('create', [Account::class, $request]);

        $newAccount = $this->accountService->store($request);

        if ($newAccount)
            return response()->json([
                'success' => true,
                'message' => 'Account created successfully',
                'data' => $newAccount
            ], Response::HTTP_CREATED);
    }

    public function show(Account $account)
    {
        $myAccount = $this->accountService->show($account);

        if ($myAccount)
            return response()->json([
                'success' => true,
                'message' => 'Here is your account.',
                'data' => $myAccount
            ], Response::HTTP_OK);
    }

    public function update(UpdateAccountRequest $request, Account $account)
    {
        Gate::authorize('update', [$account, $request]);

        $updatedAccount = $this->accountService->update($request, $account);

        if ($updatedAccount)
            return response()->json([
                'success' => true,
                'message' => 'Updated successfully',
                'data' => [
                    'account' => $updatedAccount
                ]
            ],  Response::HTTP_OK);
    }

    public function delete(Account $account)
    {
        $this->accountService->delete($account);

        return response()->json([
            'success' => true,
            'message' => "Account with id:$account->id deleted successfully"
        ],  Response::HTTP_OK);
    }
}

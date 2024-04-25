<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreSavingRequest;
use App\Http\Resources\SavingResource;
use App\Models\Account;
use App\Services\SavingService;
use Illuminate\Http\Response;

class SavingController
{
    public function __construct(protected SavingService $savingService )
    {
        
    }

    public function create(StoreSavingRequest $request, Account $account)
    {
        $saving = $this->savingService->createSaving($request->validated(), $account);

        if($saving) {
            return response()->json([
                'success' => true,
                'message' => 'Your saving is successfully created',
                'data' => [
                    'saving' => SavingResource::make($saving)
                ]
            ], Response::HTTP_CREATED);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Could not create saving. Please check do you have enough funds.',
                'data' => []
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}

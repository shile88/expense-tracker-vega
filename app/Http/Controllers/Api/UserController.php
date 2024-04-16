<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function __construct(protected UserService $userService)
    {
    }

    public function register(UserRegisterRequest $request): JsonResponse
    {
        $validatedRequest = $request->validated();

        Log::info('Trying to create new user with validated request', ['data' => $validatedRequest]);

        $newUser = $this->userService->register($validatedRequest);

        if ($newUser)
            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => [
                    'user' => $newUser
                ]
            ], Response::HTTP_CREATED);
    }

    public function login(UserLoginRequest $request): JsonResponse
    {
        $validatedRequest = $request->validated();

        Log::info('Trying to login user with validated request', ['data' => $validatedRequest]);

        $data = $this->userService->login($validatedRequest);

        if ($data) {
            [$user, $token] = $data;

            return response()->json([
                'success' => true,
                'message' => 'Logged in successfully',
                'data' => [
                    'user' => $user,
                    'access_token' => $token
                ]
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'The provided credentials do not match our records.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function logout(): JsonResponse
    {
        Log::info('User trying to logout', ['user_id' => auth()->id()]);

        $this->userService->logout();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ], Response::HTTP_OK);
    }
}

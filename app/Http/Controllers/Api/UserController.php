<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Services\UserService;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function __construct(protected UserService $userService)
    {
    }

    public function register(UserRegisterRequest $request)
    {
        $newUser = $this->userService->register($request);

        if ($newUser)
            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => [
                    'user' => $newUser
                ]
            ], Response::HTTP_OK);
    }

    public function login(UserLoginRequest $request)
    {
        $data = $this->userService->login($request);

        if ($data) {
            [$user, $token] = $data;

            return response()->json([
                'success' => true,
                'message' => 'Logged in successfully',
                'data' => [
                    'user' => $user,
                    'access_token' => $token
                ]
            ], Response::HTTP_CREATED);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'The provided credentials do not match our records.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function logout()
    {
        $this->userService->logout();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ], Response::HTTP_OK);
    }
}

<?php

namespace App\Services;

use App\Events\UserRegistered;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService
{

    public function register(array $validatedRequest): User
    {
        $user = User::create(
            [
                'name' => $validatedRequest['name'],
                'email' => $validatedRequest['email'],
                'password' => Hash::make($validatedRequest['password']),
                'type' => $validatedRequest['type'] ?? 'basic'
            ]
        );

        UserRegistered::dispatch($user);

        return $user;
    }

    public function login($validatedRequest): array|bool
    {
        if (Auth::attempt($validatedRequest)) {
            $user = auth()->user();

            $token = $user->type === 'basic' ?
                $user->createToken('access_token')->plainTextToken :
                $user->createToken('access_token')->plainTextToken;

            return [$user, $token];
        }

        return false;
    }

    public function logout(): void
    {
        auth()->user()->tokens()->delete();
    }
}

<?php

namespace App\Services;

use App\Events\UserRegistered;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService
{
    public function register(array $validatedRequest): User
    {
        $user = User::create(
            [
                'name' => $validatedRequest['name'],
                'email' => $validatedRequest['email'],
                'password' => Hash::make($validatedRequest['password']),
                'type' => $validatedRequest['type'] ?? 'basic',
            ]
        );

        Log::info('New user created', ['user' => $user->id]);

        UserRegistered::dispatch($user);

        Log::info('Created account for new user', ['user' => $user->id]);

        return $user;
    }

    public function login(array $validatedRequest): array|bool
    {
        if (Auth::attempt($validatedRequest)) {
            $user = auth()->user();

            $token = $user->type === 'basic' ?
                $user->createToken('access_token')->plainTextToken :
                $user->createToken('access_token')->plainTextToken;

            Log::info('User logged in and token created', ['user' => $user->id]);

            return [$user, $token];
        }

        Log::error('Cant login with given credentials', ['data' => $validatedRequest]);

        return false;
    }

    public function logout(): void
    {
        auth()->user()->tokens()->delete();

        Log::info('User log out', ['user' => auth()->id()]);
    }

    public function activateReminder($validatedRequest)
    {
        auth()->user()->update($validatedRequest);
    }
}

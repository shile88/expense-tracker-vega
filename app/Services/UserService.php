<?php

namespace App\Services;

use App\Events\UserRegistered;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService
{

    public function register($request)
    {
        $validated = $request->validated();

        $user = User::create(
            [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'type' => $validated['type'] ?? 'basic'
            ]
        );

        UserRegistered::dispatch($user);

        return $user;
    }

    public function login($request)
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {
            $user = auth()->user();

            $token = $user->type === 'basic' ?
                $user->createToken('access_token')->plainTextToken :
                $user->createToken('access_token')->plainTextToken;

            return [$user, $token];
        }

        return false;
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
    }
}

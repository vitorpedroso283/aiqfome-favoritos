<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function login(array $credentials): ?string
    {
        if (!Auth::attempt($credentials)) {
            return null;
        }

        $user = Auth::user();

        return $user->createToken('auth_token')->plainTextToken;
    }
}

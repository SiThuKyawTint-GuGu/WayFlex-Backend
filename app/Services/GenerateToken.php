<?php

namespace App\Services;

class GenerateToken
{
    public function GenerateToken($user)
    {
        // Generate token
        $token = $user->createToken($user->email);

        // Update token expiration
        $latestToken = $user->tokens()->latest()->first();
        $latestToken->update(['expires_at' => now()->addDay(30)]);

        return $token->plainTextToken;
    }
}

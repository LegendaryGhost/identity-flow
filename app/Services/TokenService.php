<?php

namespace App\Services;


class TokenService
{
    public function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }

    public function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    public function verifyToken(string $token, string $hashedToken): bool
    {
        return hash_equals($hashedToken, $this->hashToken($token));
    }
}

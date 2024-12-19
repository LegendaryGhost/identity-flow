<?php

namespace App;

final class Utils
{
    private function __construct() { }

    public static function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }
}

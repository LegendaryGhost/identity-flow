<?php

namespace App;

final class Utils
{
    private function __construct() { }

    public static function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }

    public static function generateCodePin(int $length = 6): string
    {
        return str_pad(mt_rand(0, 999999), $length, '0', STR_PAD_LEFT);
    }
}

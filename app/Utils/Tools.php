<?php

namespace App\Utils;

use Illuminate\Support\Str;
use Random\RandomException;

class Tools
{

    //creation d'un token pour un utilisateur
    public static function tokenGenerateur(): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';
        return substr(str_shuffle(str_repeat($characters, 60)), 0, 60);
    }

}

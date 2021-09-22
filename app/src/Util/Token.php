<?php


namespace App\Util;


use Exception;

class Token
{
    public function generateToken(int $bytes = 64): ?string
    {
        try {
            return str_replace(["\\", "/", "+", "="], '0', base64_encode(random_bytes($bytes)));
        } catch (Exception) {
            return null;
        }
    }
}
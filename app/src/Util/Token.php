<?php


namespace App\Util;


use Exception;

class Token
{
    public function generateToken(int $bytes = 64): ?string
    {
        try {
            return rtrim(strtr(base64_encode(random_bytes($bytes)), '+\/', ''), '=');
        } catch (Exception) {
            return null;
        }
    }
}
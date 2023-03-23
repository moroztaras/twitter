<?php

namespace App\Components\Utils;

class TokenGenerator
{
    public static function generateToken(): string
    {
        return rtrim(strtr(base64_encode(self::getRandomNumber()), '+/', '-_'), '=');
    }

    private static function getRandomNumber(): bool|string
    {
        return hash('sha256', uniqid(mt_rand(), true), true);
    }
}

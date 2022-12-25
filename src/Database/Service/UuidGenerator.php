<?php

declare(strict_types=1);

namespace App\Database\Service;

class UuidGenerator
{
    public static function generateUuidVersion4(): string
    {
        try {
            return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                // 32 bits for "time_low"
                random_int(0, 0xffff), random_int(0, 0xffff),

                // 16 bits for "time_mid"
                random_int(0, 0xffff),

                // 16 bits for "time_hi_and_version",
                // four most significant bits holds version number 4
                random_int(0, 0x0fff) | 0x4000,

                // 16 bits, 8 bits for "clk_seq_hi_res",
                // 8 bits for "clk_seq_low",
                // two most significant bits holds zero and one for variant DCE1.1
                random_int(0, 0x3fff) | 0x8000,

                // 48 bits for "node"
                random_int(0, 0xffff), random_int(0, 0xffff), random_int(0, 0xffff)
            );
        } catch (\Exception $e) {
            return '';
        }
    }
    
    public static function getCompactUuid(string $uuid): string
    {
        return str_replace('-', '', $uuid);
    }
    
    public static function getCompactUuid4(): string
    {
        return self::getCompactUuid(self::generateUuidVersion4());
    }
}
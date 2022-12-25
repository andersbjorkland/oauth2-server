<?php

declare(strict_types=1);

namespace App\Database\Service;

class InitializeDatabase
{
    public static function getCreateUserTableSQL(): string
    {
        return "CREATE TABLE IF NOT EXISTS user (id BINARY(32) NOT NULL COMMENT '(DC2Type:uuid4)', email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT '(DC2Type:json)', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB";
    }

}
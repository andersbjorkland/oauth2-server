<?php

declare(strict_types=1);

namespace App\Database\Service;

class InitializeDatabase
{
    public static function getCreateUserTableSQL(): string
    {
        return "CREATE TABLE IF NOT EXISTS user (id BINARY(32) NOT NULL COMMENT '(Type:uuid4)', email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT '(DC2Type:json)', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB";
    }
    
    public static function getCreateClientTableSQL(): string
    {
        $sql = "CREATE TABLE IF NOT EXISTS client (id BINARY(32) NOT NULL COMMENT '(Type:uuid4)', redirect_uri VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, is_confidential TINYINT(1) NOT NULL, secret BINARY(64) DEFAULT NULL, UNIQUE INDEX UNIQ_C74404555E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB";
        
        return $sql;
    }

}
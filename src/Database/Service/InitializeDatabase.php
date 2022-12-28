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
    
    public static function getCreateScopeTableSQL(): string
    {
        $sql = "CREATE TABLE IF NOT EXISTS scope (id BINARY(32) NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_AF55D35E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB";
        return $sql;
    }
    
    public static function getCreateAccessTokenTableSQL(): string
    {
        $sql = "CREATE TABLE IF NOT EXISTS access_token (id BINARY(32) NOT NULL, user_id BINARY(32) DEFAULT NULL COMMENT '(Type:uuid)', client_id BINARY(32) NOT NULL, expiry_date_time DATETIME NOT NULL COMMENT '(Type:datetime_immutable)', scopes LONGTEXT NOT NULL COMMENT '(Type:json)', PRIMARY KEY(id), FOREIGN KEY (user_id) REFERENCES user(id), FOREIGN KEY (client_id) REFERENCES client(id) ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB";
        return $sql;
    }

}
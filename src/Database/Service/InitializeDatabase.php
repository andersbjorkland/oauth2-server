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
        $sql = "CREATE TABLE IF NOT EXISTS access_token (id BINARY(32) NOT NULL, user_id BINARY(32) DEFAULT NULL COMMENT '(Type:uuid)', client_id BINARY(32) NOT NULL, expiry_date_time DATETIME NOT NULL COMMENT '(Type:datetime_immutable)', PRIMARY KEY(id), FOREIGN KEY (user_id) REFERENCES user(id), FOREIGN KEY (client_id) REFERENCES client(id) ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB";
        return $sql;
    }
    
    public static function getCreateAccessTokenScopeTableSQL(): string
    {
        $sql = "CREATE TABLE IF NOT EXISTS access_token_scope (access_token_id BINARY(32) NOT NULL, scope_id BINARY(32) NOT NULL, INDEX IDX_2B0E0B9B5F37A13B (access_token_id), INDEX IDX_2B0E0B9B5E237E06 (scope_id), PRIMARY KEY(access_token_id, scope_id), FOREIGN KEY (access_token_id) REFERENCES access_token(id), FOREIGN KEY (scope_id) REFERENCES scope(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB";
        return $sql;
    }
    
    public static function getCreateAuthorizationCodeTableSQL(): string
    {
        $sql = "CREATE TABLE IF NOT EXISTS authorization_code (id BINARY(32) NOT NULL, user_id BINARY(32) DEFAULT NULL COMMENT '(Type:uuid)', client_id BINARY(32) NOT NULL, expiry_date_time DATETIME NOT NULL COMMENT '(Type:datetime_immutable)', redirect_uri VARCHAR(255) NOT NULL, PRIMARY KEY(id), FOREIGN KEY (user_id) REFERENCES user(id), FOREIGN KEY (client_id) REFERENCES client(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB";
        return $sql;
    }
    
    public static function getCreateAuthorizationCodeScopeTableSQL(): string
    {
        $sql = "CREATE TABLE IF NOT EXISTS authorization_code_scope (authorization_code_id BINARY(32) NOT NULL, scope_id BINARY(32) NOT NULL, INDEX IDX_3B0E0B9B5F37A13B (authorization_code_id), INDEX IDX_3B0E0B9B5E237E06 (scope_id), PRIMARY KEY(authorization_code_id, scope_id), FOREIGN KEY (authorization_code_id) REFERENCES authorization_code(id), FOREIGN KEY (scope_id) REFERENCES scope(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB";
        return $sql;
    }
    

}
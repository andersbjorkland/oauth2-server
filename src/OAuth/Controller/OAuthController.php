<?php

declare(strict_types=1);

namespace App\OAuth\Controller;

use Defuse\Crypto\Key;
use League\OAuth2\Server\AuthorizationServer;

class OAuthController
{
    public function server():void
    {
        $key = Key::loadFromAsciiSafeString(file_get_contents(__DIR__ . '/../../../security/defuse.key'));
        
        $var = true;
    }
}
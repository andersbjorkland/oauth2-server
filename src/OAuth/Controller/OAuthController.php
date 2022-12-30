<?php

declare(strict_types=1);

namespace App\OAuth\Controller;

use Defuse\Crypto\Exception\BadFormatException;
use Defuse\Crypto\Exception\EnvironmentIsBrokenException;
use Defuse\Crypto\Key;
use League\OAuth2\Server\AuthorizationServer;

class OAuthController
{
    public function __construct(
        private AuthorizationServer $authorizationServer,
    ){}

    /**
     * @throws BadFormatException
     * @throws EnvironmentIsBrokenException
     */
    public function server():void
    {
        $defuseKey = Key::loadFromAsciiSafeString(file_get_contents(__DIR__ . '/../../../security/defuse.key'));
    }
}
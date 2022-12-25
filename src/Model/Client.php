<?php

declare(strict_types=1);

namespace App\Model;

use League\OAuth2\Server\Entities\ClientEntityInterface;

class Client implements ClientEntityInterface
{

    public function getIdentifier()
    {
        // TODO: Implement getIdentifier() method.
    }

    public function getName()
    {
        // TODO: Implement getName() method.
    }

    public function getRedirectUri()
    {
        // TODO: Implement getRedirectUri() method.
    }

    public function isConfidential()
    {
        // TODO: Implement isConfidential() method.
    }
}
<?php

declare(strict_types=1);

namespace App\Model;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;

class Client implements ClientEntityInterface
{
    public function __construct(
        private readonly string $name,
        private readonly string $redirectUri,
        private readonly bool $isConfidential = false,
        private readonly ?string $id = null,
    ){}

    public function getIdentifier()
    {
        return 'id';
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRedirectUri(): array|string
    {
        return $this->redirectUri;
    }

    public function isConfidential(): bool
    {
        return $this->isConfidential;
    }
    
    public function getId(): ?string
    {
        return $this->id;
    }
}
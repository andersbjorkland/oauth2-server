<?php

declare(strict_types=1);

namespace App\Model;

use League\OAuth2\Server\Entities\ClientEntityInterface;

class Client implements ClientEntityInterface
{
    public function __construct(
        private string $name,
        private string $redirectUri,
        private bool $isConfidential = false,
        private ?string $secret = null,
        private ?string $id = null,
    ){}

    public function getIdentifier(): string
    {
        return $this->id;
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

    /**
     * @return string|null
     */
    public function getSecret(): ?string
    {
        return $this->secret;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string $redirectUri
     */
    public function setRedirectUri(string $redirectUri): void
    {
        $this->redirectUri = $redirectUri;
    }

    /**
     * @param bool $isConfidential
     */
    public function setIsConfidential(bool $isConfidential): void
    {
        $this->isConfidential = $isConfidential;
    }

    /**
     * @param string|null $secret
     */
    public function setSecret(?string $secret): void
    {
        $this->secret = $secret;
    }

    /**
     * @param string|null $id
     */
    public function setId(?string $id): void
    {
        $this->id = $id;
    }
    
}
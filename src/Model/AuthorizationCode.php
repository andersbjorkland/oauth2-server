<?php

declare(strict_types=1);

namespace App\Model;

use DateTimeImmutable;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

class AuthorizationCode implements AuthCodeEntityInterface
{
    public function __construct(
        private string                $id,
        private                       $expiryDateTime,
        private ?string               $userId,
        private ?ClientEntityInterface $client,
        private string                $redirectUri,
        /** @var ScopeEntityInterface[] $scopes */
        private array                 $scopes = [],
    ){}
    
    public function getId(): string
    {
        return $this->id;
    }

    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }

    /** 
     * @param string $uri
     */ 
    public function setRedirectUri($uri): void
    {
        $this->redirectUri = $uri;
    }

    public function getIdentifier(): string
    {
        return $this->getId();
    }

    public function setIdentifier($identifier): void
    {
        $this->id = $identifier;
    }

    public function getExpiryDateTime(): DateTimeImmutable
    {
        return $this->expiryDateTime;
    }

    public function setExpiryDateTime(DateTimeImmutable $dateTime): void
    {
        $this->expiryDateTime = $dateTime;
    }

    public function setUserIdentifier($identifier): void
    {
        $this->userId = $identifier;
    }

    public function getUserIdentifier(): string
    {
        return $this->userId;
    }

    public function getClient(): ClientEntityInterface
    {
        return $this->client;
    }

    public function setClient(ClientEntityInterface $client): void
    {
        $this->client = $client;
    }

    public function addScope(ScopeEntityInterface $scope): void
    {
        $this->scopes[] = $scope;
    }

    /**
     * @return ScopeEntityInterface[]
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }
}
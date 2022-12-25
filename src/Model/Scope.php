<?php

declare(strict_types=1);

namespace App\Model;

use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\ScopeTrait;

class Scope implements ScopeEntityInterface
{
    public function __construct(
        private string $name,
        private ?string $description = null,
        private ?string $id = null
    ) {
    }

    public function getIdentifier(): string
    {
        return $this->id;
    }

    public function jsonSerialize(): mixed
    {
        return $this->getIdentifier();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string|null $id
     */
    public function setId(?string $id): void
    {
        $this->id = $id;
    }
    
    
}
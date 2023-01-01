<?php

declare(strict_types=1);

namespace App\Database\Repository;

use App\Database\Manager\RefreshTokenManager;
use App\Database\Service\UuidGenerator;
use App\Model\RefreshToken;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

class RefreshTokenRepository extends AbstractRepository implements RefreshTokenRepositoryInterface
{
    
    public function __construct(
        private readonly RefreshTokenManager $refreshTokenManager,
    ){
        parent::__construct($refreshTokenManager->getConnection());
    }

    public function getNewRefreshToken(): RefreshTokenEntityInterface
    {
        return new RefreshToken(
            UuidGenerator::getCompactUuid4(),
            new \DateTimeImmutable('+30 day'),
            null
        );
    }

    /**
     * @throws \Throwable
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity): void
    {
        assert($refreshTokenEntity instanceof RefreshToken);
        $this->refreshTokenManager->create($refreshTokenEntity);
    }

    /**
     * @throws \Throwable
     */
    public function revokeRefreshToken($tokenId)
    {
        $this->refreshTokenManager->delete($tokenId);
    }

    /**
     * @throws \Throwable
     */
    public function isRefreshTokenRevoked($tokenId): bool
    {
        return $this->refreshTokenManager->isRevoked($tokenId);
    }
}
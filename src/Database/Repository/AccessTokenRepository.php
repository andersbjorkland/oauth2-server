<?php

declare(strict_types=1);

namespace App\Database\Repository;

use App\Database\Manager\AccessTokenManager;
use App\Database\Service\UuidGenerator;
use App\Model\AccessToken;
use DateTimeImmutable;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Throwable;

class AccessTokenRepository extends AbstractRepository implements AccessTokenRepositoryInterface
{
    public function __construct(
        private readonly AccessTokenManager $accessTokenManager,
    ){
        parent::__construct($this->accessTokenManager->getConnection());
    }

    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null): AccessTokenEntityInterface
    {
        $accessToken = new AccessToken(
            UuidGenerator::getCompactUuid4(),
            new DateTimeImmutable(),
            $userIdentifier,
            $clientEntity,
            new CryptKey(__DIR__ . '/../../../config/private.key', null, false)
        );
        
        return $accessToken;
    }

    /**
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
    {
        try {
            $status = $this->accessTokenManager->create($accessTokenEntity);
        } catch (Throwable $exception) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }

        if ($status === false) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }
    }

    public function revokeAccessToken($tokenId): void
    {
        try {
            $this->accessTokenManager->revoke($tokenId);
        } catch (Throwable $exception) {
            
        }
    }

    public function isAccessTokenRevoked($tokenId): bool
    {
        try {
            return $this->accessTokenManager->isRevoked($tokenId);
        } catch (Throwable $exception) {
            return true;
        }
    }
}
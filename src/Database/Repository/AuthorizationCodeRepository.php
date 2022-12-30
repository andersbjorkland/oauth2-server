<?php

declare(strict_types=1);

namespace App\Database\Repository;

use App\Database\Manager\AuthorizationCodeManager;
use App\Database\Service\UuidGenerator;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use React\MySQL\ConnectionInterface;
use function React\Async\await;

class AuthorizationCodeRepository extends AbstractRepository implements AuthCodeRepositoryInterface
{
    public function __construct(
        private readonly AuthorizationCodeManager $authorizationCodeManager
    ){
        parent::__construct($this->authorizationCodeManager->getConnection());
    }

    public function getNewAuthCode(): AuthCodeEntityInterface
    {
        return new \App\Model\AuthorizationCode(
            id: UuidGenerator::getCompactUuid4(),
            expiryDateTime: new \DateTimeImmutable(),
            userId: null,
            client: null,
            redirectUri: '',
        );
    }

    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity): void
    {
        $this->authorizationCodeManager->create($authCodeEntity);
    }

    public function revokeAuthCode($codeId)
    {
        $this->authorizationCodeManager->revoke($codeId);
    }

    public function isAuthCodeRevoked($codeId): bool
    {
        $sql = 'SELECT id FROM authorization_code WHERE id = ?';
        $result = await($this->connection->query($sql, [$codeId]));
        return count($result->resultRows) === 0;
    }
}
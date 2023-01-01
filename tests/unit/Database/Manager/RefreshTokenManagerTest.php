<?php

declare(strict_types=1);

namespace unit\Database\Manager;

use App\Database\Manager\AccessTokenManager;
use App\Database\Manager\RefreshTokenManager;
use App\Database\Service\InitializeDatabase;
use App\Database\Service\UuidGenerator;
use App\Model\AccessToken;
use App\Model\Client;
use App\Model\RefreshToken;
use League\OAuth2\Server\CryptKey;
use PHPUnit\Framework\TestCase;
use React\MySQL\Factory;
use function React\Async\await;

class RefreshTokenManagerTest extends TestCase
{
    private RefreshTokenManager $refreshTokenManager;
    
    public function setUp(): void
    {
        parent::setUp();
        
        $connection = (new Factory())->createLazyConnection($_ENV['MYSQL_URI']);
        $accessTokenManager = $this->createMock(AccessTokenManager::class);
        
        $this->refreshTokenManager = new RefreshTokenManager($connection, $accessTokenManager);
        
        try {
            await($connection->query(InitializeDatabase::getCreateRefreshTokenTableSQL()));
        } catch (\Throwable $e) {
            echo $e->getMessage() . PHP_EOL;
        }
    }
    
    public function testCreateRefreshToken(): void
    {
        $refreshToken = $this->createRefreshToken();
        
        $result = $this->refreshTokenManager->create($refreshToken);
        
        $this->assertTrue($result);
        
        $refreshToken = $this->refreshTokenManager->get($refreshToken->getIdentifier());
        
        $this->assertNotNull($refreshToken);
    }
    
    public function tearDown(): void
    {
        try {
            await($this->refreshTokenManager->getConnection()->query('TRUNCATE TABLE refresh_token'));
        } catch (\Throwable $e) {
            echo $e->getMessage() . PHP_EOL;
        }
        parent::tearDown();
    }
    
    public static function tearDownAfterClass(): void
    {
        $connection = (new Factory())->createLazyConnection($_ENV['MYSQL_URI']);
        try {
            await($connection->query('TRUNCATE TABLE refresh_token'));
        } catch (\Throwable $e) {
            echo $e->getMessage() . PHP_EOL;
        }
        parent::tearDownAfterClass();
    }
    
    protected function createRefreshToken(?string $uuid = null): RefreshToken
    {
        return new RefreshToken(
            id: $uuid ?? UuidGenerator::getCompactUuid4(),
            expiryDateTime: new \DateTimeImmutable('now + 30 day'),
            accessToken: $this->createAccessToken()
        );
    }
    
    protected function createClient(): Client
    {
        return new Client(
            name: 'test',
            redirectUri: 'https://example.com',
            isConfidential: false,
            id: UuidGenerator::getCompactUuid4(),
        );
    }
    
    protected function createAccessToken(): AccessToken
    {
        return new AccessToken(
            id: UuidGenerator::getCompactUuid4(),
            expiryDateTime: new \DateTimeImmutable('now + 30 day'),
            userIdentifier: UuidGenerator::getCompactUuid4(),
            client: $this->createClient(),
            privateKey: new CryptKey(__DIR__ . '/../../../../security/private.key'),
        );
    }

}
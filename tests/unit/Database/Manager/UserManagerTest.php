<?php

declare(strict_types=1);

namespace unit\Database\Manager;

use App\Database\Manager\UserManager;
use App\Database\Service\InitializeDatabase;
use App\Database\Service\UuidGenerator;
use App\Model\User;
use PHPUnit\Framework\TestCase;
use React\MySQL\ConnectionInterface;
use React\MySQL\Factory;
use function React\Async\await;

class UserManagerTest extends TestCase
{
    private ConnectionInterface $connection;
    private UserManager $userManager;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->connection = (new Factory())->createLazyConnection('mysql://test:password@localhost:3307/test?idle=0.1&timeout=0.1');
    
        $this->userManager = new UserManager($this->connection);

        try {
            await($this->connection->query(InitializeDatabase::getCreateUserTableSQL()));
        } catch (\Throwable $e) {
            echo $e->getMessage() . PHP_EOL;
        }
    }
    
    public function testCreateUser(): void
    {
        $user = $this->createUser();
        
        $result = $this->userManager->create($user);
        
        $this->assertTrue($result);
    }
    
    public function testUpdateUser(): void
    {
        $uuid = UuidGenerator::getCompactUuid4();
        $user = $this->createUser($uuid);
        $this->userManager->create($user);
        
        $user->setEmail('test2@example.com');
        $result = $this->userManager->update($user);
        
        $this->assertTrue($result);
        
        $updatedUser = $this->userManager->get($uuid);
        
        $this->assertEquals($user->getEmail(), $updatedUser->getEmail());
        
    }
    
    public function testGetUser(): void
    {
        $uuid = UuidGenerator::getCompactUuid4();
        
        $user = $this->createUser($uuid);
        try {
            $this->userManager->create($user);
        } catch (\Throwable $e) {
            echo $e->getMessage() . PHP_EOL;
        }

        $result = $this->userManager->get($uuid);
        
        $this->assertInstanceOf(User::class, $result);
    }
    
    public function testDeleteUser(): void
    {
        $uuid = UuidGenerator::getCompactUuid4();
        
        $user = $this->createUser($uuid);
        $deleteResult = false;
        $deletedUser = false;
        
        try { 
            $this->userManager->create($user);

            $deleteResult = $this->userManager->delete($user);
            $deletedUser = $this->userManager->get($uuid);

        } catch (\Throwable $e) {
            echo $e->getMessage() . PHP_EOL;
        }
        
        $this->assertTrue($deleteResult);
        $this->assertNull($deletedUser);

    }
    
    protected function createUser(?string $uuid = null): User
    {
        $uuid = $uuid ?? UuidGenerator::getCompactUuid4();
        
        return new User(
            email: 'test@example.com',
            password: 'testpassword',
            id: $uuid,
            roles: ['ROLE_USER']
        );
    }
    
    public function tearDown(): void
    {
        try {
            await($this->connection->query('TRUNCATE TABLE user'));
        } catch (\Throwable $e) {
            echo $e->getMessage() . PHP_EOL; ;
        }
        parent::tearDown();
    }


    public static function tearDownAfterClass(): void
    {
        $connection = (new Factory())->createLazyConnection('mysql://test:password@localhost:3307/test');
        try {
            await($connection->query('TRUNCATE TABLE user'));
            await($connection->quit());
        } catch (\Throwable $e) {
            echo $e->getMessage() . PHP_EOL; ;
        }
        
        parent::tearDownAfterClass();
    }
}
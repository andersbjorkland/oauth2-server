<?php

declare(strict_types=1);

namespace unit\Oauth\Controller;

use App\Database\Manager\UserManager;
use App\OAuth\Controller\RegisterController;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use React\Http\Message\ServerRequest;

class RegisterControllerTest extends TestCase
{
    public function testNoEmailAndPasswordReturnsErrorCode(): void
    {
        $request = new ServerRequest('POST', 'https://127.0.0.1/');
        $request = $request->withQueryParams(
            [
            ]
        );
        
        $controller = new RegisterController($this->createMock(UserManager::class));
        $response = $controller($request);
        
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testNoEmailReturnsErrorCode(): void
    {
        $request = new ServerRequest(
            'POST', 
            'https://127.0.0.1/',
            ['Content-Type' => 'application/json'],
            json_encode(['password' => 'abcdefghijkl'])
        );

        $controller = new RegisterController($this->createMock(UserManager::class));
        $response = $controller($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(400, $response->getStatusCode());

        $data = json_decode($response->getBody()->getContents(), true);
        $errorMessage = $data['error'];
        
        $this->assertStringContainsString('email', $errorMessage);
    }
    
    public function testNoPasswordReturnsErrorMessage(): void
    {
        $request = new ServerRequest(
            'POST',
            'https://127.0.0.1/',
            ['Content-Type' => 'application/json'],
            json_encode([
                'email' => 'test@example.com'
            ])
        );

        $controller = new RegisterController($this->createMock(UserManager::class));
        $response = $controller($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(400, $response->getStatusCode());

        $data = json_decode($response->getBody()->getContents(), true);
        $errorMessage = $data['error'];

        $this->assertStringContainsString('password', $errorMessage);
    }
    
    public function testEmailAndPasswordReturnsSuccessfulMessage(): void
    {
        $request = new ServerRequest(
            'POST',
            'https://127.0.0.1/',
            ['Content-Type' => 'application/json'],
            json_encode([
                'email' => 'test@example.com',
                'password' => 'abcdefghijkl'
            ])
        );

        $controller = new RegisterController($this->createMock(UserManager::class));
        $response = $controller($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
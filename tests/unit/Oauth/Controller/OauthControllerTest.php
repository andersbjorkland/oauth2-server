<?php

declare(strict_types=1);

namespace unit\Oauth\Controller;

use App\OAuth\Controller\OAuthController;
use League\OAuth2\Server\AuthorizationServer;
use PHPUnit\Framework\TestCase;

class OauthControllerTest extends TestCase
{
    private OAuthController $oAuthController;
    
    public function setup(): void
    {
        $this->oAuthController = new OAuthController();
    }
    
    public function testLoadsKey(): void
    {
        $this->oAuthController->server();
        
        $this->assertTrue(false);
    }

}
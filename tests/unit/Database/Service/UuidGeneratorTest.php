<?php

declare(strict_types=1);

namespace unit\Database\Service;

use PHPUnit\Framework\TestCase;

class UuidGeneratorTest extends TestCase
{
    
    public function testGetCompactUuid4ReturnsString(): void
    {
        $uuid4 = \App\Database\Service\UuidGenerator::getCompactUuid4();
        
        $this->assertIsString($uuid4);
        
        $this->assertEquals(32, strlen($uuid4));
    }

}
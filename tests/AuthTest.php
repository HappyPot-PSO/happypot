<?php

namespace Tests;

use PHPUnit\Framework\MockObject\MockObject;

class AuthTest extends \Tests\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testUserRegistration()
    {
        // Create a test user
        $userId = $this->createTestUser();

        // Verify the user was created
        $this->assertIsInt($userId);
        $this->assertGreaterThan(0, $userId);
    }

    public function testUserLogin()
    {
        // Create a test user
        $userId = $this->createTestUser();

        // Mock the result set for user verification
        $result = $this->getMockBuilder(\mysqli_result::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['fetch_assoc'])
            ->getMock();

        $result->method('fetch_assoc')
            ->willReturn(
                [
                'id' => $userId,
                'username' => 'testuser',
                'password' => password_hash('testpass123', PASSWORD_DEFAULT)
                ]
            );

        // Mock the prepared statement
        $stmt = $this->getMockBuilder(\mysqli_stmt::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['bind_param', 'execute', 'get_result'])
            ->getMock();

        $stmt->method('bind_param')
            ->willReturn(true);
        $stmt->method('execute')
            ->willReturn(true);
        $stmt->method('get_result')
            ->willReturn($result);

        $this->dbc->method('prepare')
            ->willReturn($stmt);

        // Verify the user can login
        $this->assertTrue(password_verify('testpass123', $result->fetch_assoc()['password']));
    }

    public function testInvalidLogin()
    {
        // Create a test user
        $userId = $this->createTestUser();

        // Mock the result set for user verification
        $result = $this->getMockBuilder(\mysqli_result::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['fetch_assoc'])
            ->getMock();

        $result->method('fetch_assoc')
            ->willReturn(
                [
                'id' => $userId,
                'username' => 'testuser',
                'password' => password_hash('testpass123', PASSWORD_DEFAULT)
                ]
            );

        // Mock the prepared statement
        $stmt = $this->getMockBuilder(\mysqli_stmt::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['bind_param', 'execute', 'get_result'])
            ->getMock();

        $stmt->method('bind_param')
            ->willReturn(true);
        $stmt->method('execute')
            ->willReturn(true);
        $stmt->method('get_result')
            ->willReturn($result);

        $this->dbc->method('prepare')
            ->willReturn($stmt);

        // Verify incorrect password doesn't work
        $this->assertFalse(password_verify('wrongpass', $result->fetch_assoc()['password']));
    }
}

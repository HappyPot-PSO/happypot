<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class TestCase extends BaseTestCase
{
    protected $dbc;
    protected $lastInsertId = 1;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create mock for mysqli
        $this->dbc = $this->getMockBuilder(\mysqli::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['prepare', 'query', 'close'])
            ->addMethods(['connect_error', 'getInsertId'])
            ->getMock();
        
        // Set up common mock expectations
        $this->dbc->method('prepare')
            ->willReturn($this->createMock(\mysqli_stmt::class));
            
        $this->dbc->method('query')
            ->willReturn($this->createMock(\mysqli_result::class));
            
        $this->dbc->method('connect_error')
            ->willReturn(null);

        $this->dbc->method('close')
            ->willReturn(true);

        $this->dbc->method('getInsertId')
            ->will(
                $this->returnCallback(
                    function () {
                        return $this->lastInsertId;
                    }
                )
            );
    }

    protected function tearDown(): void
    {
        $this->dbc = null;
        $this->lastInsertId = 1;
        parent::tearDown();
    }

    protected function createTestUser($username = 'testuser', $password = 'testpass123')
    {
        // Mock the result set for user verification
        $result = $this->getMockBuilder(\mysqli_result::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['fetch_assoc'])
            ->getMock();
            
        $result->method('fetch_assoc')
            ->willReturn(
                [
                'id' => $this->lastInsertId,
                'username' => $username,
                'password' => password_hash($password, PASSWORD_DEFAULT)
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

        return $this->lastInsertId++;
    }

    protected function createTestRecipe($userId)
    {
        // Mock the result set for recipe verification
        $result = $this->getMockBuilder(\mysqli_result::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['fetch_assoc'])
            ->getMock();
            
        $result->method('fetch_assoc')
            ->willReturn(
                [
                'id' => $this->lastInsertId,
                'user_id' => $userId,
                'title' => 'Test Recipe',
                'ingredients' => 'Test ingredients',
                'instructions' => 'Test instructions'
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

        return $this->lastInsertId++;
    }

    protected function cleanTestData()
    {
        // No cleanup needed for mocks
    }
} 

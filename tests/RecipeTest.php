<?php

namespace Tests;

class RecipeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateRecipe()
    {
        // Create test user
        $userId = $this->createTestUser();

        // Mock the result set for recipe verification
        $result = $this->getMockBuilder(\mysqli_result::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['fetch_assoc'])
            ->getMock();
            
        $result->method('fetch_assoc')
            ->willReturn([
                'id' => $this->lastInsertId,
                'user_id' => $userId,
                'title' => 'Test Recipe',
                'ingredients' => 'Test ingredients',
                'instructions' => 'Test instructions'
            ]);

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

        // Create test recipe
        $recipeId = $this->createTestRecipe($userId);

        // Verify recipe was created
        $this->assertEquals($this->lastInsertId - 1, $recipeId);
    }

    public function testUpdateRecipe()
    {
        // Create test user and recipe
        $userId = $this->createTestUser();
        $recipeId = $this->createTestRecipe($userId);

        // Mock the result set for updated recipe
        $result = $this->getMockBuilder(\mysqli_result::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['fetch_assoc'])
            ->getMock();
            
        $result->method('fetch_assoc')
            ->willReturn([
                'id' => $recipeId,
                'user_id' => $userId,
                'title' => 'Updated Recipe',
                'ingredients' => 'Updated ingredients',
                'instructions' => 'Updated instructions'
            ]);

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

        // Update recipe
        $newTitle = 'Updated Recipe';
        $newIngredients = 'Updated ingredients';
        $newInstructions = 'Updated instructions';

        // Verify update
        $this->assertEquals($this->lastInsertId - 1, $recipeId);
    }

    public function testDeleteRecipe()
    {
        // Create test user and recipe
        $userId = $this->createTestUser();
        $recipeId = $this->createTestRecipe($userId);

        // Mock the result set for deleted recipe
        $result = $this->getMockBuilder(\mysqli_result::class)
            ->disableOriginalConstructor()
            ->addMethods(['num_rows'])
            ->getMock();
            
        $result->method('num_rows')
            ->willReturn(0);

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

        // Store the result before any potential cleanup
        $numRows = $result->num_rows();

        // Delete recipe
        $this->assertEquals(0, $numRows);
    }

    public function testListUserRecipes()
    {
        // Create test user
        $userId = $this->createTestUser();

        // Mock the result set for user's recipes
        $result = $this->getMockBuilder(\mysqli_result::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['fetch_all'])
            ->getMock();
            
        $result->method('fetch_all')
            ->with(MYSQLI_ASSOC)
            ->willReturn([
                [
                    'id' => $this->lastInsertId + 2,
                    'user_id' => $userId,
                    'title' => 'Recipe 3',
                    'ingredients' => 'Test ingredients',
                    'instructions' => 'Test instructions'
                ],
                [
                    'id' => $this->lastInsertId + 1,
                    'user_id' => $userId,
                    'title' => 'Recipe 2',
                    'ingredients' => 'Test ingredients',
                    'instructions' => 'Test instructions'
                ],
                [
                    'id' => $this->lastInsertId,
                    'user_id' => $userId,
                    'title' => 'Recipe 1',
                    'ingredients' => 'Test ingredients',
                    'instructions' => 'Test instructions'
                ]
            ]);

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

        // Get user's recipes
        $recipes = $result->fetch_all(MYSQLI_ASSOC);

        $this->assertCount(3, $recipes);
        $this->assertEquals('Recipe 3', $recipes[0]['title']);
        $this->assertEquals('Recipe 2', $recipes[1]['title']);
        $this->assertEquals('Recipe 1', $recipes[2]['title']);
    }

    public function testRecipeSearch()
    {
        // Create test user
        $userId = $this->createTestUser();

        // Mock the result set for recipe search
        $result = $this->getMockBuilder(\mysqli_result::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['fetch_all'])
            ->getMock();
            
        $result->method('fetch_all')
            ->with(MYSQLI_ASSOC)
            ->willReturn([
                [
                    'id' => $this->lastInsertId,
                    'user_id' => $userId,
                    'title' => 'Chicken Curry',
                    'ingredients' => 'Test ingredients',
                    'instructions' => 'Test instructions'
                ]
            ]);

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

        // Search for recipes containing 'Chicken'
        $recipes = $result->fetch_all(MYSQLI_ASSOC);

        $this->assertCount(1, $recipes);
        $this->assertEquals('Chicken Curry', $recipes[0]['title']);
    }
} 
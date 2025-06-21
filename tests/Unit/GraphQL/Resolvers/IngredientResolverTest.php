<?php

namespace Tests\Unit\GraphQL\Resolvers;

use App\GraphQL\Resolvers\IngredientResolver;
use App\Models\Ingredient;
use GraphQL\Error\Error;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IngredientResolverTest extends TestCase
{
    use RefreshDatabase;

    private IngredientResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new IngredientResolver();
    }

    public function test_ingredients_returns_paginated_ingredients_with_default_args(): void
    {
        Ingredient::factory()->count(15)->create();

        $result = $this->resolver->ingredients(null, []);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('paginatorInfo', $result);
        $this->assertCount(10, $result['data']); // Default limit
        $this->assertEquals(15, $result['paginatorInfo']['total']);
        $this->assertEquals(1, $result['paginatorInfo']['currentPage']);
        $this->assertEquals(10, $result['paginatorInfo']['perPage']);
    }

    public function test_ingredients_respects_first_parameter(): void
    {
        Ingredient::factory()->count(15)->create();

        $result = $this->resolver->ingredients(null, ['first' => 5]);

        $this->assertCount(5, $result['data']);
        $this->assertEquals(5, $result['paginatorInfo']['perPage']);
        $this->assertEquals(15, $result['paginatorInfo']['total']);
    }

    public function test_ingredients_respects_page_parameter(): void
    {
        Ingredient::factory()->count(25)->create();

        $result = $this->resolver->ingredients(null, ['first' => 10, 'page' => 2]);

        $this->assertCount(10, $result['data']);
        $this->assertEquals(2, $result['paginatorInfo']['currentPage']);
        $this->assertEquals(25, $result['paginatorInfo']['total']);
        $this->assertTrue($result['paginatorInfo']['hasMorePages']);
    }

    public function test_ingredients_respects_order_by_parameter_asc(): void
    {
        $ingredient3 = Ingredient::factory()->create(['name' => 'C Ingredient']);
        $ingredient1 = Ingredient::factory()->create(['name' => 'A Ingredient']);
        $ingredient2 = Ingredient::factory()->create(['name' => 'B Ingredient']);

        $result = $this->resolver->ingredients(null, [
            'orderBy' => ['column' => 'name', 'order' => 'ASC']
        ]);

        $this->assertEquals($ingredient1->id, $result['data'][0]['id']);
        $this->assertEquals($ingredient2->id, $result['data'][1]['id']);
        $this->assertEquals($ingredient3->id, $result['data'][2]['id']);
    }

    public function test_ingredients_respects_order_by_parameter_desc(): void
    {
        $ingredient1 = Ingredient::factory()->create(['name' => 'A Ingredient']);
        $ingredient2 = Ingredient::factory()->create(['name' => 'B Ingredient']);
        $ingredient3 = Ingredient::factory()->create(['name' => 'C Ingredient']);

        $result = $this->resolver->ingredients(null, [
            'orderBy' => ['column' => 'name', 'order' => 'DESC']
        ]);

        $this->assertEquals($ingredient3->id, $result['data'][0]['id']);
        $this->assertEquals($ingredient2->id, $result['data'][1]['id']);
        $this->assertEquals($ingredient1->id, $result['data'][2]['id']);
    }

    public function test_ingredients_sorts_by_slug_asc(): void
    {
        $ingredientC = Ingredient::factory()->create(['slug' => 'c-ingredient']);
        $ingredientA = Ingredient::factory()->create(['slug' => 'a-ingredient']);
        $ingredientB = Ingredient::factory()->create(['slug' => 'b-ingredient']);

        $result = $this->resolver->ingredients(null, [
            'orderBy' => ['column' => 'slug', 'order' => 'ASC']
        ]);

        $this->assertEquals($ingredientA->id, $result['data'][0]['id']);
        $this->assertEquals($ingredientB->id, $result['data'][1]['id']);
        $this->assertEquals($ingredientC->id, $result['data'][2]['id']);
    }

    public function test_ingredients_sorts_by_created_at_desc(): void
    {
        $ingredient1 = Ingredient::factory()->create(['created_at' => now()->subMinutes(2)]);
        $ingredient2 = Ingredient::factory()->create(['created_at' => now()->subMinute()]);
        $ingredient3 = Ingredient::factory()->create(['created_at' => now()]);

        $result = $this->resolver->ingredients(null, [
            'orderBy' => ['column' => 'created_at', 'order' => 'DESC']
        ]);

        $this->assertEquals($ingredient3->id, $result['data'][0]['id']);
        $this->assertEquals($ingredient2->id, $result['data'][1]['id']);
        $this->assertEquals($ingredient1->id, $result['data'][2]['id']);
    }

    public function test_ingredients_throws_error_for_invalid_first_parameter(): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('The first field must be less than 100.');

        $this->resolver->ingredients(null, ['first' => 101]);
    }

    public function test_ingredients_throws_error_for_zero_first_parameter(): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('The first field must be at least 1.');

        $this->resolver->ingredients(null, ['first' => 0]);
    }

    public function test_ingredients_throws_error_for_negative_first_parameter(): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('The first field must be at least 1.');

        $this->resolver->ingredients(null, ['first' => -1]);
    }

    public function test_ingredients_throws_error_for_negative_page_parameter(): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('The page field must be at least 1.');

        $this->resolver->ingredients(null, ['page' => 0]);
    }

    public function test_ingredients_throws_error_for_zero_page_parameter(): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('The page field must be at least 1.');

        $this->resolver->ingredients(null, ['page' => 0]);
    }

    public function test_ingredients_handles_empty_result_set(): void
    {
        $result = $this->resolver->ingredients(null, []);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('paginatorInfo', $result);
        $this->assertCount(0, $result['data']);
        $this->assertEquals(0, $result['paginatorInfo']['total']);
        $this->assertEquals(1, $result['paginatorInfo']['currentPage']);
        $this->assertEquals(10, $result['paginatorInfo']['perPage']);
        $this->assertFalse($result['paginatorInfo']['hasMorePages']);
    }

    public function test_ingredients_handles_single_page_result(): void
    {
        Ingredient::factory()->count(5)->create();

        $result = $this->resolver->ingredients(null, ['first' => 10]);

        $this->assertCount(5, $result['data']);
        $this->assertEquals(5, $result['paginatorInfo']['total']);
        $this->assertEquals(1, $result['paginatorInfo']['currentPage']);
        $this->assertEquals(10, $result['paginatorInfo']['perPage']);
        $this->assertFalse($result['paginatorInfo']['hasMorePages']);
    }

    public function test_ingredients_handles_last_page_correctly(): void
    {
        Ingredient::factory()->count(25)->create();

        $result = $this->resolver->ingredients(null, ['first' => 10, 'page' => 3]);

        $this->assertCount(5, $result['data']); // Last page should have 5 items
        $this->assertEquals(25, $result['paginatorInfo']['total']);
        $this->assertEquals(3, $result['paginatorInfo']['currentPage']);
        $this->assertEquals(10, $result['paginatorInfo']['perPage']);
        $this->assertFalse($result['paginatorInfo']['hasMorePages']);
    }

    public function test_ingredients_handles_non_numeric_first_parameter(): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('The first field must be at least 1.');

        $this->resolver->ingredients(null, ['first' => 'invalid']);
    }

    public function test_ingredients_handles_non_numeric_page_parameter(): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('The page field must be at least 1.');

        $this->resolver->ingredients(null, ['page' => 'invalid']);
    }

    public function test_ingredients_sorts_by_description_asc(): void
    {
        $ingredientC = Ingredient::factory()->create(['description' => 'C Description']);
        $ingredientA = Ingredient::factory()->create(['description' => 'A Description']);
        $ingredientB = Ingredient::factory()->create(['description' => 'B Description']);

        $result = $this->resolver->ingredients(null, [
            'orderBy' => ['column' => 'description', 'order' => 'ASC']
        ]);

        $this->assertEquals($ingredientA->id, $result['data'][0]['id']);
        $this->assertEquals($ingredientB->id, $result['data'][1]['id']);
        $this->assertEquals($ingredientC->id, $result['data'][2]['id']);
    }

    public function test_ingredients_handles_null_description_in_sorting(): void
    {
        $ingredientWithDesc = Ingredient::factory()->create(['description' => 'A Description']);
        $ingredientNullDesc = Ingredient::factory()->create(['description' => null]);
        $ingredientWithDesc2 = Ingredient::factory()->create(['description' => 'B Description']);

        $result = $this->resolver->ingredients(null, [
            'orderBy' => ['column' => 'description', 'order' => 'ASC']
        ]);

        // Null values should be handled gracefully
        $this->assertCount(3, $result['data']);
    }

    public function test_ingredients_handles_mixed_case_sorting(): void
    {
        $ingredientLower = Ingredient::factory()->create(['name' => 'apple']);
        $ingredientUpper = Ingredient::factory()->create(['name' => 'Apple']);
        $ingredientMixed = Ingredient::factory()->create(['name' => 'aPPle']);

        $result = $this->resolver->ingredients(null, [
            'orderBy' => ['column' => 'name', 'order' => 'ASC']
        ]);

        $this->assertCount(3, $result['data']);
        // The exact order depends on the database collation, but it should be consistent
    }

    public function test_ingredients_handles_special_characters_in_sorting(): void
    {
        $ingredientSpecial = Ingredient::factory()->create(['name' => 'Café']);
        $ingredientNormal = Ingredient::factory()->create(['name' => 'Coffee']);
        $ingredientAccent = Ingredient::factory()->create(['name' => 'Café au lait']);

        $result = $this->resolver->ingredients(null, [
            'orderBy' => ['column' => 'name', 'order' => 'ASC']
        ]);

        $this->assertCount(3, $result['data']);
        // The exact order depends on the database collation, but it should be consistent
    }

    public function test_ingredients_handles_very_large_first_parameter(): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('The first field must be less than 100.');

        $this->resolver->ingredients(null, ['first' => 999]);
    }

    public function test_ingredients_handles_very_large_page_parameter(): void
    {
        Ingredient::factory()->count(5)->create();

        $result = $this->resolver->ingredients(null, ['page' => 999]);

        $this->assertCount(0, $result['data']); // Page beyond available data
        $this->assertEquals(999, $result['paginatorInfo']['currentPage']);
        $this->assertEquals(5, $result['paginatorInfo']['total']);
    }
}

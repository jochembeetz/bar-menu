<?php

namespace Tests\Unit\GraphQL\Resolvers;

use App\GraphQL\Resolvers\CategoryResolver;
use App\Models\Category;
use App\Models\Product;
use App\Models\Ingredient;
use App\Services\CategoryService;
use GraphQL\Error\Error;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryResolverTest extends TestCase
{
    use RefreshDatabase;

    private CategoryResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new CategoryResolver(app(CategoryService::class));
    }

    public function test_categories_returns_paginated_categories_with_default_args(): void
    {
        Category::factory()->count(15)->create();

        $result = $this->resolver->categories(null, []);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('paginatorInfo', $result);
        $this->assertCount(10, $result['data']); // Default limit
        $this->assertEquals(15, $result['paginatorInfo']['total']);
        $this->assertEquals(1, $result['paginatorInfo']['currentPage']);
        $this->assertEquals(10, $result['paginatorInfo']['perPage']);
    }

    public function test_categories_respects_first_parameter(): void
    {
        Category::factory()->count(15)->create();

        $result = $this->resolver->categories(null, ['first' => 5]);

        $this->assertCount(5, $result['data']);
        $this->assertEquals(5, $result['paginatorInfo']['perPage']);
        $this->assertEquals(15, $result['paginatorInfo']['total']);
    }

    public function test_categories_respects_page_parameter(): void
    {
        Category::factory()->count(25)->create();

        $result = $this->resolver->categories(null, ['first' => 10, 'page' => 2]);

        $this->assertCount(10, $result['data']);
        $this->assertEquals(2, $result['paginatorInfo']['currentPage']);
        $this->assertEquals(25, $result['paginatorInfo']['total']);
        $this->assertTrue($result['paginatorInfo']['hasMorePages']);
    }

    public function test_categories_respects_order_by_parameter(): void
    {
        $category3 = Category::factory()->create(['sort_order' => 3]);
        $category1 = Category::factory()->create(['sort_order' => 1]);
        $category2 = Category::factory()->create(['sort_order' => 2]);

        $result = $this->resolver->categories(null, [
            'orderBy' => ['column' => 'sort_order', 'order' => 'ASC']
        ]);

        $this->assertEquals($category1->id, $result['data'][0]['id']);
        $this->assertEquals($category2->id, $result['data'][1]['id']);
        $this->assertEquals($category3->id, $result['data'][2]['id']);
    }

    public function test_categories_throws_error_for_invalid_first_parameter(): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('The first field must be less than 100.');

        $this->resolver->categories(null, ['first' => 101]);
    }

    public function test_categories_throws_error_for_zero_first_parameter(): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('The first field must be at least 1.');

        $this->resolver->categories(null, ['first' => 0]);
    }

    public function test_categories_throws_error_for_negative_page_parameter(): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('The page field must be at least 1.');

        $this->resolver->categories(null, ['page' => 0]);
    }

    public function test_products_returns_paginated_products_for_category(): void
    {
        $category = Category::factory()->withProducts(15)->create();

        $result = $this->resolver->products($category, []);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('paginatorInfo', $result);
        $this->assertCount(10, $result['data']); // Default limit
        $this->assertEquals(15, $result['paginatorInfo']['total']);
        $this->assertEquals(1, $result['paginatorInfo']['currentPage']);
        $this->assertEquals(10, $result['paginatorInfo']['perPage']);
    }

    public function test_products_includes_ingredients_relationship(): void
    {
        $category = Category::factory()->withSpecificProducts([
            ['attributes' => [], 'sort_order' => 1],
        ])->create();

        // Add ingredients to the first product
        $product = $category->products()->first();
        $ingredients = Ingredient::factory()->count(2)->create();
        $product->ingredients()->attach([
            $ingredients[0]->id => ['type' => 'base'],
            $ingredients[1]->id => ['type' => 'optional'],
        ]);

        $result = $this->resolver->products($category, []);

        $this->assertCount(1, $result['data']);
        $this->assertTrue($result['data'][0]->relationLoaded('ingredients'));
        $this->assertCount(2, $result['data'][0]->ingredients);
    }

    public function test_products_respects_pagination_parameters(): void
    {
        $category = Category::factory()->withProducts(25)->create();

        $result = $this->resolver->products($category, ['first' => 5, 'page' => 2]);

        $this->assertCount(5, $result['data']);
        $this->assertEquals(2, $result['paginatorInfo']['currentPage']);
        $this->assertEquals(25, $result['paginatorInfo']['total']);
        $this->assertTrue($result['paginatorInfo']['hasMorePages']);
    }

    public function test_products_respects_sorting_parameters(): void
    {
        $category = Category::factory()->withSpecificProducts([
            ['attributes' => [], 'sort_order' => 1],
            ['attributes' => [], 'sort_order' => 2],
            ['attributes' => [], 'sort_order' => 3],
        ])->create();

        $result = $this->resolver->products($category, [
            'orderBy' => ['column' => 'sort_order', 'order' => 'ASC']
        ]);

        $products = $result['data'];
        $this->assertEquals(1, $products[0]['id']);
        $this->assertEquals(2, $products[1]['id']);
        $this->assertEquals(3, $products[2]['id']);
    }

    public function test_products_throws_error_for_invalid_pagination(): void
    {
        $category = Category::factory()->create();

        $this->expectException(Error::class);
        $this->expectExceptionMessage('The first field must be less than 100.');

        $this->resolver->products($category, ['first' => 101]);
    }

    public function test_categoryProducts_returns_sorted_products_without_pagination(): void
    {
        $category = Category::factory()->withSpecificProducts([
            ['attributes' => [], 'sort_order' => 1],
            ['attributes' => [], 'sort_order' => 2],
            ['attributes' => [], 'sort_order' => 3],
        ])->create();

        $result = $this->resolver->categoryProducts($category, [
            'orderBy' => ['column' => 'sort_order', 'order' => 'ASC']
        ]);

        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        $this->assertEquals(1, $result[0]['id']);
        $this->assertEquals(2, $result[1]['id']);
        $this->assertEquals(3, $result[2]['id']);
    }

    public function test_categoryProducts_includes_ingredients_relationship(): void
    {
        $category = Category::factory()->withSpecificProducts([
            ['attributes' => [], 'sort_order' => 1],
        ])->create();

        // Add ingredients to the first product
        $product = $category->products()->first();
        $ingredient = Ingredient::factory()->create();
        $product->ingredients()->attach($ingredient->id, ['type' => 'base']);

        $result = $this->resolver->categoryProducts($category, []);

        $this->assertCount(1, $result);
        $this->assertTrue($result[0]->relationLoaded('ingredients'));
        $this->assertCount(1, $result[0]->ingredients);
    }

    public function test_categoryProducts_ignores_pagination_parameters(): void
    {
        $category = Category::factory()->withProducts(15)->create();

        $result = $this->resolver->categoryProducts($category, ['first' => 5, 'page' => 2]);

        $this->assertIsArray($result);
        $this->assertCount(15, $result); // All products returned, no pagination
    }

    public function test_categoryProducts_uses_default_sorting_when_no_order_by(): void
    {
        $category = Category::factory()->withSpecificProducts([
            ['attributes' => [], 'sort_order' => 1],
            ['attributes' => [], 'sort_order' => 2],
            ['attributes' => [], 'sort_order' => 3],
        ])->create();

        $result = $this->resolver->categoryProducts($category, []);

        $this->assertCount(3, $result);
        $this->assertEquals(1, $result[0]['id']);
        $this->assertEquals(2, $result[1]['id']);
        $this->assertEquals(3, $result[2]['id']);
    }
}

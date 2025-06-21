<?php

namespace Tests\Unit\GraphQL\Resolvers;

use App\GraphQL\Resolvers\ProductResolver;
use App\Models\Product;
use GraphQL\Error\Error;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductResolverTest extends TestCase
{
    use RefreshDatabase;

    private ProductResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new ProductResolver();
    }

    public function test_products_returns_paginated_products_with_default_args(): void
    {
        Product::factory()->count(15)->create();

        $result = $this->resolver->products(null, []);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('paginatorInfo', $result);
        $this->assertCount(10, $result['data']); // Default limit
        $this->assertEquals(15, $result['paginatorInfo']['total']);
        $this->assertEquals(1, $result['paginatorInfo']['currentPage']);
        $this->assertEquals(10, $result['paginatorInfo']['perPage']);
    }

    public function test_products_respects_first_parameter(): void
    {
        Product::factory()->count(15)->create();

        $result = $this->resolver->products(null, ['first' => 5]);

        $this->assertCount(5, $result['data']);
        $this->assertEquals(5, $result['paginatorInfo']['perPage']);
        $this->assertEquals(15, $result['paginatorInfo']['total']);
    }

    public function test_products_respects_page_parameter(): void
    {
        Product::factory()->count(25)->create();

        $result = $this->resolver->products(null, ['first' => 10, 'page' => 2]);

        $this->assertCount(10, $result['data']);
        $this->assertEquals(2, $result['paginatorInfo']['currentPage']);
        $this->assertEquals(25, $result['paginatorInfo']['total']);
        $this->assertTrue($result['paginatorInfo']['hasMorePages']);
    }


    public function test_products_sorts_by_name_asc(): void
    {
        $productC = Product::factory()->create(['name' => 'C Product']);
        $productA = Product::factory()->create(['name' => 'A Product']);
        $productB = Product::factory()->create(['name' => 'B Product']);

        $result = $this->resolver->products(null, [
            'orderBy' => ['column' => 'name', 'order' => 'ASC']
        ]);

        $this->assertEquals($productA->id, $result['data'][0]['id']);
        $this->assertEquals($productB->id, $result['data'][1]['id']);
        $this->assertEquals($productC->id, $result['data'][2]['id']);
    }

    public function test_products_sorts_by_price_in_cents_desc(): void
    {
        $productCheap = Product::factory()->create(['price_in_cents' => 500]);
        $productExpensive = Product::factory()->create(['price_in_cents' => 2000]);
        $productMedium = Product::factory()->create(['price_in_cents' => 1000]);

        $result = $this->resolver->products(null, [
            'orderBy' => ['column' => 'price_in_cents', 'order' => 'DESC']
        ]);

        $this->assertEquals($productExpensive->id, $result['data'][0]['id']);
        $this->assertEquals($productMedium->id, $result['data'][1]['id']);
        $this->assertEquals($productCheap->id, $result['data'][2]['id']);
    }

    public function test_products_sorts_by_created_at_asc(): void
    {
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();
        $product3 = Product::factory()->create();

        $result = $this->resolver->products(null, [
            'orderBy' => ['column' => 'created_at', 'order' => 'ASC']
        ]);

        $this->assertEquals($product1->id, $result['data'][0]['id']);
        $this->assertEquals($product2->id, $result['data'][1]['id']);
        $this->assertEquals($product3->id, $result['data'][2]['id']);
    }

    public function test_products_throws_error_for_invalid_first_parameter(): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('The first field must be less than 100.');

        $this->resolver->products(null, ['first' => 101]);
    }

    public function test_products_throws_error_for_zero_first_parameter(): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('The first field must be at least 1.');

        $this->resolver->products(null, ['first' => 0]);
    }

    public function test_products_throws_error_for_negative_first_parameter(): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('The first field must be at least 1.');

        $this->resolver->products(null, ['first' => -1]);
    }

    public function test_products_throws_error_for_negative_page_parameter(): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('The page field must be at least 1.');

        $this->resolver->products(null, ['page' => 0]);
    }

    public function test_products_throws_error_for_zero_page_parameter(): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('The page field must be at least 1.');

        $this->resolver->products(null, ['page' => 0]);
    }

    public function test_products_handles_empty_result_set(): void
    {
        $result = $this->resolver->products(null, []);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('paginatorInfo', $result);
        $this->assertCount(0, $result['data']);
        $this->assertEquals(0, $result['paginatorInfo']['total']);
        $this->assertEquals(1, $result['paginatorInfo']['currentPage']);
        $this->assertEquals(10, $result['paginatorInfo']['perPage']);
        $this->assertFalse($result['paginatorInfo']['hasMorePages']);
    }

    public function test_products_handles_single_page_result(): void
    {
        Product::factory()->count(5)->create();

        $result = $this->resolver->products(null, ['first' => 10]);

        $this->assertCount(5, $result['data']);
        $this->assertEquals(5, $result['paginatorInfo']['total']);
        $this->assertEquals(1, $result['paginatorInfo']['currentPage']);
        $this->assertEquals(10, $result['paginatorInfo']['perPage']);
        $this->assertFalse($result['paginatorInfo']['hasMorePages']);
    }

    public function test_products_handles_last_page_correctly(): void
    {
        Product::factory()->count(25)->create();

        $result = $this->resolver->products(null, ['first' => 10, 'page' => 3]);

        $this->assertCount(5, $result['data']); // Last page should have 5 items
        $this->assertEquals(25, $result['paginatorInfo']['total']);
        $this->assertEquals(3, $result['paginatorInfo']['currentPage']);
        $this->assertEquals(10, $result['paginatorInfo']['perPage']);
        $this->assertFalse($result['paginatorInfo']['hasMorePages']);
    }

    public function test_products_handles_non_numeric_first_parameter(): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('The first field must be at least 1.');

        $this->resolver->products(null, ['first' => 'invalid']);
    }

    public function test_products_handles_non_numeric_page_parameter(): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('The page field must be at least 1.');

        $this->resolver->products(null, ['page' => 'invalid']);
    }
}

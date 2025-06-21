<?php

declare(strict_types=1);

namespace Tests\Unit\GraphQL\Resources;

use App\GraphQL\Resources\PaginatedResponse;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PaginatedResponseTest extends TestCase
{
    use RefreshDatabase;

    public function test_formats_paginator_correctly(): void
    {
        Category::factory()->count(15)->create();

        $paginator = Category::query()->paginate(10, ['*'], 'page', 1);

        $result = PaginatedResponse::fromPaginator($paginator);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('paginatorInfo', $result);

        $this->assertCount(10, $result['data']);
        $this->assertEquals(10, $result['paginatorInfo']['count']);
        $this->assertEquals(1, $result['paginatorInfo']['currentPage']);
        $this->assertEquals(10, $result['paginatorInfo']['perPage']);
        $this->assertEquals(15, $result['paginatorInfo']['total']);
        $this->assertTrue($result['paginatorInfo']['hasMorePages']);
        $this->assertEquals(2, $result['paginatorInfo']['lastPage']);
        $this->assertEquals(1, $result['paginatorInfo']['firstItem']);
        $this->assertEquals(10, $result['paginatorInfo']['lastItem']);
    }

    public function test_handles_empty_paginator(): void
    {
        $paginator = Category::query()->paginate(10, ['*'], 'page', 1);

        $result = PaginatedResponse::fromPaginator($paginator);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('paginatorInfo', $result);

        $this->assertCount(0, $result['data']);
        $this->assertEquals(0, $result['paginatorInfo']['count']);
        $this->assertEquals(1, $result['paginatorInfo']['currentPage']);
        $this->assertEquals(10, $result['paginatorInfo']['perPage']);
        $this->assertEquals(0, $result['paginatorInfo']['total']);
        $this->assertFalse($result['paginatorInfo']['hasMorePages']);
        $this->assertEquals(1, $result['paginatorInfo']['lastPage']);
        $this->assertNull($result['paginatorInfo']['firstItem']);
        $this->assertNull($result['paginatorInfo']['lastItem']);
    }

    public function test_handles_last_page(): void
    {
        Category::factory()->count(5)->create();

        $paginator = Category::query()->paginate(10, ['*'], 'page', 1);

        $result = PaginatedResponse::fromPaginator($paginator);

        $this->assertCount(5, $result['data']);
        $this->assertEquals(5, $result['paginatorInfo']['count']);
        $this->assertEquals(1, $result['paginatorInfo']['currentPage']);
        $this->assertEquals(10, $result['paginatorInfo']['perPage']);
        $this->assertEquals(5, $result['paginatorInfo']['total']);
        $this->assertFalse($result['paginatorInfo']['hasMorePages']);
        $this->assertEquals(1, $result['paginatorInfo']['lastPage']);
        $this->assertEquals(1, $result['paginatorInfo']['firstItem']);
        $this->assertEquals(5, $result['paginatorInfo']['lastItem']);
    }
}

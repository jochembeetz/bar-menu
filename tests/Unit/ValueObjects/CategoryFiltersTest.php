<?php

declare(strict_types=1);

namespace Tests\Unit\ValueObjects;

use App\ValueObjects\CategoryFilters;
use App\ValueObjects\PaginationOptions;
use App\ValueObjects\SortingOptions;
use InvalidArgumentException;
use Tests\TestCase;

final class CategoryFiltersTest extends TestCase
{
    public function test_creates_with_pagination()
    {
        $filters = CategoryFilters::withPagination([
            'sortBy' => 'name',
            'sortOrder' => 'desc',
            'limit' => 25,
            'page' => 3,
        ]);

        $this->assertInstanceOf(SortingOptions::class, $filters->sorting);
        $this->assertInstanceOf(PaginationOptions::class, $filters->pagination);
        $this->assertEquals('name', $filters->sorting->sortBy);
        $this->assertEquals('desc', $filters->sorting->sortOrder);
        $this->assertEquals(25, $filters->pagination->limit);
        $this->assertEquals(3, $filters->pagination->page);
    }

    public function test_creates_sorting_only()
    {
        $filters = CategoryFilters::sortingOnly([
            'sortBy' => 'created_at',
            'sortOrder' => 'asc',
        ]);

        $this->assertInstanceOf(SortingOptions::class, $filters->sorting);
        $this->assertNull($filters->pagination);
        $this->assertEquals('created_at', $filters->sorting->sortBy);
        $this->assertEquals('asc', $filters->sorting->sortOrder);
    }

    public function test_creates_from_graphql_args()
    {
        $filters = CategoryFilters::fromGraphQLArgs([
            'orderBy' => [
                'column' => 'price',
                'order' => 'desc',
            ],
            'first' => 50,
            'page' => 2,
        ]);

        $this->assertInstanceOf(SortingOptions::class, $filters->sorting);
        $this->assertInstanceOf(PaginationOptions::class, $filters->pagination);
        $this->assertEquals('price', $filters->sorting->sortBy);
        $this->assertEquals('desc', $filters->sorting->sortOrder);
        $this->assertEquals(50, $filters->pagination->limit);
        $this->assertEquals(2, $filters->pagination->page);
    }

    public function test_creates_from_graphql_args_sorting_only()
    {
        $filters = CategoryFilters::fromGraphQLArgsSortingOnly([
            'orderBy' => [
                'column' => 'name',
                'order' => 'asc',
            ],
        ]);

        $this->assertInstanceOf(SortingOptions::class, $filters->sorting);
        $this->assertNull($filters->pagination);
        $this->assertEquals('name', $filters->sorting->sortBy);
        $this->assertEquals('asc', $filters->sorting->sortOrder);
    }

    public function test_has_pagination()
    {
        $withPagination = CategoryFilters::withPagination(['limit' => 10, 'page' => 1]);
        $sortingOnly = CategoryFilters::sortingOnly([]);

        $this->assertTrue($withPagination->hasPagination());
        $this->assertFalse($sortingOnly->hasPagination());
    }

    public function test_get_pagination_returns_pagination_options()
    {
        $filters = CategoryFilters::withPagination(['limit' => 15, 'page' => 2]);
        $pagination = $filters->getPagination();

        $this->assertInstanceOf(PaginationOptions::class, $pagination);
        $this->assertEquals(15, $pagination->limit);
        $this->assertEquals(2, $pagination->page);
    }

    public function test_get_pagination_throws_when_not_available()
    {
        $filters = CategoryFilters::sortingOnly([]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Pagination options not available');

        $filters->getPagination();
    }

    public function test_to_array_with_pagination()
    {
        $filters = CategoryFilters::withPagination([
            'sortBy' => 'name',
            'sortOrder' => 'desc',
            'limit' => 25,
            'page' => 3,
        ]);

        $array = $filters->toArray();

        $this->assertEquals([
            'sortBy' => 'name',
            'sortOrder' => 'desc',
            'limit' => 25,
            'page' => 3,
        ], $array);
    }

    public function test_to_array_sorting_only()
    {
        $filters = CategoryFilters::sortingOnly([
            'sortBy' => 'created_at',
            'sortOrder' => 'asc',
        ]);

        $array = $filters->toArray();

        $this->assertEquals([
            'sortBy' => 'created_at',
            'sortOrder' => 'asc',
        ], $array);
    }
}

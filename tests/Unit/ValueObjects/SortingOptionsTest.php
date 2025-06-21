<?php

namespace Tests\Unit\ValueObjects;

use App\ValueObjects\SortingOptions;
use InvalidArgumentException;
use Tests\TestCase;

class SortingOptionsTest extends TestCase
{
    public function test_creates_with_valid_parameters()
    {
        $sorting = new SortingOptions('name', 'asc');

        $this->assertEquals('name', $sorting->sortBy);
        $this->assertEquals('asc', $sorting->sortOrder);
    }

    public function test_creates_with_uppercase_sort_order()
    {
        $sorting = new SortingOptions('price_in_cents', 'DESC');

        $this->assertEquals('price_in_cents', $sorting->sortBy);
        $this->assertEquals('DESC', $sorting->sortOrder);
    }

    public function test_from_array_with_all_parameters()
    {
        $sorting = SortingOptions::fromArray([
            'sortBy' => 'created_at',
            'sortOrder' => 'desc',
        ]);

        $this->assertEquals('created_at', $sorting->sortBy);
        $this->assertEquals('desc', $sorting->sortOrder);
    }

    public function test_from_array_with_defaults()
    {
        $sorting = SortingOptions::fromArray([]);

        $this->assertEquals('sort_order', $sorting->sortBy);
        $this->assertEquals('asc', $sorting->sortOrder);
    }

    public function test_from_array_with_partial_parameters()
    {
        $sorting = SortingOptions::fromArray([
            'sortBy' => 'name',
        ]);

        $this->assertEquals('name', $sorting->sortBy);
        $this->assertEquals('asc', $sorting->sortOrder);
    }

    public function test_from_graphql_args_with_all_parameters()
    {
        $sorting = SortingOptions::fromGraphQLArgs([
            'orderBy' => [
                'column' => 'price_in_cents',
                'order' => 'DESC',
            ],
        ]);

        $this->assertEquals('price_in_cents', $sorting->sortBy);
        $this->assertEquals('DESC', $sorting->sortOrder);
    }

    public function test_from_graphql_args_with_defaults()
    {
        $sorting = SortingOptions::fromGraphQLArgs([]);

        $this->assertEquals('sort_order', $sorting->sortBy);
        $this->assertEquals('asc', $sorting->sortOrder);
    }

    public function test_from_graphql_args_with_partial_order_by()
    {
        $sorting = SortingOptions::fromGraphQLArgs([
            'orderBy' => [
                'column' => 'name',
            ],
        ]);

        $this->assertEquals('name', $sorting->sortBy);
        $this->assertEquals('asc', $sorting->sortOrder);
    }

    public function test_from_graphql_args_with_empty_order_by()
    {
        $sorting = SortingOptions::fromGraphQLArgs([
            'orderBy' => [],
        ]);

        $this->assertEquals('sort_order', $sorting->sortBy);
        $this->assertEquals('asc', $sorting->sortOrder);
    }

    public function test_throws_exception_for_invalid_sort_order()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Sort order must be "asc" or "desc"');

        new SortingOptions('name', 'invalid');
    }

    public function test_throws_exception_for_invalid_sort_order_case_insensitive()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Sort order must be "asc" or "desc"');

        new SortingOptions('name', 'ASCENDING');
    }

    public function test_accepts_various_valid_sort_by_fields()
    {
        $validFields = ['name', 'price_in_cents', 'created_at', 'sort_order', 'slug', 'description'];

        foreach ($validFields as $field) {
            $sorting = new SortingOptions($field, 'asc');
            $this->assertEquals($field, $sorting->sortBy);
        }
    }
}

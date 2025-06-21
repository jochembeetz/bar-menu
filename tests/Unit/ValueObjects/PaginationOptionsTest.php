<?php

namespace Tests\Unit\ValueObjects;

use App\ValueObjects\PaginationOptions;
use InvalidArgumentException;
use Tests\TestCase;

class PaginationOptionsTest extends TestCase
{
    public function test_creates_with_valid_parameters()
    {
        $pagination = new PaginationOptions(25, 2);

        $this->assertEquals(25, $pagination->limit);
        $this->assertEquals(2, $pagination->page);
    }

    public function test_creates_with_minimum_values()
    {
        $pagination = new PaginationOptions(1, 1);

        $this->assertEquals(1, $pagination->limit);
        $this->assertEquals(1, $pagination->page);
    }

    public function test_creates_with_maximum_limit()
    {
        $pagination = new PaginationOptions(100, 5);

        $this->assertEquals(100, $pagination->limit);
        $this->assertEquals(5, $pagination->page);
    }

    public function test_from_array_with_all_parameters()
    {
        $pagination = PaginationOptions::fromArray([
            'limit' => 50,
            'page' => 3
        ]);

        $this->assertEquals(50, $pagination->limit);
        $this->assertEquals(3, $pagination->page);
    }

    public function test_from_array_with_defaults()
    {
        $pagination = PaginationOptions::fromArray([]);

        $this->assertEquals(10, $pagination->limit);
        $this->assertEquals(1, $pagination->page);
    }

    public function test_from_array_with_partial_parameters()
    {
        $pagination = PaginationOptions::fromArray([
            'limit' => 15
        ]);

        $this->assertEquals(15, $pagination->limit);
        $this->assertEquals(1, $pagination->page);
    }

    public function test_from_array_with_only_page()
    {
        $pagination = PaginationOptions::fromArray([
            'page' => 5
        ]);

        $this->assertEquals(10, $pagination->limit);
        $this->assertEquals(5, $pagination->page);
    }

    public function test_from_graphql_args_with_all_parameters()
    {
        $pagination = PaginationOptions::fromGraphQLArgs([
            'first' => 20,
            'page' => 4
        ]);

        $this->assertEquals(20, $pagination->limit);
        $this->assertEquals(4, $pagination->page);
    }

    public function test_from_graphql_args_with_defaults()
    {
        $pagination = PaginationOptions::fromGraphQLArgs([]);

        $this->assertEquals(10, $pagination->limit);
        $this->assertEquals(1, $pagination->page);
    }

    public function test_from_graphql_args_with_partial_parameters()
    {
        $pagination = PaginationOptions::fromGraphQLArgs([
            'first' => 30
        ]);

        $this->assertEquals(30, $pagination->limit);
        $this->assertEquals(1, $pagination->page);
    }

    public function test_from_graphql_args_with_only_page()
    {
        $pagination = PaginationOptions::fromGraphQLArgs([
            'page' => 7
        ]);

        $this->assertEquals(10, $pagination->limit);
        $this->assertEquals(7, $pagination->page);
    }

    public function test_throws_exception_for_limit_less_than_one()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Limit must be at least 1');

        new PaginationOptions(0, 1);
    }

    public function test_throws_exception_for_negative_limit()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Limit must be at least 1');

        new PaginationOptions(-5, 1);
    }

    public function test_throws_exception_for_limit_greater_than_hundred()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Limit must be at most 100');

        new PaginationOptions(101, 1);
    }

    public function test_throws_exception_for_limit_exactly_hundred_plus_one()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Limit must be at most 100');

        new PaginationOptions(101, 1);
    }

    public function test_throws_exception_for_page_less_than_one()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Page must be at least 1');

        new PaginationOptions(10, 0);
    }

    public function test_throws_exception_for_negative_page()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Page must be at least 1');

        new PaginationOptions(10, -3);
    }

    public function test_accepts_valid_limit_boundaries()
    {
        $validLimits = [1, 10, 25, 50, 75, 100];

        foreach ($validLimits as $limit) {
            $pagination = new PaginationOptions($limit, 1);
            $this->assertEquals($limit, $pagination->limit);
        }
    }

    public function test_accepts_valid_page_numbers()
    {
        $validPages = [1, 2, 5, 10, 100, 1000];

        foreach ($validPages as $page) {
            $pagination = new PaginationOptions(10, $page);
            $this->assertEquals($page, $pagination->page);
        }
    }

    public function test_properties_are_readonly()
    {
        $pagination = new PaginationOptions(25, 3);

        $this->assertTrue(property_exists($pagination, 'limit'));
        $this->assertTrue(property_exists($pagination, 'page'));

        // Test that properties are readonly (can't be modified after construction)
        $reflection = new \ReflectionClass($pagination);
        $limitProperty = $reflection->getProperty('limit');
        $pageProperty = $reflection->getProperty('page');

        $this->assertTrue($limitProperty->isReadOnly());
        $this->assertTrue($pageProperty->isReadOnly());
    }

    public function test_handles_string_numeric_values_from_array()
    {
        $pagination = PaginationOptions::fromArray([
            'limit' => '25',
            'page' => '3'
        ]);

        $this->assertEquals(25, $pagination->limit);
        $this->assertEquals(3, $pagination->page);
    }

    public function test_handles_string_numeric_values_from_graphql_args()
    {
        $pagination = PaginationOptions::fromGraphQLArgs([
            'first' => '30',
            'page' => '5'
        ]);

        $this->assertEquals(30, $pagination->limit);
        $this->assertEquals(5, $pagination->page);
    }
}

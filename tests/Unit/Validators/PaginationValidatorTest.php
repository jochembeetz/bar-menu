<?php

declare(strict_types=1);

namespace Tests\Unit\Validators;

use App\Validators\PaginationValidator;
use Tests\TestCase;

final class PaginationValidatorTest extends TestCase
{
    public function test_from_graphql_args_extracts_pagination_correctly(): void
    {
        $args = [
            'first' => 25,
            'page' => 2,
        ];

        $result = PaginationValidator::fromGraphQLArgs($args);

        $this->assertEquals(25, $result['limit']);
        $this->assertEquals(2, $result['page']);
    }

    public function test_from_graphql_args_uses_defaults_when_missing(): void
    {
        $args = [];

        $result = PaginationValidator::fromGraphQLArgs($args);

        $this->assertEquals(10, $result['limit']);
        $this->assertEquals(1, $result['page']);
    }

    public function test_from_graphql_args_extracts_values_as_is(): void
    {
        $args = [
            'first' => 150, // No longer capped
            'page' => 0,    // No longer adjusted
        ];

        $result = PaginationValidator::fromGraphQLArgs($args);

        $this->assertEquals(150, $result['limit']);
        $this->assertEquals(0, $result['page']);
    }

    public function test_normalize_validates_pagination_parameters(): void
    {
        $data = [
            'limit' => 50,
            'page' => 3,
        ];

        $result = PaginationValidator::normalize($data);

        $this->assertEquals(50, $result['limit']);
        $this->assertEquals(3, $result['page']);
    }

    public function test_normalize_uses_defaults_when_missing(): void
    {
        $data = [];

        $result = PaginationValidator::normalize($data);

        $this->assertEquals(10, $result['limit']);
        $this->assertEquals(1, $result['page']);
    }
}

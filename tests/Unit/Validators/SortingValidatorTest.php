<?php

declare(strict_types=1);

namespace Tests\Unit\Validators;

use App\Validators\SortingValidator;
use Tests\TestCase;

final class SortingValidatorTest extends TestCase
{
    public function test_from_graphql_args_extracts_sorting_correctly(): void
    {
        $args = [
            'orderBy' => [
                'column' => 'name',
                'order' => 'desc',
            ],
        ];

        $result = SortingValidator::fromGraphQLArgs($args);

        $this->assertEquals('name', $result['column']);
        $this->assertEquals('desc', $result['order']);
    }

    public function test_from_graphql_args_uses_defaults_when_missing(): void
    {
        $args = [];

        $result = SortingValidator::fromGraphQLArgs($args);

        $this->assertEquals('sort_order', $result['column']);
        $this->assertEquals('asc', $result['order']);
    }

    public function test_rules_with_additional_sort_fields(): void
    {
        $rules = SortingValidator::rules(['price_in_cents']);

        $this->assertStringContainsString('price_in_cents', $rules['sortBy']);
        $this->assertStringContainsString('sort_order', $rules['sortBy']);
        $this->assertStringContainsString('name', $rules['sortBy']);
    }

    public function test_messages_with_additional_sort_fields(): void
    {
        $messages = SortingValidator::messages(['price_in_cents']);

        $this->assertStringContainsString('price_in_cents', $messages['sortBy.in']);
        $this->assertStringContainsString('sort_order', $messages['sortBy.in']);
    }

    public function test_defaults_returns_correct_structure(): void
    {
        $defaults = SortingValidator::defaults();

        $this->assertEquals('sort_order', $defaults['column']);
        $this->assertEquals('asc', $defaults['order']);
    }
}

<?php

namespace Tests\Unit\GraphQL\Types;

use App\GraphQL\Types\IngredientType;
use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IngredientTypeTest extends TestCase
{
    use RefreshDatabase;

    private IngredientType $ingredientType;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ingredientType = new IngredientType;
    }

    public function test_it_returns_pivot_type_for_ingredient_with_pivot(): void
    {
        $product = Product::factory()->withIngredients(1, [], ['type' => 'base'])->create();
        $ingredientWithPivot = $product->ingredients()->first();

        $result = $this->ingredientType->__invoke($ingredientWithPivot);

        $this->assertEquals('base', $result);
    }

    public function test_it_returns_optional_type_for_ingredient_with_pivot(): void
    {
        $product = Product::factory()->withIngredients(1, [], ['type' => 'optional'])->create();
        $ingredientWithPivot = $product->ingredients()->first();

        $result = $this->ingredientType->__invoke($ingredientWithPivot);

        $this->assertEquals('optional', $result);
    }

    public function test_it_returns_add_on_type_for_ingredient_with_pivot(): void
    {
        $product = Product::factory()->withIngredients(1, [], ['type' => 'add-on'])->create();
        $ingredientWithPivot = $product->ingredients()->first();

        $result = $this->ingredientType->__invoke($ingredientWithPivot);

        $this->assertEquals('add-on', $result);
    }

    public function test_it_returns_null_for_ingredient_without_pivot(): void
    {
        $ingredient = Ingredient::factory()->create();

        $result = $this->ingredientType->__invoke($ingredient);

        $this->assertNull($result);
    }

    public function test_it_returns_null_for_non_ingredient_instance(): void
    {
        $product = Product::factory()->create();

        $result = $this->ingredientType->__invoke($product);

        $this->assertNull($result);
    }

    public function test_it_returns_null_for_null_input(): void
    {
        $result = $this->ingredientType->__invoke(null);

        $this->assertNull($result);
    }

    public function test_it_returns_null_for_string_input(): void
    {
        $result = $this->ingredientType->__invoke('not an ingredient');

        $this->assertNull($result);
    }

    public function test_it_returns_null_for_array_input(): void
    {
        $result = $this->ingredientType->__invoke(['id' => 1, 'name' => 'test']);

        $this->assertNull($result);
    }

    public function test_it_handles_multiple_ingredients_with_different_types(): void
    {
        $product = Product::factory()->withSpecificIngredients([
            ['attributes' => [], 'pivot' => ['type' => 'base']],
            ['attributes' => [], 'pivot' => ['type' => 'optional']],
            ['attributes' => [], 'pivot' => ['type' => 'add-on']],
        ])->create();

        $ingredients = $product->ingredients()->get();

        // Check that we have all three types, regardless of order
        $types = $ingredients->map(function ($ingredient) {
            return $this->ingredientType->__invoke($ingredient);
        })->sort()->values();

        $this->assertCount(3, $types);
        $this->assertContains('base', $types);
        $this->assertContains('optional', $types);
        $this->assertContains('add-on', $types);
    }
}

<?php

namespace Tests\Unit\Actions\Categories;

use App\Actions\Categories\GetCategoryProductsArrayAction;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Product;
use App\ValueObjects\CategoryFilters;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetCategoryProductsArrayActionTest extends TestCase
{
    use RefreshDatabase;

    private GetCategoryProductsArrayAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new GetCategoryProductsArrayAction;
    }

    public function test_gets_category_products_as_array_with_sorting()
    {
        $category = Category::factory()->cocktails()->withSpecificProducts([
            ['attributes' => ['name' => 'B Mojito'], 'sort_order' => 2],
            ['attributes' => ['name' => 'A Margarita'], 'sort_order' => 1],
            ['attributes' => ['name' => 'C Martini'], 'sort_order' => 3],
        ])->create();

        $filters = CategoryFilters::sortingOnly([
            'sortBy' => 'sort_order',
            'sortOrder' => 'asc',
        ]);

        $result = ($this->action)($category, $filters);

        $this->assertIsArray($result);
        $this->assertCount(3, $result);

        // Check sorting
        $this->assertEquals('A Margarita', $result[0]->name);
        $this->assertEquals('B Mojito', $result[1]->name);
        $this->assertEquals('C Martini', $result[2]->name);
    }

    public function test_gets_category_products_with_custom_sorting()
    {
        $category = Category::factory()->beers()->withSpecificProducts([
            ['attributes' => ['name' => 'Zebra Guinness'], 'sort_order' => 1],
            ['attributes' => ['name' => 'Alpha Heineken'], 'sort_order' => 2],
        ])->create();

        $filters = CategoryFilters::sortingOnly([
            'sortBy' => 'name',
            'sortOrder' => 'asc',
        ]);

        $result = ($this->action)($category, $filters);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals('Alpha Heineken', $result[0]->name);
        $this->assertEquals('Zebra Guinness', $result[1]->name);
    }

    public function test_gets_category_products_with_descending_sort()
    {
        $category = Category::factory()->wines()->withSpecificProducts([
            ['attributes' => ['name' => 'A Red Wine'], 'sort_order' => 1],
            ['attributes' => ['name' => 'B White Wine'], 'sort_order' => 2],
        ])->create();

        $filters = CategoryFilters::sortingOnly([
            'sortBy' => 'sort_order',
            'sortOrder' => 'desc',
        ]);

        $result = ($this->action)($category, $filters);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals('B White Wine', $result[0]->name);
        $this->assertEquals('A Red Wine', $result[1]->name);
    }

    public function test_only_returns_products_for_specific_category()
    {
        $category1 = Category::factory()->cocktails()->withSpecificProducts([
            ['attributes' => ['name' => 'Mojito']],
        ])->create();

        $category2 = Category::factory()->beers()->withSpecificProducts([
            ['attributes' => ['name' => 'Heineken']],
        ])->create();

        $filters = CategoryFilters::sortingOnly([]);

        $result = ($this->action)($category1, $filters);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('Mojito', $result[0]->name);
    }

    public function test_handles_empty_result()
    {
        $category = Category::factory()->softDrinks()->create();

        $filters = CategoryFilters::sortingOnly([]);

        $result = ($this->action)($category, $filters);

        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }

    public function test_returns_all_products_without_pagination()
    {
        $category = Category::factory()->mocktails()->withProducts(25)->create();

        $filters = CategoryFilters::sortingOnly([]);

        $result = ($this->action)($category, $filters);

        $this->assertIsArray($result);
        $this->assertCount(25, $result);
    }

    public function test_includes_ingredients_relationship()
    {
        $category = Category::factory()->cocktails()->withSpecificProducts([
            ['attributes' => ['name' => 'Mojito']],
        ])->create();

        // Add ingredients to the first product
        $product = $category->products()->first();
        $product->ingredients()->attach(Ingredient::factory()->count(2)->create());

        $filters = CategoryFilters::sortingOnly([]);

        $result = ($this->action)($category, $filters);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertTrue($result[0]->relationLoaded('ingredients'));
    }
}

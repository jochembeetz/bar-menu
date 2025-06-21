<?php

namespace Tests\Unit\Actions\Categories;

use App\Actions\Categories\ListCategoryProductsAction;
use App\Models\Category;
use App\Models\Product;
use App\ValueObjects\CategoryFilters;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListCategoryProductsActionTest extends TestCase
{
    use RefreshDatabase;

    private ListCategoryProductsAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new ListCategoryProductsAction();
    }

    public function test_lists_category_products_with_pagination_and_sorting()
    {
        $category = Category::factory()->cocktails()->withSpecificProducts([
            ['attributes' => ['name' => 'B Mojito'], 'sort_order' => 2],
            ['attributes' => ['name' => 'A Margarita'], 'sort_order' => 1],
            ['attributes' => ['name' => 'C Martini'], 'sort_order' => 3],
        ])->create();

        $filters = CategoryFilters::withPagination([
            'sortBy' => 'sort_order',
            'sortOrder' => 'asc',
            'limit' => 2,
            'page' => 1
        ]);

        $result = ($this->action)($category, $filters);

        $this->assertCount(2, $result->items());
        $this->assertEquals(3, $result->total());
        $this->assertEquals(1, $result->currentPage());
        $this->assertEquals(2, $result->perPage());

        // Check sorting
        $items = $result->items();
        $this->assertEquals('A Margarita', $items[0]->name);
        $this->assertEquals('B Mojito', $items[1]->name);
    }

    public function test_lists_category_products_with_custom_sorting()
    {
        $category = Category::factory()->beers()->withSpecificProducts([
            ['attributes' => ['name' => 'Zebra Guinness'], 'sort_order' => 1],
            ['attributes' => ['name' => 'Alpha Heineken'], 'sort_order' => 2],
        ])->create();

        $filters = CategoryFilters::withPagination([
            'sortBy' => 'name',
            'sortOrder' => 'asc',
            'limit' => 10,
            'page' => 1
        ]);

        $result = ($this->action)($category, $filters);

        $items = $result->items();
        $this->assertEquals('Alpha Heineken', $items[0]->name);
        $this->assertEquals('Zebra Guinness', $items[1]->name);
    }

    public function test_lists_category_products_with_descending_sort()
    {
        $category = Category::factory()->wines()->withSpecificProducts([
            ['attributes' => ['name' => 'A Red Wine'], 'sort_order' => 1],
            ['attributes' => ['name' => 'B White Wine'], 'sort_order' => 2],
        ])->create();

        $filters = CategoryFilters::withPagination([
            'sortBy' => 'sort_order',
            'sortOrder' => 'desc',
            'limit' => 10,
            'page' => 1
        ]);

        $result = ($this->action)($category, $filters);

        $items = $result->items();
        $this->assertEquals('B White Wine', $items[0]->name);
        $this->assertEquals('A Red Wine', $items[1]->name);
    }

    public function test_only_returns_products_for_specific_category()
    {
        $category1 = Category::factory()->cocktails()->withSpecificProducts([
            ['attributes' => ['name' => 'Mojito']],
        ])->create();

        $category2 = Category::factory()->beers()->withSpecificProducts([
            ['attributes' => ['name' => 'Heineken']],
        ])->create();

        $filters = CategoryFilters::withPagination([
            'limit' => 10,
            'page' => 1
        ]);

        $result = ($this->action)($category1, $filters);

        $this->assertCount(1, $result->items());
        $this->assertEquals('Mojito', $result->items()[0]->name);
    }

    public function test_handles_empty_result()
    {
        $category = Category::factory()->softDrinks()->create();

        $filters = CategoryFilters::withPagination([
            'limit' => 10,
            'page' => 1
        ]);

        $result = ($this->action)($category, $filters);

        $this->assertCount(0, $result->items());
        $this->assertEquals(0, $result->total());
    }

    public function test_respects_pagination_parameters()
    {
        $category = Category::factory()->mocktails()->withProducts(5)->create();

        $filters = CategoryFilters::withPagination([
            'limit' => 3,
            'page' => 2
        ]);

        $result = ($this->action)($category, $filters);

        $this->assertCount(2, $result->items()); // 5 total - 3 on first page = 2 on second page
        $this->assertEquals(2, $result->currentPage());
        $this->assertEquals(3, $result->perPage());
    }
}

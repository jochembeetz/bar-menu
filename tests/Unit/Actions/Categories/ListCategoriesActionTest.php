<?php

namespace Tests\Unit\Actions\Categories;

use App\Actions\Categories\ListCategoriesAction;
use App\Models\Category;
use App\ValueObjects\CategoryFilters;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListCategoriesActionTest extends TestCase
{
    use RefreshDatabase;

    private ListCategoriesAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new ListCategoriesAction();
    }

    public function test_lists_categories_with_pagination_and_sorting()
    {
        // Create categories in reverse order
        Category::factory()->create(['name' => 'B Category', 'sort_order' => 2]);
        Category::factory()->create(['name' => 'A Category', 'sort_order' => 1]);
        Category::factory()->create(['name' => 'C Category', 'sort_order' => 3]);

        $filters = CategoryFilters::withPagination([
            'sortBy' => 'sort_order',
            'sortOrder' => 'asc',
            'limit' => 2,
            'page' => 1
        ]);

        $result = ($this->action)($filters);

        $this->assertCount(2, $result->items());
        $this->assertEquals(3, $result->total());
        $this->assertEquals(1, $result->currentPage());
        $this->assertEquals(2, $result->perPage());

        // Check sorting
        $items = $result->items();
        $this->assertEquals('A Category', $items[0]->name);
        $this->assertEquals('B Category', $items[1]->name);
    }

    public function test_lists_categories_with_custom_sorting()
    {
        Category::factory()->create(['name' => 'Zebra', 'sort_order' => 1]);
        Category::factory()->create(['name' => 'Alpha', 'sort_order' => 2]);

        $filters = CategoryFilters::withPagination([
            'sortBy' => 'name',
            'sortOrder' => 'asc',
            'limit' => 10,
            'page' => 1
        ]);

        $result = ($this->action)($filters);

        $items = $result->items();
        $this->assertEquals('Alpha', $items[0]->name);
        $this->assertEquals('Zebra', $items[1]->name);
    }

    public function test_lists_categories_with_descending_sort()
    {
        Category::factory()->create(['name' => 'A Category', 'sort_order' => 1]);
        Category::factory()->create(['name' => 'B Category', 'sort_order' => 2]);

        $filters = CategoryFilters::withPagination([
            'sortBy' => 'sort_order',
            'sortOrder' => 'desc',
            'limit' => 10,
            'page' => 1
        ]);

        $result = ($this->action)($filters);

        $items = $result->items();
        $this->assertEquals('B Category', $items[0]->name);
        $this->assertEquals('A Category', $items[1]->name);
    }

    public function test_handles_empty_result()
    {
        $filters = CategoryFilters::withPagination([
            'limit' => 10,
            'page' => 1
        ]);

        $result = ($this->action)($filters);

        $this->assertCount(0, $result->items());
        $this->assertEquals(0, $result->total());
    }

    public function test_respects_pagination_parameters()
    {
        Category::factory()->count(5)->create();

        $filters = CategoryFilters::withPagination([
            'limit' => 3,
            'page' => 2
        ]);

        $result = ($this->action)($filters);

        $this->assertCount(2, $result->items()); // 5 total - 3 on first page = 2 on second page
        $this->assertEquals(2, $result->currentPage());
        $this->assertEquals(3, $result->perPage());
    }
}

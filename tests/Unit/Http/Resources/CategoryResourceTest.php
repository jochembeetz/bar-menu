<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class CategoryResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_transforms_category_to_array(): void
    {
        $category = Category::factory()->create([
            'name' => 'Cocktails',
            'slug' => 'cocktails',
            'description' => 'Alcoholic mixed drinks',
            'sort_order' => 10,
        ]);

        $resource = new CategoryResource($category);
        $request = Request::create('/api/categories');

        $result = $resource->toArray($request);

        $this->assertEquals([
            'id' => $category->id,
            'name' => 'Cocktails',
            'slug' => 'cocktails',
            'description' => 'Alcoholic mixed drinks',
        ], $result);
    }

    public function test_it_handles_null_description(): void
    {
        $category = Category::factory()->create([
            'name' => 'Beers',
            'slug' => 'beers',
            'description' => null,
            'sort_order' => 20,
        ]);

        $resource = new CategoryResource($category);
        $request = Request::create('/api/categories');

        $result = $resource->toArray($request);

        $this->assertEquals([
            'id' => $category->id,
            'name' => 'Beers',
            'slug' => 'beers',
            'description' => null,
        ], $result);
    }

    public function test_it_handles_empty_description(): void
    {
        $category = Category::factory()->create([
            'name' => 'Wines',
            'slug' => 'wines',
            'description' => '',
            'sort_order' => 30,
        ]);

        $resource = new CategoryResource($category);
        $request = Request::create('/api/categories');

        $result = $resource->toArray($request);

        $this->assertEquals([
            'id' => $category->id,
            'name' => 'Wines',
            'slug' => 'wines',
            'description' => '',
        ], $result);
    }

    public function test_it_handles_zero_sort_order(): void
    {
        $category = Category::factory()->create([
            'name' => 'Soft Drinks',
            'slug' => 'soft-drinks',
            'description' => 'Non-alcoholic beverages',
            'sort_order' => 0,
        ]);

        $resource = new CategoryResource($category);
        $request = Request::create('/api/categories');

        $result = $resource->toArray($request);

        $this->assertEquals([
            'id' => $category->id,
            'name' => 'Soft Drinks',
            'slug' => 'soft-drinks',
            'description' => 'Non-alcoholic beverages',
        ], $result);
    }

    public function test_it_handles_large_sort_order(): void
    {
        $category = Category::factory()->create([
            'name' => 'Mocktails',
            'slug' => 'mocktails',
            'description' => 'Non-alcoholic mixed drinks',
            'sort_order' => 999999,
        ]);

        $resource = new CategoryResource($category);
        $request = Request::create('/api/categories');

        $result = $resource->toArray($request);

        $this->assertEquals([
            'id' => $category->id,
            'name' => 'Mocktails',
            'slug' => 'mocktails',
            'description' => 'Non-alcoholic mixed drinks',
        ], $result);
    }

    public function test_it_handles_special_characters_in_name(): void
    {
        $category = Category::factory()->create([
            'name' => 'Café & Coffee',
            'slug' => 'cafe-coffee',
            'description' => 'Hot beverages',
            'sort_order' => 40,
        ]);

        $resource = new CategoryResource($category);
        $request = Request::create('/api/categories');

        $result = $resource->toArray($request);

        $this->assertEquals([
            'id' => $category->id,
            'name' => 'Café & Coffee',
            'slug' => 'cafe-coffee',
            'description' => 'Hot beverages',
        ], $result);
    }

    public function test_it_handles_long_description(): void
    {
        $longDescription = str_repeat('This is a very long description. ', 50);
        $category = Category::factory()->create([
            'name' => 'Spirits',
            'slug' => 'spirits',
            'description' => $longDescription,
            'sort_order' => 50,
        ]);

        $resource = new CategoryResource($category);
        $request = Request::create('/api/categories');

        $result = $resource->toArray($request);

        $this->assertEquals([
            'id' => $category->id,
            'name' => 'Spirits',
            'slug' => 'spirits',
            'description' => $longDescription,
        ], $result);
    }

    public function test_it_returns_correct_data_types(): void
    {
        $category = Category::factory()->create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'Test description',
            'sort_order' => 100,
        ]);

        $resource = new CategoryResource($category);
        $request = Request::create('/api/categories');

        $result = $resource->toArray($request);

        $this->assertIsInt($result['id']);
        $this->assertIsString($result['name']);
        $this->assertIsString($result['slug']);
        $this->assertIsString($result['description']);
    }

    public function test_it_works_with_collection(): void
    {
        $categories = Category::factory()->count(3)->create();

        $collection = CategoryResource::collection($categories);
        $request = Request::create('/api/categories');

        $result = $collection->toArray($request);

        $this->assertCount(3, $result);
        $this->assertArrayHasKey('id', $result[0]);
        $this->assertArrayHasKey('name', $result[0]);
        $this->assertArrayHasKey('slug', $result[0]);
        $this->assertArrayHasKey('description', $result[0]);
    }
}

<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Ingredient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListCategoryProductsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_products_in_category_with_default_pagination(): void
    {
        $category = Category::factory()->withProducts(15)->create();

        $response = $this->getJson("api/v1/categories/{$category->id}/products");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'description',
                        'price_in_cents',
                        'ingredients' => [
                            '*' => [
                                'id',
                                'name',
                                'slug',
                                'description',
                                'type',
                            ],
                        ],
                    ],
                ],
                'links',
                'meta',
            ])
            ->assertJsonCount(10, 'data'); // Default limit is 10
    }

    public function test_it_returns_products_sorted_by_sort_order_asc(): void
    {
        $category = Category::factory()->withSpecificProducts([
            ['attributes' => [], 'sort_order' => 3],
            ['attributes' => [], 'sort_order' => 1],
            ['attributes' => [], 'sort_order' => 2],
        ])->create();

        $response = $this->getJson("api/v1/categories/{$category->id}/products?sortBy=sort_order&sortOrder=asc");

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(3, $data);

        // Get the sort_order values from the pivot table
        $sortOrders = [];
        foreach ($data as $product) {
            $pivot = $category->products()->where('product_id', $product['id'])->first()->pivot;
            $sortOrders[] = $pivot->sort_order;
        }

        // Verify they're sorted by sort_order asc
        $this->assertEquals([1, 2, 3], $sortOrders);
    }

    public function test_it_returns_products_sorted_by_sort_order_desc(): void
    {
        $category = Category::factory()->withSpecificProducts([
            ['attributes' => [], 'sort_order' => 3],
            ['attributes' => [], 'sort_order' => 1],
            ['attributes' => [], 'sort_order' => 2],
        ])->create();

        $response = $this->getJson("api/v1/categories/{$category->id}/products?sortBy=sort_order&sortOrder=desc");

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(3, $data);

        // Get the sort_order values from the pivot table
        $sortOrders = [];
        foreach ($data as $product) {
            $pivot = $category->products()->where('product_id', $product['id'])->first()->pivot;
            $sortOrders[] = $pivot->sort_order;
        }

        // Verify they're sorted by sort_order desc
        $this->assertEquals([3, 2, 1], $sortOrders);
    }

    public function test_it_returns_products_sorted_by_name_asc(): void
    {
        $category = Category::factory()->withSpecificProducts([
            ['attributes' => ['name' => 'Cocktail'], 'sort_order' => 3],
            ['attributes' => ['name' => 'Ale'], 'sort_order' => 1],
            ['attributes' => ['name' => 'Beer'], 'sort_order' => 2],
        ])->create();

        $response = $this->getJson("api/v1/categories/{$category->id}/products?sortBy=name&sortOrder=asc");

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(3, $data);
        $this->assertEquals('Ale', $data[0]['name']);
        $this->assertEquals('Beer', $data[1]['name']);
        $this->assertEquals('Cocktail', $data[2]['name']);
    }

    public function test_it_returns_products_sorted_by_price_in_cents_desc(): void
    {
        $category = Category::factory()->withSpecificProducts([
            ['attributes' => ['price_in_cents' => 500], 'sort_order' => 1],
            ['attributes' => ['price_in_cents' => 2000], 'sort_order' => 3],
            ['attributes' => ['price_in_cents' => 1000], 'sort_order' => 2],
        ])->create();

        $response = $this->getJson("api/v1/categories/{$category->id}/products?sortBy=price_in_cents&sortOrder=desc");

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(3, $data);
        $this->assertEquals(2000, $data[0]['price_in_cents']);
        $this->assertEquals(1000, $data[1]['price_in_cents']);
        $this->assertEquals(500, $data[2]['price_in_cents']);
    }

    public function test_it_respects_limit_parameter(): void
    {
        $category = Category::factory()->withProducts(15)->create();

        $response = $this->getJson("api/v1/categories/{$category->id}/products?limit=5");

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    public function test_it_respects_page_parameter(): void
    {
        $category = Category::factory()->withProducts(15)->create();

        $response = $this->getJson("api/v1/categories/{$category->id}/products?limit=5&page=2");

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');

        $meta = $response->json('meta');
        $this->assertEquals(2, $meta['current_page']);
    }

    public function test_it_returns_empty_array_when_category_has_no_products(): void
    {
        $category = Category::factory()->create();

        $response = $this->getJson("api/v1/categories/{$category->id}/products");

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    public function test_it_returns_404_when_category_not_found(): void
    {
        $response = $this->getJson('api/v1/categories/999/products');

        $response->assertStatus(404);
    }

    public function test_it_validates_sort_by_parameter(): void
    {
        $category = Category::factory()->create();

        $response = $this->getJson("api/v1/categories/{$category->id}/products?sortBy=invalid_field");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['sortBy']);
    }

    public function test_it_validates_sort_order_parameter(): void
    {
        $category = Category::factory()->create();

        $response = $this->getJson("api/v1/categories/{$category->id}/products?sortOrder=invalid_order");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['sortOrder']);
    }

    public function test_it_validates_limit_parameter_min_value(): void
    {
        $category = Category::factory()->create();

        $response = $this->getJson("api/v1/categories/{$category->id}/products?limit=0");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['limit']);
    }

    public function test_it_validates_limit_parameter_max_value(): void
    {
        $category = Category::factory()->create();

        $response = $this->getJson("api/v1/categories/{$category->id}/products?limit=101");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['limit']);
    }

    public function test_it_validates_page_parameter_min_value(): void
    {
        $category = Category::factory()->create();

        $response = $this->getJson("api/v1/categories/{$category->id}/products?page=0");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['page']);
    }

    public function test_it_validates_limit_parameter_must_be_integer(): void
    {
        $category = Category::factory()->create();

        $response = $this->getJson("api/v1/categories/{$category->id}/products?limit=abc");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['limit']);
    }

    public function test_it_validates_page_parameter_must_be_integer(): void
    {
        $category = Category::factory()->create();

        $response = $this->getJson("api/v1/categories/{$category->id}/products?page=abc");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['page']);
    }

    public function test_it_uses_default_sort_order_when_sort_by_not_provided(): void
    {
        $category = Category::factory()->withSpecificProducts([
            ['attributes' => [], 'sort_order' => 3],
            ['attributes' => [], 'sort_order' => 1],
            ['attributes' => [], 'sort_order' => 2],
        ])->create();

        $response = $this->getJson("api/v1/categories/{$category->id}/products");

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(3, $data);

        // Get the sort_order values from the pivot table
        $sortOrders = [];
        foreach ($data as $product) {
            $pivot = $category->products()->where('product_id', $product['id'])->first()->pivot;
            $sortOrders[] = $pivot->sort_order;
        }

        // Verify they're sorted by sort_order asc (default)
        $this->assertEquals([1, 2, 3], $sortOrders);
    }

    public function test_it_uses_default_asc_order_when_sort_order_not_provided(): void
    {
        $category = Category::factory()->withSpecificProducts([
            ['attributes' => [], 'sort_order' => 3],
            ['attributes' => [], 'sort_order' => 1],
            ['attributes' => [], 'sort_order' => 2],
        ])->create();

        $response = $this->getJson("api/v1/categories/{$category->id}/products?sortBy=sort_order");

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(3, $data);

        // Get the sort_order values from the pivot table
        $sortOrders = [];
        foreach ($data as $product) {
            $pivot = $category->products()->where('product_id', $product['id'])->first()->pivot;
            $sortOrders[] = $pivot->sort_order;
        }

        // Verify they're sorted by sort_order asc (default)
        $this->assertEquals([1, 2, 3], $sortOrders);
    }

    public function test_it_returns_correct_pagination_metadata(): void
    {
        $category = Category::factory()->withProducts(25)->create();

        $response = $this->getJson("api/v1/categories/{$category->id}/products?limit=10&page=2");

        $response->assertStatus(200);

        $meta = $response->json('meta');
        $this->assertEquals(25, $meta['total']);
        $this->assertEquals(10, $meta['per_page']);
        $this->assertEquals(2, $meta['current_page']);
        $this->assertEquals(3, $meta['last_page']);
    }

    public function test_it_includes_ingredients_in_response(): void
    {
        $category = Category::factory()->withSpecificProducts([
            ['attributes' => [], 'sort_order' => 1],
        ])->create();

        // Get the first product and add ingredients to it
        $product = $category->products()->first();
        $product->ingredients()->attach([
            Ingredient::factory()->create()->id => ['type' => 'base'],
            Ingredient::factory()->create()->id => ['type' => 'optional'],
        ]);

        $response = $this->getJson("api/v1/categories/{$category->id}/products");

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(2, $data[0]['ingredients']);

        $ingredients = $data[0]['ingredients'];
        $this->assertEquals('base', $ingredients[0]['type']);
        $this->assertEquals('optional', $ingredients[1]['type']);
    }

    public function test_it_only_returns_products_from_specified_category(): void
    {
        $category1 = Category::factory()->withProducts(2)->create();
        $category2 = Category::factory()->withProducts(1)->create();

        $response = $this->getJson("api/v1/categories/{$category1->id}/products");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_it_handles_products_with_no_ingredients(): void
    {
        $category = Category::factory()->withSpecificProducts([
            ['attributes' => [], 'sort_order' => 1],
        ])->create();

        $response = $this->getJson("api/v1/categories/{$category->id}/products");

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertEmpty($data[0]['ingredients']);
    }

    public function test_it_sorts_by_slug_desc(): void
    {
        $category = Category::factory()->withSpecificProducts([
            ['attributes' => ['slug' => 'apple-drink'], 'sort_order' => 1],
            ['attributes' => ['slug' => 'banana-drink'], 'sort_order' => 2],
            ['attributes' => ['slug' => 'cherry-drink'], 'sort_order' => 3],
        ])->create();

        $response = $this->getJson("api/v1/categories/{$category->id}/products?sortBy=slug&sortOrder=desc");

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(3, $data);
        $this->assertEquals('cherry-drink', $data[0]['slug']);
        $this->assertEquals('banana-drink', $data[1]['slug']);
        $this->assertEquals('apple-drink', $data[2]['slug']);
    }
}

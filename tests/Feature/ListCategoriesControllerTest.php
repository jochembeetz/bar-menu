<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListCategoriesControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_categories_with_default_pagination(): void
    {
        Category::factory()->count(15)->create();

        $response = $this->getJson('api/v1/categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'description',
                    ],
                ],
                'links',
                'meta',
            ])
            ->assertJsonCount(10, 'data'); // Default limit is 10
    }

    public function test_it_returns_categories_sorted_by_sort_order_asc(): void
    {
        $category3 = Category::factory()->create(['sort_order' => 3]);
        $category1 = Category::factory()->create(['sort_order' => 1]);
        $category2 = Category::factory()->create(['sort_order' => 2]);

        $response = $this->getJson('api/v1/categories?sortBy=sort_order&sortOrder=asc');

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertEquals($category1->id, $data[0]['id']);
        $this->assertEquals($category2->id, $data[1]['id']);
        $this->assertEquals($category3->id, $data[2]['id']);
    }

    public function test_it_returns_categories_sorted_by_sort_order_desc(): void
    {
        $category3 = Category::factory()->create(['sort_order' => 3]);
        $category1 = Category::factory()->create(['sort_order' => 1]);
        $category2 = Category::factory()->create(['sort_order' => 2]);

        $response = $this->getJson('api/v1/categories?sortBy=sort_order&sortOrder=desc');

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertEquals($category3->id, $data[0]['id']);
        $this->assertEquals($category2->id, $data[1]['id']);
        $this->assertEquals($category1->id, $data[2]['id']);
    }

    public function test_it_respects_limit_parameter(): void
    {
        Category::factory()->count(15)->create();

        $response = $this->getJson('api/v1/categories?limit=5');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    public function test_it_respects_page_parameter(): void
    {
        Category::factory()->count(15)->create();

        $response = $this->getJson('api/v1/categories?limit=5&page=2');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');

        $meta = $response->json('meta');
        $this->assertEquals(2, $meta['current_page']);
    }

    public function test_it_returns_empty_array_when_no_categories(): void
    {
        $response = $this->getJson('api/v1/categories');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    public function test_it_validates_sort_by_parameter(): void
    {
        $response = $this->getJson('api/v1/categories?sortBy=invalid_field');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['sortBy']);
    }

    public function test_it_validates_sort_order_parameter(): void
    {
        $response = $this->getJson('api/v1/categories?sortOrder=invalid_order');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['sortOrder']);
    }

    public function test_it_validates_limit_parameter_min_value(): void
    {
        $response = $this->getJson('api/v1/categories?limit=0');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['limit']);
    }

    public function test_it_validates_limit_parameter_max_value(): void
    {
        $response = $this->getJson('api/v1/categories?limit=101');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['limit']);
    }

    public function test_it_validates_page_parameter_min_value(): void
    {
        $response = $this->getJson('api/v1/categories?page=0');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['page']);
    }

    public function test_it_validates_limit_parameter_must_be_integer(): void
    {
        $response = $this->getJson('api/v1/categories?limit=abc');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['limit']);
    }

    public function test_it_validates_page_parameter_must_be_integer(): void
    {
        $response = $this->getJson('api/v1/categories?page=abc');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['page']);
    }

    public function test_it_uses_default_sort_order_when_sort_by_not_provided(): void
    {
        $category3 = Category::factory()->create(['sort_order' => 3]);
        $category1 = Category::factory()->create(['sort_order' => 1]);
        $category2 = Category::factory()->create(['sort_order' => 2]);

        $response = $this->getJson('api/v1/categories');

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertEquals($category1->id, $data[0]['id']);
        $this->assertEquals($category2->id, $data[1]['id']);
        $this->assertEquals($category3->id, $data[2]['id']);
    }

    public function test_it_uses_default_asc_order_when_sort_order_not_provided(): void
    {
        $category3 = Category::factory()->create(['sort_order' => 3]);
        $category1 = Category::factory()->create(['sort_order' => 1]);
        $category2 = Category::factory()->create(['sort_order' => 2]);

        $response = $this->getJson('api/v1/categories?sortBy=sort_order');

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertEquals($category1->id, $data[0]['id']);
        $this->assertEquals($category2->id, $data[1]['id']);
        $this->assertEquals($category3->id, $data[2]['id']);
    }

    public function test_it_returns_correct_pagination_metadata(): void
    {
        Category::factory()->count(25)->create();

        $response = $this->getJson('api/v1/categories?limit=10&page=2');

        $response->assertStatus(200);

        $meta = $response->json('meta');
        $this->assertEquals(25, $meta['total']);
        $this->assertEquals(10, $meta['per_page']);
        $this->assertEquals(2, $meta['current_page']);
        $this->assertEquals(3, $meta['last_page']);
    }
}

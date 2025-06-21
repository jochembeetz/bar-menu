<?php

namespace Tests\Feature\GraphQL;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Nuwave\Lighthouse\Testing\RefreshesSchemaCache;
use Tests\TestCase;

class ListCategoriesQueryTest extends TestCase
{
    use RefreshDatabase;
    use MakesGraphQLRequests;
    use RefreshesSchemaCache;

    public function test_it_returns_categories_with_default_pagination(): void
    {
        Category::factory()->count(15)->create();

        $response = $this->graphQL(/** @lang GraphQL */ '
            query {
                categories {
                    data {
                        id
                        name
                        slug
                        description
                    }
                    paginatorInfo {
                        count
                        currentPage
                        firstItem
                        hasMorePages
                        lastItem
                        lastPage
                        perPage
                        total
                    }
                }
            }
        ');

        $response->assertGraphQLErrorFree()
            ->assertJsonStructure([
                'data' => [
                    'categories' => [
                        'data' => [
                            '*' => [
                                'id',
                                'name',
                                'slug',
                                'description',
                            ],
                        ],
                        'paginatorInfo' => [
                            'count',
                            'currentPage',
                            'firstItem',
                            'hasMorePages',
                            'lastItem',
                            'lastPage',
                            'perPage',
                            'total',
                        ],
                    ],
                ],
            ])
            ->assertJsonCount(10, 'data.categories.data'); // Default limit is 10
    }

    public function test_it_returns_categories_sorted_by_sort_order_asc(): void
    {
        $category3 = Category::factory()->create(['sort_order' => 3]);
        $category1 = Category::factory()->create(['sort_order' => 1]);
        $category2 = Category::factory()->create(['sort_order' => 2]);

        $response = $this->graphQL(/** @lang GraphQL */ '
            query {
                categories(orderBy: { column: "sort_order", order: ASC }) {
                    data {
                        id
                        name
                        slug
                    }
                }
            }
        ');

        $response->assertGraphQLErrorFree();

        $data = $response->json('data.categories.data');
        $this->assertEquals($category1->id, $data[0]['id']);
        $this->assertEquals($category2->id, $data[1]['id']);
        $this->assertEquals($category3->id, $data[2]['id']);
    }

    public function test_it_returns_categories_sorted_by_sort_order_desc(): void
    {
        $category1 = Category::factory()->create(['sort_order' => 3]);
        $category2 = Category::factory()->create(['sort_order' => 5]);
        $category3 = Category::factory()->create(['sort_order' => 4]);

        $response = $this->graphQL(/** @lang GraphQL */ '
            query {
                categories(orderBy: { column: "sort_order", order: ASC }) {
                    data {
                        id
                        name
                        slug
                    }
                }
            }
        ');

        $response->assertGraphQLErrorFree();

        $data = $response->json('data.categories.data');
        $this->assertEquals($category1->id, $data[0]['id']);
        $this->assertEquals($category3->id, $data[1]['id']);
        $this->assertEquals($category2->id, $data[2]['id']);
    }

    public function test_it_respects_first_parameter(): void
    {
        Category::factory()->count(15)->create();

        $response = $this->graphQL(/** @lang GraphQL */ '
            query {
                categories(first: 5) {
                    data {
                        id
                        name
                        slug
                    }
                    paginatorInfo {
                        count
                        total
                    }
                }
            }
        ');

        $response->assertGraphQLErrorFree()
            ->assertJsonCount(5, 'data.categories.data')
            ->assertJson([
                'data' => [
                    'categories' => [
                        'paginatorInfo' => [
                            'count' => 5,
                            'total' => 15,
                        ],
                    ],
                ],
            ]);
    }

    public function test_it_respects_page_parameter(): void
    {
        Category::factory()->count(15)->create();

        $response = $this->graphQL(/** @lang GraphQL */ '
            query {
                categories(first: 5, page: 2) {
                    data {
                        id
                        name
                        slug
                    }
                    paginatorInfo {
                        currentPage
                        total
                    }
                }
            }
        ');

        $response->assertGraphQLErrorFree()
            ->assertJsonCount(5, 'data.categories.data')
            ->assertJson([
                'data' => [
                    'categories' => [
                        'paginatorInfo' => [
                            'currentPage' => 2,
                            'total' => 15,
                        ],
                    ],
                ],
            ]);
    }

    public function test_it_returns_empty_array_when_no_categories(): void
    {
        $response = $this->graphQL(/** @lang GraphQL */ '
            query {
                categories {
                    data {
                        id
                        name
                        slug
                    }
                }
            }
        ');

        $response->assertGraphQLErrorFree()
            ->assertJsonCount(0, 'data.categories.data');
    }

    public function test_it_validates_first_parameter_max_value(): void
    {
        $response = $this->graphQL(/** @lang GraphQL */ '
            query {
                categories(first: 101) {
                    data {
                        id
                        name
                        slug
                    }
                }
            }
        ');

        $response->assertGraphQLErrorMessage('The first field must be less than 100.');
    }

    public function test_it_uses_default_sort_order_when_not_provided(): void
    {
        $category3 = Category::factory()->create(['sort_order' => 3]);
        $category1 = Category::factory()->create(['sort_order' => 1]);
        $category2 = Category::factory()->create(['sort_order' => 2]);

        $response = $this->graphQL(/** @lang GraphQL */ '
            query {
                categories {
                    data {
                        id
                        name
                        slug
                    }
                }
            }
        ');

        $response->assertGraphQLErrorFree();

        $data = $response->json('data.categories.data');
        $this->assertEquals($category1->id, $data[0]['id']);
        $this->assertEquals($category2->id, $data[1]['id']);
        $this->assertEquals($category3->id, $data[2]['id']);
    }

    public function test_it_returns_correct_pagination_metadata(): void
    {
        Category::factory()->count(25)->create();

        $response = $this->graphQL(/** @lang GraphQL */ '
            query {
                categories(first: 10, page: 2) {
                    data {
                        id
                        name
                        slug
                    }
                    paginatorInfo {
                        count
                        currentPage
                        firstItem
                        hasMorePages
                        lastItem
                        lastPage
                        perPage
                        total
                    }
                }
            }
        ');

        $response->assertGraphQLErrorFree();

        $paginatorInfo = $response->json('data.categories.paginatorInfo');
        $this->assertEquals(25, $paginatorInfo['total']);
        $this->assertEquals(10, $paginatorInfo['perPage']);
        $this->assertEquals(2, $paginatorInfo['currentPage']);
        $this->assertEquals(3, $paginatorInfo['lastPage']);
        $this->assertTrue($paginatorInfo['hasMorePages']);
    }
}

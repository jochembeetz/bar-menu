<?php

namespace Tests\Feature\Consistency;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Nuwave\Lighthouse\Testing\RefreshesSchemaCache;
use Tests\TestCase;

class ListCategoriesPaginationTest extends TestCase
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

        $response->assertGraphQLErrorFree();
    }

    public function test_it_returns_the_same_default_pagination_size_and_limit_for_graphql_and_api(): void
    {
        Category::factory()->count(15)->create();

        $graphqlResponse = $this->graphQL(/** @lang GraphQL */ '
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

        $apiResponse = $this->getJson('api/v1/categories');


        $this->assertEquals($graphqlResponse->json('data.categories.paginatorInfo.perPage'), $apiResponse->json('meta.per_page'));
        $this->assertEquals($graphqlResponse->json('data.categories.paginatorInfo.total'), $apiResponse->json('meta.total'));
        $this->assertEquals($graphqlResponse->json('data.categories.paginatorInfo.currentPage'), $apiResponse->json('meta.current_page'));
    }

    public function test_it_validates_the_same_max_pagination_size_for_graphql_and_api(): void
    {
      $graphqlResponse = $this->graphQL(/** @lang GraphQL */ '
            query {
                categories(first: 101) {
                    data {
                        id
                        name
                        slug
                        description
                    }
                }
            }
        ');

        $apiResponse = $this->getJson('api/v1/categories?limit=101');
        // dd($graphqlResponse->json());
        $this->assertEquals($apiResponse->assertStatus(422)->json('errors.limit.0'), 'The limit field must be less than 100.');
        $this->assertEquals($graphqlResponse->json('errors.0.message'), 'The first field must be less than 100.');
    }
}
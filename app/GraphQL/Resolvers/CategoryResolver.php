<?php

namespace App\GraphQL\Resolvers;

use App\GraphQL\Resources\PaginatedResponse;
use App\Models\Category;
use App\Services\CategoryService;
use App\ValueObjects\CategoryFilters;

class CategoryResolver extends BaseResolver
{
    public function __construct(
        private readonly CategoryService $categoryService
    ) {}

    /**
     * Resolve categories query for GraphQL.
     */
    public function categories($root, array $args): array
    {
        $this->validatePaginationArgs($args);

        $filters = CategoryFilters::fromGraphQLArgs($args);

        $paginator = $this->categoryService->getPaginatedCategories($filters);

        return PaginatedResponse::fromPaginator($paginator);
    }

    /**
     * Resolve products relationship for a category (paginated).
     */
    public function products($root, array $args): array
    {
        $this->validatePaginationArgs($args);

        $filters = CategoryFilters::fromGraphQLArgs($args);

        $paginator = $this->categoryService->getCategoryProducts($root, $filters);

        return PaginatedResponse::fromPaginator($paginator);
    }

    /**
     * Resolve products relationship for a category (simple array).
     */
    public function categoryProducts($root, array $args): array
    {
        $filters = CategoryFilters::fromGraphQLArgsSortingOnly($args);

        return $this->categoryService->getCategoryProductsArray($root, $filters);
    }
}

<?php

namespace App\GraphQL\Resolvers;

use App\Models\Category;

class CategoryResolver extends BaseResolver
{
    /**
     * Resolve categories query for GraphQL.
     */
    public function categories($root, array $args): array
    {
        $query = Category::query();

        return $this->applyPaginationAndSorting($query, $args);
    }

    /**
     * Resolve products relationship for a category.
     */
    public function products($root, array $args): array
    {
        $productsQuery = $root->products()->with('ingredients');

        return $this->applyPaginationAndSortingToRelationship($productsQuery, $args);
    }

    public function categoryProducts($root, array $args): array
    {
        $productsQuery = $root->products()->with('ingredients');
        return $this->applySortingToRelationship($productsQuery, $args);
    }
}

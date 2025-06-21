<?php

namespace App\GraphQL\Resolvers;

use App\Models\Product;

class ProductResolver extends BaseResolver
{
    /**
     * Resolve products query for GraphQL.
     */
    public function products($root, array $args): array
    {
        $query = Product::query();

        return $this->applyPaginationAndSorting($query, $args);
    }
}

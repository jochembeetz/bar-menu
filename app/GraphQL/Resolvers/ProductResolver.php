<?php

declare(strict_types=1);

namespace App\GraphQL\Resolvers;

use App\Models\Product;

final class ProductResolver extends BaseResolver
{
    /**
     * Resolve products query for GraphQL.
     */
    public function products($root, array $args): array
    {
        $query = Product::query()->with(['categories', 'ingredients']);

        return $this->applyPaginationAndSorting($query, $args, [
            'price_in_cents',
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\GraphQL\Resolvers;

use App\Models\Ingredient;

final class IngredientResolver extends BaseResolver
{
    /**
     * Resolve ingredients query for GraphQL.
     */
    public function ingredients($root, array $args): array
    {
        $query = Ingredient::query()->with(['products']);

        return $this->applyPaginationAndSorting($query, $args);
    }
}

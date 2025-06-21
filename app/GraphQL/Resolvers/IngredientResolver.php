<?php

namespace App\GraphQL\Resolvers;

use App\Models\Ingredient;

class IngredientResolver extends BaseResolver
{
    /**
     * Resolve ingredients query for GraphQL.
     */
    public function ingredients($root, array $args): array
    {
        $query = Ingredient::query();

        return $this->applyPaginationAndSorting($query, $args);
    }
}

<?php

namespace App\GraphQL\Types;

use App\Models\Ingredient;

class IngredientType
{
    /**
     * Resolve the ingredients relationship for a product with pivot data.
     *
     * @param Product $rootValue
     * @param array $args
     * @return Collection
     */
    public function __invoke($rootValue)
    {
        if (!$rootValue instanceof Ingredient) {
            return null;
        }

        return $rootValue->pivot?->type;
    }
}

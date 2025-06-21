<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Ingredient;
use App\Models\Product;

final class IngredientType
{
    public function __invoke(Product|Ingredient $rootValue): ?string
    {
        if (! $rootValue instanceof Ingredient) {
            return null;
        }

        if (! $rootValue->relationLoaded('pivot')) {
            return null;
        }

        /** @var \App\Models\IngredientProduct|null $pivot */
        $pivot = $rootValue->pivot;

        return $pivot?->type;
    }
}

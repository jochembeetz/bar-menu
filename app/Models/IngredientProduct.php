<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

final class IngredientProduct extends Pivot
{
    protected $table = 'ingredient_product';

    protected $fillable = [
        'product_id',
        'ingredient_id',
        'type',
    ];

    protected $casts = [
        'type' => 'string',
    ];
}

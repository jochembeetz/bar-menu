<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class IngredientProduct extends Pivot
{
    protected $table = 'ingredient_product';

    protected $fillable = [
        'product_id',
        'ingredient_id',
        'type'
    ];

    protected $casts = [
        'type' => 'string',
    ];
}

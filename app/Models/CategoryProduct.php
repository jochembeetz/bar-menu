<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

final class CategoryProduct extends Pivot
{
    protected $table = 'category_product';

    protected $fillable = [
        'category_id',
        'product_id',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];
}

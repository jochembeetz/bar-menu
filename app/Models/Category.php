<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @model
 *
 * @property \App\Models\CategoryProduct|null $pivot
 */
final class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'sort_order',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->using(CategoryProduct::class)
            ->withPivot('sort_order')
            ->withTimestamps();
    }
}

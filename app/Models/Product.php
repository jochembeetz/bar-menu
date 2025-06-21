<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @model
 *
 * @property \App\Models\CategoryProduct|\App\Models\IngredientProduct|null $pivot
 */
final class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_in_cents',
        'sort_order',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)
            ->using(CategoryProduct::class)
            ->withPivot('sort_order')
            ->withTimestamps();
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class)
            ->using(IngredientProduct::class)
            ->withPivot('type')
            ->withTimestamps();
    }
}

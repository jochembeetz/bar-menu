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
 * @property \App\Models\IngredientProduct|null $pivot
 */
final class Ingredient extends Model
{
    /** @use HasFactory<\Database\Factories\IngredientFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->using(IngredientProduct::class)
            ->withPivot('type')
            ->withTimestamps();
    }
}

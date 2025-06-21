<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Ingredient
 */
final class IngredientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $type = null;
        if ($this->resource->relationLoaded('pivot')) {
            /** @var \App\Models\IngredientProduct|null $pivot */
            $pivot = $this->resource->pivot;
            $type = $pivot?->type;
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'type' => $type,
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price_in_cents' => $this->price_in_cents,
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'ingredients' => IngredientResource::collection($this->whenLoaded('ingredients')),
        ];
    }
}

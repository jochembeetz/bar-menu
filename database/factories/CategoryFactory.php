<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->word();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'sort_order' => fake()->numberBetween(1),
        ];
    }

    /**
     * Create a category with products attached
     */
    public function withProducts(int $count = 1, array $productAttributes = []): static
    {
        return $this->afterCreating(function (Category $category) use ($count, $productAttributes) {
            $products = Product::factory()->count($count)->create($productAttributes);

            foreach ($products as $index => $product) {
                $category->products()->attach($product->id, [
                    'sort_order' => $productAttributes['sort_order'] ?? ($index + 1),
                ]);
            }
        });
    }

    /**
     * Create a category with specific products
     */
    public function withSpecificProducts(array $products): static
    {
        return $this->afterCreating(function (Category $category) use ($products) {
            foreach ($products as $productData) {
                $product = Product::factory()->create($productData['attributes'] ?? []);
                $category->products()->attach($product->id, [
                    'sort_order' => $productData['sort_order'] ?? 1,
                ]);
            }
        });
    }

    /**
     * Cocktails category
     */
    public function cocktails(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Cocktails',
            'slug' => 'cocktails',
            'description' => 'Cocktails are a type of alcoholic beverage made with a base of spirits, such as vodka, gin, rum, or whiskey, mixed with other ingredients such as fruit juice, syrups, or bitters.',
            'sort_order' => 10,
        ]);
    }

    /**
     * Beers category
     */
    public function beers(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Beers',
            'slug' => 'beers',
            'description' => 'Beers are a type of alcoholic beverage made with a base of malt, such as barley, wheat, or rice, mixed with other ingredients such as hops, yeast, or water.',
            'sort_order' => 20,
        ]);
    }

    /**
     * Wines category
     */
    public function wines(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Wines',
            'slug' => 'wines',
            'description' => 'Wines are a type of alcoholic beverage made with a base of grapes, such as red or white wine, mixed with other ingredients such as fruit juice, syrups, or bitters.',
            'sort_order' => 30,
        ]);
    }

    /**
     * Soft Drinks category
     */
    public function softDrinks(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Soft Drinks',
            'slug' => 'soft-drinks',
            'description' => 'Soft drinks are a type of non-alcoholic beverage made with a base of water, such as soda, juice, or tea, mixed with other ingredients such as fruit juice, syrups, or bitters.',
            'sort_order' => 40,
        ]);
    }

    /**
     * Mocktails category
     */
    public function mocktails(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Mocktails',
            'slug' => 'mocktails',
            'description' => 'Mocktails are a type of non-alcoholic beverage made with a base of water, such as soda, juice, or tea, mixed with other ingredients such as fruit juice, syrups, or bitters.',
            'sort_order' => 50,
        ]);
    }
}

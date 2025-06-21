<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
final class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'price_in_cents' => fake()->numberBetween(500, 2500), // $5.00 to $25.00
        ];
    }

    /**
     * Create a product with ingredients attached
     */
    public function withIngredients(int $count = 1, array $ingredientAttributes = [], array $pivotAttributes = []): static
    {
        return $this->afterCreating(function (Product $product) use ($count, $ingredientAttributes, $pivotAttributes) {
            $ingredients = Ingredient::factory()->count($count)->create($ingredientAttributes);

            foreach ($ingredients as $index => $ingredient) {
                $product->ingredients()->attach($ingredient->id, [
                    'type' => $pivotAttributes['type'] ?? 'base',
                ]);
            }
        });
    }

    /**
     * Create a product with specific ingredients
     */
    public function withSpecificIngredients(array $ingredients): static
    {
        return $this->afterCreating(function (Product $product) use ($ingredients) {
            foreach ($ingredients as $ingredientData) {
                $ingredient = Ingredient::factory()->create($ingredientData['attributes'] ?? []);
                $product->ingredients()->attach($ingredient->id, [
                    'type' => $ingredientData['pivot']['type'] ?? 'base',
                ]);
            }
        });
    }

    public function withCategories(int $count = 1, array $categoryAttributes = []): static
    {
        return $this->afterCreating(function (Product $product) use ($count, $categoryAttributes) {
            $categories = Category::factory()->count($count)->create($categoryAttributes);
            $product->categories()->attach($categories->pluck('id'));
        });
    }

    /**
     * Indicate that the product is expensive.
     */
    public function expensive(): static
    {
        return $this->state(fn (array $attributes) => [
            'price_in_cents' => fake()->numberBetween(2000, 5000), // $20.00 to $50.00
        ]);
    }

    /**
     * Indicate that the product is cheap.
     */
    public function cheap(): static
    {
        return $this->state(fn (array $attributes) => [
            'price_in_cents' => fake()->numberBetween(200, 800), // $2.00 to $8.00
        ]);
    }

    // Cocktails
    public function mojito(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Mojito',
            'slug' => 'mojito',
            'description' => 'Refreshing Cuban cocktail with rum, lime, mint, and soda',
            'price_in_cents' => 1200,
        ]);
    }

    public function margarita(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Margarita',
            'slug' => 'margarita',
            'description' => 'Classic tequila cocktail with lime and triple sec',
            'price_in_cents' => 1100,
        ]);
    }

    public function martini(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Martini',
            'slug' => 'martini',
            'description' => 'Sophisticated gin and vermouth cocktail',
            'price_in_cents' => 1400,
        ]);
    }

    public function oldFashioned(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Old Fashioned',
            'slug' => 'old-fashioned',
            'description' => 'Classic whiskey cocktail with bitters and sugar',
            'price_in_cents' => 1300,
        ]);
    }

    // Beers
    public function heineken(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Heineken',
            'slug' => 'heineken',
            'description' => 'Premium lager beer from the Netherlands',
            'price_in_cents' => 800,
        ]);
    }

    public function coronaExtra(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Corona Extra',
            'slug' => 'corona-extra',
            'description' => 'Mexican lager with lime',
            'price_in_cents' => 900,
        ]);
    }

    public function guinness(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Guinness',
            'slug' => 'guinness',
            'description' => 'Irish dry stout with creamy head',
            'price_in_cents' => 1000,
        ]);
    }

    // Wines
    public function houseRedWine(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'House Red Wine',
            'slug' => 'house-red-wine',
            'description' => 'Premium house red wine selection',
            'price_in_cents' => 1200,
        ]);
    }

    public function houseWhiteWine(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'House White Wine',
            'slug' => 'house-white-wine',
            'description' => 'Premium house white wine selection',
            'price_in_cents' => 1200,
        ]);
    }

    public function champagne(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Champagne',
            'slug' => 'champagne',
            'description' => 'French sparkling wine',
            'price_in_cents' => 1800,
        ]);
    }

    // Soft Drinks
    public function cocaCola(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Coca Cola',
            'slug' => 'coca-cola',
            'description' => 'Classic cola beverage',
            'price_in_cents' => 400,
        ]);
    }

    public function sprite(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Sprite',
            'slug' => 'sprite',
            'description' => 'Lemon-lime flavored soft drink',
            'price_in_cents' => 400,
        ]);
    }

    public function orangeJuice(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Orange Juice',
            'slug' => 'orange-juice',
            'description' => 'Fresh squeezed orange juice',
            'price_in_cents' => 600,
        ]);
    }

    // Mocktails
    public function virginMojito(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Virgin Mojito',
            'slug' => 'virgin-mojito',
            'description' => 'Non-alcoholic version of the classic mojito',
            'price_in_cents' => 800,
        ]);
    }

    public function shirleyTemple(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Shirley Temple',
            'slug' => 'shirley-temple',
            'description' => 'Ginger ale with grenadine and cherry garnish',
            'price_in_cents' => 700,
        ]);
    }
}

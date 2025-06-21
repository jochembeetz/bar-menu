<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ingredient>
 */
final class IngredientFactory extends Factory
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
        ];
    }

    // Spirits and liqueurs
    public function vodka(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Vodka',
            'slug' => 'vodka',
            'description' => 'Clear distilled spirit made from fermented grains or potatoes',
        ]);
    }

    public function gin(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Gin',
            'slug' => 'gin',
            'description' => 'Distilled spirit flavored with juniper berries and other botanicals',
        ]);
    }

    public function rum(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Rum',
            'slug' => 'rum',
            'description' => 'Distilled spirit made from sugarcane byproducts',
        ]);
    }

    public function whiskey(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Whiskey',
            'slug' => 'whiskey',
            'description' => 'Distilled spirit made from fermented grain mash',
        ]);
    }

    public function tequila(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Tequila',
            'slug' => 'tequila',
            'description' => 'Distilled spirit made from blue agave',
        ]);
    }

    public function bourbon(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Bourbon',
            'slug' => 'bourbon',
            'description' => 'American whiskey made primarily from corn',
        ]);
    }

    public function scotch(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Scotch',
            'slug' => 'scotch',
            'description' => 'Whiskey made in Scotland from malted barley',
        ]);
    }

    public function brandy(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Brandy',
            'slug' => 'brandy',
            'description' => 'Distilled spirit made from fermented fruit juice',
        ]);
    }

    public function tripleSec(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Triple Sec',
            'slug' => 'triple-sec',
            'description' => 'Orange-flavored liqueur',
        ]);
    }

    public function vermouth(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Vermouth',
            'slug' => 'vermouth',
            'description' => 'Aromatized fortified wine flavored with botanicals',
        ]);
    }

    // Juices and mixers
    public function limeJuice(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Lime Juice',
            'slug' => 'lime-juice',
            'description' => 'Fresh squeezed lime juice',
        ]);
    }

    public function lemonJuice(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Lemon Juice',
            'slug' => 'lemon-juice',
            'description' => 'Fresh squeezed lemon juice',
        ]);
    }

    public function orangeJuice(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Orange Juice',
            'slug' => 'orange-juice',
            'description' => 'Fresh squeezed orange juice',
        ]);
    }

    public function cranberryJuice(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Cranberry Juice',
            'slug' => 'cranberry-juice',
            'description' => 'Cranberry juice cocktail',
        ]);
    }

    public function pineappleJuice(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Pineapple Juice',
            'slug' => 'pineapple-juice',
            'description' => 'Fresh pineapple juice',
        ]);
    }

    public function grapefruitJuice(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Grapefruit Juice',
            'slug' => 'grapefruit-juice',
            'description' => 'Fresh grapefruit juice',
        ]);
    }

    public function simpleSyrup(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Simple Syrup',
            'slug' => 'simple-syrup',
            'description' => 'Equal parts sugar and water',
        ]);
    }

    public function grenadine(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Grenadine',
            'slug' => 'grenadine',
            'description' => 'Pomegranate-flavored syrup',
        ]);
    }

    public function angosturaBitters(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Angostura Bitters',
            'slug' => 'angostura-bitters',
            'description' => 'Aromatic bitters',
        ]);
    }

    public function sodaWater(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Soda Water',
            'slug' => 'soda-water',
            'description' => 'Carbonated water',
        ]);
    }

    public function tonicWater(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Tonic Water',
            'slug' => 'tonic-water',
            'description' => 'Carbonated water with quinine',
        ]);
    }

    public function cola(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Cola',
            'slug' => 'cola',
            'description' => 'Carbonated cola beverage',
        ]);
    }

    public function gingerBeer(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Ginger Beer',
            'slug' => 'ginger-beer',
            'description' => 'Non-alcoholic ginger-flavored beverage',
        ]);
    }

    // Garnishes
    public function mintLeaves(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Mint Leaves',
            'slug' => 'mint-leaves',
            'description' => 'Fresh mint leaves for garnish',
        ]);
    }

    public function olive(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Olive',
            'slug' => 'olive',
            'description' => 'Green olive for garnish',
        ]);
    }

    public function limeWedge(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Lime Wedge',
            'slug' => 'lime-wedge',
            'description' => 'Lime wedge for garnish',
        ]);
    }

    public function lemonWedge(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Lemon Wedge',
            'slug' => 'lemon-wedge',
            'description' => 'Lemon wedge for garnish',
        ]);
    }

    public function orangeSlice(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Orange Slice',
            'slug' => 'orange-slice',
            'description' => 'Orange slice for garnish',
        ]);
    }

    public function cherry(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Cherry',
            'slug' => 'cherry',
            'description' => 'Maraschino cherry for garnish',
        ]);
    }
}

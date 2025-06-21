<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories for reference
        $categories = Category::all()->keyBy('slug');

        // Get ingredients for reference
        $ingredients = Ingredient::all()->keyBy('slug');

        // Create products using factory and attach relationships
        $this->createCocktails($categories, $ingredients);
        $this->createBeers($categories, $ingredients);
        $this->createWines($categories);
        $this->createSoftDrinks($categories);
        $this->createMocktails($categories, $ingredients);
    }

    private function createCocktails($categories, $ingredients): void
    {
        $cocktailCategory = $categories->get('cocktails');
        if (! $cocktailCategory) {
            return;
        }

        // Mojito
        $mojito = Product::factory()->mojito()->create();
        $mojito->categories()->attach($cocktailCategory->id, ['sort_order' => 10]);
        $mojito->ingredients()->attach([
            $ingredients->get('rum')->id => ['type' => 'base'],
            $ingredients->get('lime-juice')->id => ['type' => 'base'],
            $ingredients->get('simple-syrup')->id => ['type' => 'base'],
            $ingredients->get('mint-leaves')->id => ['type' => 'base'],
            $ingredients->get('soda-water')->id => ['type' => 'base'],
        ]);

        // Margarita
        $margarita = Product::factory()->margarita()->create();
        $margarita->categories()->attach($cocktailCategory->id, ['sort_order' => 20]);
        $margarita->ingredients()->attach([
            $ingredients->get('tequila')->id => ['type' => 'base'],
            $ingredients->get('triple-sec')->id => ['type' => 'base'],
            $ingredients->get('lime-juice')->id => ['type' => 'base'],
            $ingredients->get('lime-wedge')->id => ['type' => 'optional'],
        ]);

        // Martini
        $martini = Product::factory()->martini()->create();
        $martini->categories()->attach($cocktailCategory->id, ['sort_order' => 30]);
        $martini->ingredients()->attach([
            $ingredients->get('gin')->id => ['type' => 'base'],
            $ingredients->get('vermouth')->id => ['type' => 'base'],
            $ingredients->get('olive')->id => ['type' => 'optional'],
        ]);

        // Old Fashioned
        $oldFashioned = Product::factory()->oldFashioned()->create();
        $oldFashioned->categories()->attach($cocktailCategory->id, ['sort_order' => 40]);
        $oldFashioned->ingredients()->attach([
            $ingredients->get('bourbon')->id => ['type' => 'base'],
            $ingredients->get('simple-syrup')->id => ['type' => 'base'],
            $ingredients->get('angostura-bitters')->id => ['type' => 'base'],
            $ingredients->get('orange-slice')->id => ['type' => 'optional'],
            $ingredients->get('cherry')->id => ['type' => 'optional'],
        ]);
    }

    private function createBeers($categories, $ingredients): void
    {
        $beerCategory = $categories->get('beers');
        if (! $beerCategory) {
            return;
        }

        // Heineken
        $heineken = Product::factory()->heineken()->create();
        $heineken->categories()->attach($beerCategory->id, ['sort_order' => 10]);

        // Corona Extra
        $corona = Product::factory()->coronaExtra()->create();
        $corona->categories()->attach($beerCategory->id, ['sort_order' => 20]);
        $corona->ingredients()->attach([
            $ingredients->get('lime-wedge')->id => ['type' => 'optional'],
        ]);

        // Guinness
        $guinness = Product::factory()->guinness()->create();
        $guinness->categories()->attach($beerCategory->id, ['sort_order' => 30]);
    }

    private function createWines($categories): void
    {
        $wineCategory = $categories->get('wines');
        if (! $wineCategory) {
            return;
        }

        // House Red Wine
        $redWine = Product::factory()->houseRedWine()->create();
        $redWine->categories()->attach($wineCategory->id, ['sort_order' => 10]);

        // House White Wine
        $whiteWine = Product::factory()->houseWhiteWine()->create();
        $whiteWine->categories()->attach($wineCategory->id, ['sort_order' => 20]);

        // Champagne
        $champagne = Product::factory()->champagne()->create();
        $champagne->categories()->attach($wineCategory->id, ['sort_order' => 30]);
    }

    private function createSoftDrinks($categories): void
    {
        $softDrinkCategory = $categories->get('soft-drinks');
        if (! $softDrinkCategory) {
            return;
        }

        // Coca Cola
        $cocaCola = Product::factory()->cocaCola()->create();
        $cocaCola->categories()->attach($softDrinkCategory->id, ['sort_order' => 10]);

        // Sprite
        $sprite = Product::factory()->sprite()->create();
        $sprite->categories()->attach($softDrinkCategory->id, ['sort_order' => 20]);

        // Orange Juice
        $orangeJuice = Product::factory()->orangeJuice()->create();
        $orangeJuice->categories()->attach($softDrinkCategory->id, ['sort_order' => 30]);
    }

    private function createMocktails($categories, $ingredients): void
    {
        $mocktailCategory = $categories->get('mocktails');
        if (! $mocktailCategory) {
            return;
        }

        // Virgin Mojito
        $virginMojito = Product::factory()->virginMojito()->create();
        $virginMojito->categories()->attach($mocktailCategory->id, ['sort_order' => 10]);
        $virginMojito->ingredients()->attach([
            $ingredients->get('lime-juice')->id => ['type' => 'base'],
            $ingredients->get('simple-syrup')->id => ['type' => 'base'],
            $ingredients->get('mint-leaves')->id => ['type' => 'base'],
            $ingredients->get('soda-water')->id => ['type' => 'base'],
        ]);

        // Shirley Temple
        $shirleyTemple = Product::factory()->shirleyTemple()->create();
        $shirleyTemple->categories()->attach($mocktailCategory->id, ['sort_order' => 20]);
        $shirleyTemple->ingredients()->attach([
            $ingredients->get('ginger-beer')->id => ['type' => 'base'],
            $ingredients->get('grenadine')->id => ['type' => 'base'],
            $ingredients->get('cherry')->id => ['type' => 'optional'],
        ]);
    }
}

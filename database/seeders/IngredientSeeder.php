<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Spirits and liqueurs
        Ingredient::factory()->vodka()->create();
        Ingredient::factory()->gin()->create();
        Ingredient::factory()->rum()->create();
        Ingredient::factory()->whiskey()->create();
        Ingredient::factory()->tequila()->create();
        Ingredient::factory()->bourbon()->create();
        Ingredient::factory()->scotch()->create();
        Ingredient::factory()->brandy()->create();
        Ingredient::factory()->tripleSec()->create();
        Ingredient::factory()->vermouth()->create();

        // Juices and mixers
        Ingredient::factory()->limeJuice()->create();
        Ingredient::factory()->lemonJuice()->create();
        Ingredient::factory()->orangeJuice()->create();
        Ingredient::factory()->cranberryJuice()->create();
        Ingredient::factory()->pineappleJuice()->create();
        Ingredient::factory()->grapefruitJuice()->create();
        Ingredient::factory()->simpleSyrup()->create();
        Ingredient::factory()->grenadine()->create();
        Ingredient::factory()->angosturaBitters()->create();
        Ingredient::factory()->sodaWater()->create();
        Ingredient::factory()->tonicWater()->create();
        Ingredient::factory()->cola()->create();
        Ingredient::factory()->gingerBeer()->create();

        // Garnishes
        Ingredient::factory()->mintLeaves()->create();
        Ingredient::factory()->olive()->create();
        Ingredient::factory()->limeWedge()->create();
        Ingredient::factory()->lemonWedge()->create();
        Ingredient::factory()->orangeSlice()->create();
        Ingredient::factory()->cherry()->create();
    }
}

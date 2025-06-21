<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::insert([
            'name' => 'Cocktails',
            'slug' => 'cocktails',
            'description' => 'Cocktails are a type of alcoholic beverage made with a base of spirits, such as vodka, gin, rum, or whiskey, mixed with other ingredients such as fruit juice, syrups, or bitters.',
            'sort_order' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Category::insert([
            'name' => 'Beers',
            'slug' => 'beers',
            'description' => 'Beers are a type of alcoholic beverage made with a base of malt, such as barley, wheat, or rice, mixed with other ingredients such as hops, yeast, or water.',
            'sort_order' => 20,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Category::insert([
            'name' => 'Wines',
            'slug' => 'wines',
            'description' => 'Wines are a type of alcoholic beverage made with a base of grapes, such as red or white wine, mixed with other ingredients such as fruit juice, syrups, or bitters.',
            'sort_order' => 30,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Category::insert([
            'name' => 'Soft Drinks',
            'slug' => 'soft-drinks',
            'description' => 'Soft drinks are a type of non-alcoholic beverage made with a base of water, such as soda, juice, or tea, mixed with other ingredients such as fruit juice, syrups, or bitters.',
            'sort_order' => 40,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Category::insert([
            'name' => 'Mocktails',
            'slug' => 'mocktails',
            'description' => 'Mocktails are a type of non-alcoholic beverage made with a base of water, such as soda, juice, or tea, mixed with other ingredients such as fruit juice, syrups, or bitters.',
            'sort_order' => 50,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

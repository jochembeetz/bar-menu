<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

final class CategorySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::factory()->cocktails()->create();
        Category::factory()->beers()->create();
        Category::factory()->wines()->create();
        Category::factory()->softDrinks()->create();
        Category::factory()->mocktails()->create();
    }
}

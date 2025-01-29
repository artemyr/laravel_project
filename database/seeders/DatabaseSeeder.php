<?php

namespace Database\Seeders;

use App\Models\Product;
use Domain\Auth\Models\User;
use Domain\Catalog\Models\Brand;
use Domain\Catalog\Models\Category;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'artemymaratovitch@yandex.ru',
        ]);

        Brand::factory(20)->create();
        Category::factory(20)->create();
        Product::factory(20)
            ->has(Category::factory(rand(1,3)))
            ->create();
    }
}

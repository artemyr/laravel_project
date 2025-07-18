<?php

namespace Database\Seeders;

use Domain\Auth\Models\User;
use Domain\Catalog\Models\Brand;
use Domain\Catalog\Models\Category;
use Domain\Product\Models\Option;
use Domain\Product\Models\OptionValue;
use Domain\Product\Models\Product;
use Domain\Product\Models\Property;
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

        Brand::factory(20)
            ->create();

        $properties = Property::factory(10)
            ->create();

        Option::factory(2)
            ->create();

        $optionValues = OptionValue::factory(10)
            ->create();

        Category::factory(10)
            ->has(
                Product::factory(10)
                    ->hasAttached($optionValues)
                    ->hasAttached($properties, function () {
                        return ['value' => ucfirst(fake()->word())];
                    })
            )
            ->create();

        //        Product::factory(20)
        //            ->has(Category::factory(rand(1,3)))
        //            ->create();
    }
}

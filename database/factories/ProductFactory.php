<?php

namespace Database\Factories;

use Domain\Catalog\Models\Brand;
use Domain\Product\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Domain\Product\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => ucfirst($this->faker->words(2, true)),
            'brand_id' => Brand::query()->inRandomOrder()->value('id'),
            'thumbnail' => $this->faker->fixturesImage('products', 'products'),
            'price' => $this->faker->numberBetween(1000, 100000),
            'on_home_page' => $this->faker->boolean(),
            'sorting' => $this->faker->numberBetween(1, 999),
            'text' => $this->faker->realText(),
        ];
    }
}

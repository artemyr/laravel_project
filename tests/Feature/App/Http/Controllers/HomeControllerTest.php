<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\HomeController;
use Domain\Catalog\Models\Brand;
use Domain\Catalog\Models\Category;
use Domain\Product\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_success_response(): void
    {
        Brand::factory()->count(5)
            ->create([
                'on_home_page' => true,
                'sorting' => 999,
            ]);

        $brand = Brand::factory()
            ->createOne([
                'on_home_page' => true,
                'sorting' => 1,
            ]);

        Product::factory()
            ->create([
                'on_home_page' => true,
                'sorting' => 999,
            ]);

        $product = Product::factory()
            ->createOne([
                'on_home_page' => true,
                'sorting' => 1,
            ]);

        Category::factory()->count(5)
            ->create([
                'on_home_page' => true,
                'sorting' => 999,
            ]);

        $category = Category::factory()
            ->createOne([
                'on_home_page' => true,
                'sorting' => 1,
            ]);

        $this->get(action(HomeController::class))
            ->assertOk()
            ->assertViewHas('categories.0', $category)
            ->assertViewHas('products.0', $product)
            ->assertViewHas('brands.0', $brand);
    }
}

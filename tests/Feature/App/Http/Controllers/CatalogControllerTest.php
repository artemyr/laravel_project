<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\CatalogController;
use Domain\Catalog\Models\Brand;
use Domain\Product\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_success_price_filtered_response(): void
    {
        Brand::factory(3)->create();

        $products = Product::factory(10)
            ->create([
                'price' => 200,
            ]);

        $expectedProduct = Product::factory()
            ->createOne([
                'price' => 100000,
            ]);

        $request = [
            'filters' => [
                'price' => ['from' => 999, 'to' => 1001],
            ],
        ];

        $this->get(action(CatalogController::class, $request))
            ->assertOk()
            ->assertSee($expectedProduct->title)
            ->assertDontSee($products->random()->first()->title);
    }

    public function test_it_success_brand_filtered_response(): void
    {
        Brand::factory()->create();

        $products = Product::factory(10)
            ->create();

        $brand = Brand::factory()->create();

        $expectedProduct = Product::factory()
            ->createOne([
                'brand_id' => $brand,
            ]);

        $request = [
            'filters' => [
                'brands' => [$brand->id => $brand->id],
            ],
        ];

        $this->get(action(CatalogController::class, $request))
            ->assertOk()
            ->assertSee($expectedProduct->title)
            ->assertDontSee($products->random()->first()->title);
    }

    public function test_it_success_sorted_response(): void
    {
        Brand::factory(3)->create();

        $products = Product::factory(3)
            ->create();

        $request = [
            'sort' => 'title',
        ];

        $this->get(action(CatalogController::class, $request))
            ->assertOk()
            ->assertSeeInOrder(
                $products->sortBy('title')
                    ->flatMap(fn ($item) => [$item->title])
                    ->toArray()
            );
    }
}

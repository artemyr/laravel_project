<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\ProductController;
use Domain\Catalog\Models\Brand;
use Domain\Product\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_success_response(): void
    {
        Brand::factory(3)->create();

        $product = Product::factory()->createOne();

        $this->get(action(ProductController::class, $product))
            ->assertOk();
    }
}

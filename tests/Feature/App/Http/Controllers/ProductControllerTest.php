<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\ProductController;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_success_response(): void
    {
        $product = Product::factory()->createOne();

        $this->get(ProductController::class, $product)
            ->assertOk();
    }
}

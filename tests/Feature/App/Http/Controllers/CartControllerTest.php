<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\CartController;
use Domain\Cart\CartManager;
use Domain\Catalog\Models\Brand;
use Domain\Product\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        CartManager::fake();
    }

    public function test_it_is_empty(): void
    {
        $this->get(action([CartController::class, 'index']))
            ->assertOk()
            ->assertViewIs('cart.index')
            ->assertViewHas('items', collect([]));
    }

    public function test_it_is_not_empty_cart(): void
    {
        Brand::factory()->create();

        $product = Product::factory()->create();

        cart()->add($product);

        $this->get(action([CartController::class, 'index']))
            ->assertOk()
            ->assertViewIs('cart.index')
            ->assertViewHas('items', cart()->items());
    }

    public function test_it_added_success(): void
    {
        Brand::factory()->create();

        $product = Product::factory()->create();

        $this->assertEquals(0, cart()->count());

        $this->post(action([CartController::class, 'add'], $product), ['quantity' => 4]);

        $this->assertEquals(4, cart()->count());
    }

    public function test_it_quantity_changed(): void
    {
        Brand::factory()->create();

        $product = Product::factory()->create();

        cart()->add($product, 4);

        $this->assertEquals(4, cart()->count());

        $this->post(action([CartController::class, 'quantity'], cart()->items()->first()), ['quantity' => 8]);

        $this->assertEquals(8, cart()->count());
    }

    public function test_it_delete_success(): void
    {
        Brand::factory()->create();

        $product = Product::factory()->create();

        cart()->add($product, 4);

        $this->assertEquals(4, cart()->count());

        $this->delete(action([CartController::class, 'delete'], cart()->items()->first()));

        $this->assertEquals(0, cart()->count());
    }

    public function test_it_truncate_success(): void
    {
        Brand::factory()->create();

        $product = Product::factory()->create();

        cart()->add($product, 4);

        $this->assertEquals(4, cart()->count());

        $this->delete(action([CartController::class, 'truncate']));

        $this->assertEquals(0, cart()->count());
    }
}

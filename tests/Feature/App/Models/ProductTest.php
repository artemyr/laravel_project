<?php

namespace Tests\Feature\App\Models;

use Domain\Catalog\Models\Brand;
use Domain\Product\Models\Product;
use Support\ValueObjects\Price;
use Tests\TestCase;

class ProductTest extends TestCase
{
    public function test_it_get_price()
    {
        $brand = Brand::factory()->create();

        $product = Product::factory()->createOne([
            'price' => 10000,
            'brand_id' => $brand->id,
        ]);

        $this->assertInstanceOf(Price::class, $product->price);
        $this->assertEquals('100 ₽', strval($product->price));
        $this->assertEquals(100, $product->price->value());
        $this->assertEquals(10000, $product->price->row());
        $this->assertEquals('RUB', $product->price->currency());
        $this->assertEquals('₽', $product->price->symbol());
    }
}

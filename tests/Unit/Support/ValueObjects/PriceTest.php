<?php

namespace Tests\Unit\Support\ValueObjects;

use Support\ValueObjects\Price;
use Tests\TestCase;

class PriceTest extends TestCase
{
    public function test_it_all(): void
    {
        $price = Price::make(10000);
        $this->assertInstanceOf(Price::class, $price);
        $this->assertEquals(100, $price->value());
        $this->assertEquals(10000, $price->row());
        $this->assertEquals('RUB', $price->currency());
        $this->assertEquals('₽', $price->symbol());
        $this->assertEquals('100 ₽', strval($price));

        $this->expectException(\InvalidArgumentException::class);
        Price::make(-10000);
        Price::make(10000, 'USD');
    }
}

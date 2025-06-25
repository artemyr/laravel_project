<?php

namespace Tests\Feature\App\Jobs;

use App\Jobs\ProductJsonProperties;
use App\Models\Product;
use App\Models\Property;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProductJsonPropertiesTest extends TestCase
{
    public function test_it_created_json_properties(): void
    {
        $queue = Queue::getFacadeRoot();

        Queue::fake([ProductJsonProperties::class]);

        $properties = Property::factory()->count(10)->create();

        $product = Product::factory()
            ->hasAttached($properties, function () {
                return ['value' => fake()->word()];
            })
            ->create();

        $this->assertEmpty($product->json_properties);

        Queue::swap($queue);

        ProductJsonProperties::dispatchSync($product);

        $product->refresh();

        $this->assertNotEmpty($product->json_properties);
    }
}

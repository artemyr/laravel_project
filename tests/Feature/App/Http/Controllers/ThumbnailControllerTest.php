<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Models\Product;
use Domain\Catalog\Models\Brand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ThumbnailControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generated_success(): void
    {
        $size = '500x500';
        $method = 'resize';
        $storage = Storage::disk('images');

        config()->set('thumbnail', ['allowed_sizes' => [$size]]);

        Brand::factory(3)->create();

        $product = Product::factory()->create();

        $response = $this->get($product->makeThumbnail($size, $method));

        $response->assertOk();

        $storage->assertExists(
            "products/$method/$size/" . File::basename($product->thumbnail)
        );
    }
}

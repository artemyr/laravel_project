<?php

namespace App\Jobs;

use Domain\Product\Models\Product;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProductJsonProperties implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Product $product
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->product->updateQuietly([
            'json_properties' => $this->product->properties->keyValues(),
        ]);
    }

    public function uniqueId()
    {
        return $this->product->getKey();
    }
}

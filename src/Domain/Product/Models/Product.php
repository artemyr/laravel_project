<?php

namespace Domain\Product\Models;

use App\Jobs\ProductJsonProperties;
use Database\Factories\ProductFactory;
use Domain\Catalog\Models\Brand;
use Domain\Catalog\Models\Category;
use Domain\Product\QueryBuilders\ProductQueryBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Support\Casts\PriceCast;
use Support\Traits\Models\HasSlug;
use Support\Traits\Models\HasThumbnail;

/**
 * @method static Product|ProductQueryBuilder query()
 */
class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    use HasSlug;
    use HasThumbnail;

    protected $fillable = [
        'title',
        'slug',
        'price',
        'brand_id',
        'thumbnail',
        'on_home_page',
        'sorting',
        'text',
        'json_properties',
    ];

    protected $casts = [
        'price' => PriceCast::class,
        'json_properties' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function (Product $product) {
            ProductJsonProperties::dispatch($product)
                ->delay(now()->addSeconds(10));
        });
    }

    public function newEloquentBuilder($query)
    {
        return new ProductQueryBuilder($query);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    protected function thumbnailDir(): string
    {
        return 'products';
    }

    protected static function newFactory()
    {
        return ProductFactory::new();
    }

    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class)
            ->withPivot('value');
    }

    public function optionValues(): BelongsToMany
    {
        return $this->belongsToMany(OptionValue::class);
    }
}

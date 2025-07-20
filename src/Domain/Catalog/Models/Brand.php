<?php

namespace Domain\Catalog\Models;

use Database\Factories\BrandFactory;
use Domain\Catalog\Collections\BrandCollection;
use Domain\Catalog\QueryBuilders\BrandQueryBuilder;
use Domain\Product\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Support\Traits\Models\Cacheable;
use Support\Traits\Models\HasSlug;
use Support\Traits\Models\HasThumbnail;

/**
 * @method static Category|BrandQueryBuilder query()
 */
class Brand extends Model
{
    use Cacheable;

    /** @use HasFactory<BrandFactory> */
    use HasFactory;

    use HasSlug;
    use HasThumbnail;

    protected $fillable = [
        'title',
        'slug',
        'thumbnail',
        'on_home_page',
        'sorting',
    ];

    public function newEloquentBuilder($query): BrandQueryBuilder
    {
        return new BrandQueryBuilder($query);
    }

    public function newCollection(array $models = []): BrandCollection
    {
        return new BrandCollection($models);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    protected function thumbnailDir(): string
    {
        return 'brands';
    }

    protected static function newFactory(): BrandFactory
    {
        return BrandFactory::new();
    }

    public function getCacheKeys(): array
    {
        return ['brand_home_page'];
    }
}

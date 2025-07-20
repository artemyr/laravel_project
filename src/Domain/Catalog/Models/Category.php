<?php

namespace Domain\Catalog\Models;

use Database\Factories\CategoryFactory;
use Domain\Catalog\Collections\CategoryCollection;
use Domain\Catalog\QueryBuilders\CategoryQueryBuilder;
use Domain\Product\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Support\Traits\Models\Cacheable;
use Support\Traits\Models\HasSlug;

/**
 * @method static Category|CategoryQueryBuilder query()
 */
class Category extends Model
{
    use Cacheable;

    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    use HasSlug;

    protected $fillable = [
        'title',
        'slug',
        'on_home_page',
        'sorting',
    ];

    public function newEloquentBuilder($query): CategoryQueryBuilder
    {
        return new CategoryQueryBuilder($query);
    }

    public function newCollection(array $models = []): CategoryCollection
    {
        return new CategoryCollection($models);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    protected static function newFactory(): CategoryFactory
    {
        return CategoryFactory::new();
    }

    public function getCacheKeys(): array
    {
        return ['category_home_page'];
    }
}

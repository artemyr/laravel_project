<?php

namespace Domain\Catalog\Models;

use App\Models\Product;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Support\Traits\Models\HasSlug;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;
    use HasSlug;

    protected $fillable = [
        'title',
        'slug',
        'on_home_page',
        'sorting'
    ];

    public function scopeHomePage(Builder $query)
    {
        $query->where('on_home_page', true)
            ->orderBy('sorting')
            ->limit(6);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    protected static function newFactory()
    {
        return CategoryFactory::new();
    }
}

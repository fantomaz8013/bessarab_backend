<?php

namespace App\Models;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title'  ,
        'description'  ,
        'result'  ,
        'Purpose'  ,
        'avatar_url'  ,
        'product_category_id'  ,
        'additional_product_category_id'  ,
        'compound'  ,
        'product_line_id'  ,
        'short_name'  ,
        'sort'  ,
        'brand_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('delete', function (Builder $builder) {
            $builder->where('is_delete',  0);
        });
    }

    public function scopeFilter(Builder $builder, QueryFilter $filters)
    {
        return $filters->apply($builder);
    }

    public function scopePages(Builder $builder, QueryFilter $filters)
    {
        return $filters->apply($builder);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function addCategory(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'additional_product_category_id');
    }

    public function line(): BelongsTo
    {
        return $this->belongsTo(ProductLines::class, 'product_line_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function sizes(): HasMany
    {
        return $this->hasMany(ProductSize::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_products', 'product_id', 'order_id');
    }
}

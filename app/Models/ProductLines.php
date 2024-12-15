<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductLines extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'img',
        'brand_id'
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}

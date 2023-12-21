<?php

namespace App\Models;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    /*
     * Заказ создан
     */
    const ORDER_STATUS_TYPE = 1;
    /*
     * Заказ принят
     */
    const ORDER_STATUS_ACCEPT = 2;
    /*
     * Заказ доставлен
     */
    const ORDER_STATUS_DELIVERED = 3;
    /*
     * Заказ закрыт
     */
    const ORDER_STATUS_CLOSED = 4;
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'city',
        'address',
    ];

    public function scopeFilter(Builder $builder, QueryFilter $filters)
    {
        return $filters->apply($builder);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_products', 'order_id', 'product_id');
    }
}

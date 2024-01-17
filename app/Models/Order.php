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
     * Заказ оплачен
     */
    const ORDER_STATUS_PAY = 2;
    /*
     * Заказ принят
     */
    const ORDER_STATUS_ACCEPT = 3;
    /*
     * Заказ доставлен
     */
    const ORDER_STATUS_DELIVERED = 4;
    /*
     * Заказ закрыт
     */
    const ORDER_STATUS_CLOSED = 5;
    /*
     * Заказ отклонен
     */
    const ORDER_STATUS_REJECT = 6;
    /*
     * Выполнен возврат
     */
    const ORDER_STATUS_REFUNDED = 7;
    protected $fillable = [
        'first_name',
        'email',
        'phone',
        'city',
        'address',
        'delivery_type',
    ];

    public function scopeFilter(Builder $builder, QueryFilter $filters)
    {
        return $filters->apply($builder);
    }

    public function products()
    {
        return $this
            ->belongsToMany(Product::class, 'order_products', 'order_id', 'product_id')
            ->withPivot('quantity');
    }
}

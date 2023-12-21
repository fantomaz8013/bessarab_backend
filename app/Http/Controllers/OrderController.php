<?php

namespace App\Http\Controllers;

use App\Filters\OrderFilter;
use App\Http\Requests\OrderStatusRequest;
use App\Http\Requests\OrderStoreRequest;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Список заказов
     * @bodyParam firstName string . Example: Шампунь
     * @bodyParam lastName string . Example: Шампунь
     * @bodyParam email string . Example: Шампунь
     * @bodyParam phone string . Example: Шампунь
     * @bodyParam city string . Example: Шампунь
     * @bodyParam address string . Example: Шампунь
     */
    public function index(OrderFilter $filter)
    {
        return Order::filter($filter)
            ->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Создать заказ
     */
    public function store(OrderStoreRequest $request)
    {
        $data = $request->validated();
        $order = Order::create($data);
        $products = $data['products'];
        foreach ($products as $product)
        {
            OrderProduct::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
            ]);
        }
        return response()->json(["result" => "Ok"]);
    }

    /**
     * Изменить статус заказа
     * @param OrderStatusRequest $request
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(OrderStatusRequest $request, Order $order)
    {
        $data = $request->validated();
        $order->status_id = $data['status_id'];
        return response()->json(["result" => "Ok"]);
    }


    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}

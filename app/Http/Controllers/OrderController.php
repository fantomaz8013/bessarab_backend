<?php

namespace App\Http\Controllers;

use App\Filters\OrderFilter;
use App\Http\Requests\OrderStatusRequest;
use App\Http\Requests\OrderStoreRequest;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\TelegramUser;
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
                'product_id' => $product,
            ]);
        }

        $order = Order::find($order->id);
        $order->load('products');

        $telegramUsers = TelegramUser::where('is_work', 1)
            ->get();

        $text = "<b>У вас новый заказ #{$order->id} </b>\n
<b>Заказчик:</b> {$order->first_name} {$order->last_name} \n
<b>Email Заказчика:</b> {$order->email} \n
<b>Телефон Заказчика:</b> {$order->phone} \n
<b>Город Заказчика:</b> {$order->city} \n
<b>Адрес Заказчика:</b> {$order->address} \n
<b>Состав заказа:</b> \n";

        $productText = "";

        foreach ($order->products as $product)
        {
            $productText.=$product->title." ". $product->price ."руб. \n";
        }

        $text.=$productText;

        foreach ($telegramUsers as $telegramUser)
        {
            $data = http_build_query([
                'chat_id' => $telegramUser->chat_id,
                'text' => $text,
                'parse_mode' => 'html'
            ]);
            file_get_contents("https://api.telegram.org/bot6720731238:AAGcZ4QSSFRVWYrL8BzuRbGYiMRoWQR8oAA/sendMessage?$data");
        }

        return response()->json(["result" => "Ok"]);
    }

    /**
     * Изменить статус заказа
     * @param OrderStatusRequest $request
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(OrderStatusRequest $request, Order $Order)
    {
        $data = $request->validated();
        $Order->status_id = $data['status_id'];
        $Order->save();
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

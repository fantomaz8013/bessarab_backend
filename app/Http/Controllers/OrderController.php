<?php

namespace App\Http\Controllers;

use App\Filters\OrderFilter;
use App\Http\Requests\OrderStatusRequest;
use App\Http\Requests\OrderStoreRequest;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\TelegramUser;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Список заказов
     * @queryParam  firstName string . Example: Иван
     * @queryParam  lastName string . Example: Иванов
     * @queryParam  email string . Example: Ivan@mail.ru
     * @queryParam  phone string . Example: 89999999999
     * @queryParam  city string . Example: Ивановск
     * @queryParam  address string . Example: улица иванова 15 квартира 14
     * @queryParam  page int Страница. Example: 1
     * @queryParam  limit int Сколько выдать записей. Example: 10.
     * @queryParam  orderByAsc string сортировка по возрастанию
     * @queryParam  orderByDesc string сортировка по убыванию
     */
    public function index(OrderFilter $filter)
    {
        $data =  Order::filter($filter)
            ->with('products')
            ->get();

        $pages = $filter->countPages;

        return response()->json(["result" => $data, "pages" => $pages]);
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
        DB::transaction(function() use ($data) {
            $order = Order::create($data);
            $products = $data['products'];
            foreach ($products as $product)
            {
                OrderProduct::create([
                    'order_id' => $order->id,
                    'product_id' => $product['id'],
                    'quantity' => $product['quantity'],
                    'product_size_id' => $product['size_id'],
                ]);
            }

            $allPrice = 0;

            $p = OrderProduct::where('order_id', $order->id)
                ->with('product')
                ->get();
            $items = [];
            foreach ($p as $product)
            {
                $productSize = ProductSize::find($product->product_size_id);
                $allPrice+=$productSize->price * $product->quantity;
                $items[] = [
                    'Name'  => $product->product->title,
                    'Price' => $productSize->price,    //цена товара в рублях
                    'NDS'   => 'vat20',  //НДС
                    'Quantity'   => $product->quantity,  //Количество
                ];
            }

            $payment = [
                'OrderId'       => $order->id,        //Ваш идентификатор платежа
                'Amount'        => $allPrice,        //сумма всего платежа в рублях
                'Language'      => 'ru',            //язык - используется для локализации страницы оплаты
                'Description'   => 'Оплата заказа на сайте',   //описание платежа
                'Email'         => $order->email,//email покупателя
                'Phone'         => $order->phone,   //телефон покупателя
                'Name'          => $order->first_name, //Имя покупателя
                'Taxation'      => 'usn_income'     //Налогооблажение
            ];

            //Получение url для оплаты
            $paymentURL =  $this->paymentService->init($payment, $items, $order->id);

            if(!$paymentURL){
                throw new \Exception($this->paymentService->error);
            }

            return response()->json(["result" => ['order_id' => $order->id, 'url' => $paymentURL]]);
        });

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

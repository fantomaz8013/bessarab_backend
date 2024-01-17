<?php

namespace App\Http\Controllers;

use App\Http\Requests\TinkoffWebhookRequest;
use App\Models\Order;
use App\Services\TinkoffApi;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected TinkoffApi $tinkoffApi;

    public function __construct(TinkoffApi $tinkoffApi)
    {
        $this->tinkoffApi = $tinkoffApi;
    }

    public function init()
    {
        $payment = [
            'OrderId'       => '123456',        //Ваш идентификатор платежа
            'Amount'        => '100',           //сумма всего платежа в рублях
            'Language'      => 'ru',            //язык - используется для локализации страницы оплаты
            'Description'   => 'Some buying',   //описание платежа
            'Email'         => 'user@email.com',//email покупателя
            'Phone'         => '89099998877',   //телефон покупателя
            'Name'          => 'Customer name', //Имя покупателя
            'Taxation'      => 'usn_income'     //Налогооблажение
        ];

        //подготовка массива с покупками
        $items[] = [
            'Name'  => 'Название товара',
            'Price' => '100',    //цена товара в рублях
            'NDS'   => 'vat20',  //НДС
            'Quantity'   => '1',  //Количество
        ];

        //Получение url для оплаты
        $paymentURL =  $this->tinkoffApi->paymentURL($payment, $items);

        if(!$paymentURL){
            echo($this->tinkoffApi->error);
        } else {
            $payment_id = $this->tinkoffApi->payment_id;
            return $paymentURL;
        }
    }

    public function webhook(TinkoffWebhookRequest $request)
    {
        $data = $request->validated();

        if (isset($data['orderId']))
        {
            $orderId = $data['orderId'];
            $order = Order::find($orderId);

            if (isset($data['Success']) && $data['Success'])
            {
                if (isset($data['Status']) && $data['Status'] == TinkoffApi::ORDER_STATUS_CONFIRMED && $order->status_id != Order::ORDER_STATUS_PAY)
                {
                    $order->status_id = Order::ORDER_STATUS_PAY;
                }
            }

            if (isset($data['Status']))
            {
                $order->payment_status = $data['Status'];
            }

            $order->save();
        }
        return response('OK', 200);
    }

}

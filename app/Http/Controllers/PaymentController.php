<?php

namespace App\Http\Controllers;

use App\Http\Requests\CancelPaymentRequest;
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

    /**
     * Отменить платеж
     * @param CancelPaymentRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function cancel(CancelPaymentRequest $request)
    {
        $data = $request->validated();
        if (isset($data['PaymentId']))
        {
            $PaymentId = $data['PaymentId'];
            $this->tinkoffApi->cencelPayment($PaymentId);
        }
        return response('OK', 200);
    }

    /**
     * Веб хук для оплаты
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function webhook(Request $request)
    {
        $data = $request->all();

        if (isset($data['OrderId']))
        {
            $orderId = $data['OrderId'];
            $order = Order::find($orderId);

            $order->ext_data = json_encode($data);
            $order->save();

            if (isset($data['Success']))
            {
                if ($data['Success'])
                {
                    if (isset($data['Status']))
                    {
                        if ($data['Status'] == TinkoffApi::ORDER_STATUS_CONFIRMED && $order->status_id != Order::ORDER_STATUS_PAY)
                        {
                            $order->status_id = Order::ORDER_STATUS_PAY;
                        }

                        if ($data['Status'] == TinkoffApi::ORDER_STATUS_REFUNDED)
                        {
                            $order->status_id = Order::ORDER_STATUS_REFUNDED;
                        }
                    }
                }
                if (!$data['Success'])
                {
                    if (isset($data['Status']))
                    {
                        if ($data['Status'] == TinkoffApi::ORDER_STATUS_REJECTED)
                        {
                            $order->status_id = Order::ORDER_STATUS_REJECT;
                        }
                    }
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

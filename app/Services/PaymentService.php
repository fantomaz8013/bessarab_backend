<?php


namespace App\Services;


use App\Models\Order;

class PaymentService
{
    protected TinkoffApi $tinkoffApi;

    public $error;
    protected $response;
    protected $payment_id;
    protected $payment_url;
    protected $payment_status;
    protected $last_generate_token;

    public function __construct(TinkoffApi $tinkoffApi)
    {
        $this->tinkoffApi = $tinkoffApi;
    }

    public function init(array $payment, array $items, $orderId)
    {
        $paymentURL =  $this->tinkoffApi->paymentURL($payment, $items);

        if(!$paymentURL){
            $this->error = $this->tinkoffApi->error;
        } else {
            $this->payment_id = $this->tinkoffApi->payment_id;
            $order = Order::find($orderId);
            $order->payment_token = $this->tinkoffApi->last_generate_token;
            $order->save();
            return $paymentURL;
        }
    }
}

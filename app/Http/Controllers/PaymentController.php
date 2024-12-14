<?php

namespace App\Http\Controllers;

use App\Http\Requests\CancelPaymentRequest;
use App\Http\Requests\TinkoffWebhookRequest;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\ProductSize;
use App\Models\TelegramUser;
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

            if ($order->payment_id != $data['PaymentId'])
            {
                return response('OK', 200);
            }

            if (isset($data['Success']))
            {
                if ($data['Success'])
                {
                    if (isset($data['Status']))
                    {
                        if ($data['Status'] == TinkoffApi::ORDER_STATUS_CONFIRMED && $order->status_id != Order::ORDER_STATUS_PAY)
                        {
                            $order->status_id = Order::ORDER_STATUS_PAY;
                            try {
                                $this->sendTelegram($order->id);
                            }
                            catch (\Exception $ex) {

                            }
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
            $data = http_build_query([
                'chat_id' => '386852571',
                'text' => json_encode($data)
            ]);
            file_get_contents("https://api.telegram.org/bot6720731238:AAGcZ4QSSFRVWYrL8BzuRbGYiMRoWQR8oAA/sendMessage?$data");
        }

        return response('OK', 200);
    }


    private function sendTelegram($orderId)
    {
        $order = Order::find($orderId);
        $telegramUsers = TelegramUser::where('is_work', 1)
            ->get();

        $text = "<b>У вас новый заказ #{$order->id} </b>\n
<b>Заказчик:</b> {$order->first_name} \n
<b>Статус:</b> Оплачен \n
<b>Email Заказчика:</b> {$order->email} \n
<b>Телефон Заказчика:</b> {$order->phone} \n
<b>Город Заказчика:</b> {$order->city} \n
<b>Адрес Заказчика:</b> {$order->address} \n
<b>Состав заказа:</b> \n";

        $productText = "";
        $allPrice = 0;

        $p = OrderProduct::where('order_id', $order->id)
            ->with('product')
            ->get();
        $items = [];
        foreach ($p as $product)
        {
            $productSize = ProductSize::find($product->product_size_id);
            $productText.= $product->product->title." ".$productSize->value.$productSize->unit." (x{$product->quantity}) ". $productSize->price ." руб. \n";
            $allPrice+=$productSize->price * $product->quantity;
            $items[] = [
                'Name'  => $product->product->title,
                'Price' => $productSize->price,    //цена товара в рублях
                'NDS'   => 'vat20',  //НДС
                'Quantity'   => $product->quantity,  //Количество
            ];
        }

        $text.=$productText;
        $text.="----------------------------------------\n";
        $text.="<b>Сумма заказа: </b>" . $allPrice;
        foreach ($telegramUsers as $telegramUser)
        {
            $data = http_build_query([
                'chat_id' => $telegramUser->chat_id,
                'text' => $text,
                'parse_mode' => 'html'
            ]);
            file_get_contents("https://api.telegram.org/bot6720731238:AAGcZ4QSSFRVWYrL8BzuRbGYiMRoWQR8oAA/sendMessage?$data");
        }
    }

}

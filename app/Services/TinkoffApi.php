<?php


namespace App\Services;


class TinkoffApi
{
    private $acquiring_url;
    private $terminal_id;
    private $secret_key;

    private $url_init;
    private $url_cancel;
    private $url_confirm;
    private $url_get_state;

    protected $error;
    protected $response;

    protected $payment_id;
    protected $payment_url;
    protected $payment_status;
    public $last_generate_token;

    public const ORDER_STATUS_NEW = 0;
    public const ORDER_STATUS_CANCELED = 1;
    public const ORDER_STATUS_PREAUTHORIZING = 2;
    public const ORDER_STATUS_FORMSHOWED = 3;
    public const ORDER_STATUS_AUTHORIZING = 4;
    public const ORDER_STATUS_ThreeDS_CHECKING = 5;
    public const ORDER_STATUS_ThreeDS_CHECKED = 6;
    public const ORDER_STATUS_AUTH_FAIL = 7;
    public const ORDER_STATUS_PAY_CHECKING = 8;
    public const ORDER_STATUS_AUTHORIZED = 9;
    public const ORDER_STATUS_REVERSING = 10;
    public const ORDER_STATUS_REVERSED = 11;
    public const ORDER_STATUS_CONFIRMING = 12;
    public const ORDER_STATUS_CONFIRM_CHECKING = 13;
    public const ORDER_STATUS_CONFIRMED = 14;
    public const ORDER_STATUS_REFUNDING = 15;
    public const ORDER_STATUS_ASYNC_REFUNDING = 16;
    public const ORDER_STATUS_PARTIAL_REFUNDED = 17;
    public const ORDER_STATUS_REFUNDED = 18;
    public const ORDER_STATUS_REJECTED = 19;
    public const ORDER_STATUS_DEADLINE_EXPIRED = 20;
    public const ORDER_STATUS_UNKNOWN = 21;

    /**
     * Inicialize Tinkoff class
     *
     * @param [string] $acquiring_url - tinkoff acquiring APi url
     * @param [string] $terminal_id   - acquiring terminal number
     * @param [string] $secret_key    - acquiring terminal password
     */
    public function __construct() {
        $this->acquiring_url  = 'https://securepay.tinkoff.ru/v2/';
        $this->terminal_id    = env('TINKOFF_TERMINAL');
        $this->secret_key     = env('TINKOFF_SECRET');
        $this->setupUrls();
    }

    /**
     * Generate payment URL
     *
     * -------------------------------------------------
     * For generate url need to send $payment array and array of $items
     * All keys for correct checking in paymentArrayChecked()
     * and itemsArrayChecked()
     *
     * Tinkoff does not accept a Item name longer than $item_name_max_lenght
     * $amount_multiplicator - need for convert price to cents
     *
     * @param  array  $payment array of payment data
     * @param  array  $items   array of items
     * @return mixed - return payment url if has no errors
     */
    public function paymentURL(array $payment, array $items){
        if ( !$this->paymentArrayChecked($payment) ) {
            $this->error = 'Incomplete payment data';
            return FALSE;
        }

        $item_name_max_lenght = 64;
        $amount_multiplicator = 100;

        /**
         * Generate items array for Receipt
         */
        foreach ($items as $item) {
            if ( !$this->itemsArrayChecked($item) ) {
                $this->error = 'Incomplete items data';
                return FALSE;
            }

            $payment['Items'][] = [
                'Name'      => mb_strimwidth($item['Name'], 0, $item_name_max_lenght - 1, ''),
                'Price'     => round($item['Price'] * $amount_multiplicator),
                'Quantity'  => $item['Quantity'],
                'Amount'    => round($item['Price'] * $item['Quantity'] * $amount_multiplicator),
                'Tax'       => $item['NDS'],
            ];
        }

        $params = array(
            'OrderId'       => $payment['OrderId'],
            'Amount'        => round($payment['Amount'] * $amount_multiplicator),
            'Language'      => $payment['Language'],
            'Description'   => $payment['Description'],
            'NotificationURL'   => 'https://germanbessarab.com/api/payment/webhook',
            'SuccessURL'   => 'https://germanbessarab.com/success?order='.$payment['OrderId'],
            'DATA' => [
                'Email'     => $payment['Email'],
                'Phone'     => $payment['Phone'],
                'Name'      => $payment['Name'],
            ],
            'Receipt' => [
                'Email'     => $payment['Email'],
                'Phone'     => $payment['Phone'],
                'Taxation'  => $payment['Taxation'],
                'Items'     => $payment['Items'],
            ],
        );

        if( $this->sendRequest($this->url_init, $params) ){
            return $this->payment_url;
        }

        return FALSE;
    }

    /**
     * Check payment status
     *
     * @param  [string] Tinkoff payment id
     * @return [mixed] status of payment or false
     */
    public function getState($payment_id){
        $params = [ 'PaymentId' => $payment_id ];

        if( $this->sendRequest($this->url_get_state, $params) ){
            return $this->payment_status;
        }

        return FALSE;
    }

    /**
     * Confirm payment
     *
     * @param  [string] Tinkoff payment id
     * @return [mixed] status of payment or false
     */
    public function confirmPayment($payment_id){
        $params = [ 'PaymentId' => $payment_id ];

        if( $this->sendRequest($this->url_confirm, $params) ){
            return $this->payment_status;
        }

        return FALSE;
    }

    /**
     * Cancel payment
     *
     * @param  [string] Tinkoff payment id
     * @return [mixed] status of payment or false
     */
    public function cencelPayment($payment_id){
        $params = [ 'PaymentId' => $payment_id ];

        if( $this->sendRequest($this->url_cancel, $params) ){
            return $this->payment_status;
        }

        return FALSE;
    }

    /**
     * Send reques to bank acquiring API
     *
     * @param  [string] $path url
     * @param  [array]  $args data
     * @return [json]   json decoded data
     */
    private function sendRequest($path,  array $args) {
        $args['TerminalKey'] = $this->terminal_id;
        $args['Token']       = $this->generateToken($args);
        $this->last_generate_token = $args['Token'];
        $args = json_encode($args);

        if($curl = curl_init()) {
            curl_setopt($curl, CURLOPT_URL, $path);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $args);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            $this->response = $response;
            $json = json_decode($response);

            if($json) {
                if ( $this->errorsFound() ) {
                    return FALSE;

                } else {
                    $this->payment_id       = @$json->PaymentId;
                    $this->payment_url      = @$json->PaymentURL;
                    $this->payment_status   = @$json->Status;

                    return TRUE;
                }
            }

            $this->error .= "Can't create connection to: $path | with args: $args";
            return FALSE;

        } else {
            $this->error .= "CURL init filed: $path | with args: $args";
            return FALSE;
        }
    }

    /**
     * Finding all possible errors
     * @return bool
     */
    private function errorsFound():bool {
        $response = json_decode($this->response, TRUE);

        if (isset($response['ErrorCode'])) {
            $error_code = (int) $response['ErrorCode'];
        } else {
            $error_code = 0;
        }

        if (isset($response['Message'])) {
            $error_msg = $response['Message'];
        } else {
            $error_msg = 'Unknown error.';
        }

        if (isset($response['Details'])) {
            $error_message = $response['Details'];
        } else {
            $error_message = 'Unknown error.';
        }

        if($error_code !== 0){
            $this->error = 'Error code: '. $error_code .
                ' | Msg: ' . $error_msg .
                ' | Message: ' . $error_message;
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Generate sha256 token for bank API
     *
     * @param  array of args
     * @return sha256 token
     */
    private function generateToken(array $args) {
        $token = '';
        $args['Password']    = $this->secret_key;
        $args['TerminalKey'] = $this->terminal_id;
        ksort($args);

        foreach ($args as $arg) {
            if (!is_array($arg)) {
                $token .= $arg;
            }
        }

        return hash('sha256', $token);
    }

    /**
     * Setting up urls for API
     *
     * @return void
     */
    private function setupUrls(){
        $this->acquiring_url = $this->checkSlashOnUrlEnd($this->acquiring_url);
        $this->url_init = $this->acquiring_url . 'Init/';
        $this->url_cancel = $this->acquiring_url . 'Cancel/';
        $this->url_confirm = $this->acquiring_url . 'Confirm/';
        $this->url_get_state = $this->acquiring_url . 'GetState/';
    }

    /**
     * Adding slash on end of url string if not there
     *
     * @return url string
     */
    private function checkSlashOnUrlEnd($url) {
        if ( $url[strlen($url) - 1] !== '/'){
            $url .= '/';
        }
        return $url;
    }

    /**
     * Check payment array for all keys is isset
     *
     * @param  array for checking
     * @return [bool]
     */
    private function paymentArrayChecked(array $array_for_check){
        $keys = ['OrderId', 'Amount', 'Language',
            'Description', 'Email', 'Phone',
            'Name', 'Email', 'Phone', 'Taxation'];
        return $this->allKeysIsExistInArray($keys, $array_for_check);
    }

    /**
     * Check items array for all keys is isset
     *
     * @param  array for checking
     * @return [bool]
     */
    private function itemsArrayChecked(array $array_for_check){
        $keys = ['Name', 'Price', 'NDS', 'Quantity'];
        return $this->allKeysIsExistInArray($keys, $array_for_check);
    }

    /**
     * Checking for existing all $keys in $arr
     *
     * @param  array $keys - array of keys
     * @param  array $arr - checked array
     * @return [bool]
     */
    private function allKeysIsExistInArray(array $keys, array $arr){
        return (bool) !array_diff_key(array_flip($keys), $arr);
    }

    /**
     * return protected propertys
     * @param  [mixed] $property name
     * @return [mixed]           value
     */
    public function __get($property){
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }
}

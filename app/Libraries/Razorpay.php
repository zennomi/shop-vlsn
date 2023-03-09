<?php

/**
 * Razorpay PHP library
 *
 **/

require_once APPPATH . "ThirdParty/razorpay/vendor/autoload.php";

use Razorpay\Api\Api;


class Razorpay
{
    /**
     * Privates
     */
    private $clientId = '';
    private $secret = '';
    private $client;

    /**
     * Constructor
     *
     * @access public
     * @param array
     */
    public function __construct($razorpay)
    {
        if (!empty($razorpay)) {
            $this->clientId = $razorpay->public_key;
            $this->secret = $razorpay->secret_key;
        }
        $this->client = new Api($this->clientId, $this->secret);
    }

    /**
     * Create Order
     *
     * @access public
     */
    public function createOrder($array)
    {
        try {
            $order = $this->client->order->create($array);
            if (!empty($order)) {
                return $order['id'];
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
            if ($message == 'Currency is not supported') {
                echo "Currency is not supported. You have to enable multi-currency support in your Razorpay account. If you don't know how to enable this option, please contact Razaorpay support.";
            } else {
                echo $message;
            }
        }
        return false;
    }

    /**
     * Verify Payment Signature
     *
     * @access public
     */
    public function verifyPaymentSignature($array)
    {
        $attributes = [
            'razorpay_signature' => $array['razorpay_signature'],
            'razorpay_payment_id' => $array['payment_id'],
            'razorpay_order_id' => $array['razorpay_order_id']
        ];
        try {
            $order = $this->client->utility->verifyPaymentSignature($attributes);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
<?php
/**
 * PayPal PHP library
 *
 **/

require_once APPPATH . "ThirdParty/paypal/vendor/autoload.php";

use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;

class Paypal
{

    /**
     * Privates
     */
    private $paypalClientId = '';
    private $paypalSecret = '';
    private $client;

    /**
     * Constructor
     *
     * @access public
     * @param array
     */
    public function __construct($paypal)
    {
        if (!empty($paypal)) {
            $this->paypalClientId = $paypal->public_key;
            $this->paypalSecret = $paypal->secret_key;
        }
        $environment = null;
        if ($paypal->environment == 'sandbox') {
            $environment = new SandboxEnvironment($this->paypalClientId, $this->paypalSecret);
        } else {
            $environment = new ProductionEnvironment($this->paypalClientId, $this->paypalSecret);
        }
        $this->client = new PayPalHttpClient($environment);
    }

    /**
     * Get Order
     *
     * @access public
     */
    public function getOrder($orderId)
    {
        try {
            $response = $this->client->execute(new OrdersGetRequest($orderId));
            if (!empty($response) && $response->result->status == 'COMPLETED') {
                return true;
            } else {
                return false;
            }
        } catch (BraintreeHttp\HttpException $ex) {
            return false;
        } catch (HttpException $ex) {
            return false;
        }
        return false;
    }
}
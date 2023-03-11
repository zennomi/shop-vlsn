<?php

/**
 * PayStack PHP library
 *
 **/

class Paystack
{
    /**
     * Privates
     */
    private $secretKey = '';
    private $publicKey = '';

    /**
     * Constructor
     *
     * @access public
     * @param array
     */
    public function __construct($paystack)
    {
        if (!empty($paystack)) {
            $this->secretKey = $paystack->secret_key;
        }
    }

    /**
     * Verify Transaction
     *
     * @access public
     */
    public function verifyTransaction($reference)
    {
        if (empty($reference)) {
            return false;
        }
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "authorization: Bearer " . $this->secretKey,
                "cache-control: no-cache"
            ],
        ));
        $response = curl_exec($curl);
        if (curl_error($curl)) {
            return false;
        }
        $transaction = json_decode($response);
        if (empty($transaction->status)) {
            return false;
        }
        if ($transaction->data->status == 'success') {
            return true;
        }
    }
}
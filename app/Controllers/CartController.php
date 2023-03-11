<?php

namespace App\Controllers;

use App\Models\CartModel;
use App\Models\MembershipModel;
use App\Models\OrderModel;
use App\Models\ProfileModel;
use App\Models\PromoteModel;
use App\Models\ShippingModel;

class CartController extends BaseController
{
    protected $cartModel;
    protected $orderModel;
    protected $membershipModel;
    public $sessionCartItems;

    /*
     * Payment Types
     *
     * 1. sale: Product purchases
     * 2. membership: Membership purchases
     * 3. promote: Promote purchases
     *
     */
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->cartModel = new CartModel();
        $this->orderModel = new OrderModel();
        $this->membershipModel = new MembershipModel();
        $this->sessionCartItems = $this->cartModel->getSessCartItems();
        $this->cartModel->calculateCartTotal($this->sessionCartItems);
    }

    /**
     * Cart
     */
    public function cart()
    {
        $data['title'] = trans("shopping_cart");
        $data['description'] = trans("shopping_cart") . ' - ' . $this->baseVars->appName;
        $data['keywords'] = trans("shopping_cart") . ',' . $this->baseVars->appName;
        $data['cartItems'] = $this->sessionCartItems;
        $data['cartTotal'] = $this->cartModel->getSessCartTotal();
        $data['cartHasPhysicalProduct'] = $this->cartModel->checkCartHasPhysicalProduct();
        
        echo view('partials/_header', $data);
        echo view('cart/cart', $data);
        echo view('partials/_footer');
    }

    /**
     * Add to Cart
     */
    public function addToCart()
    {
        $productId = inputPost('product_id');
        $isAjax = inputPost('is_ajax');
        $product = $this->productModel->getActiveProduct($productId);
        if (!empty($product)) {
            if ($product->status != 1) {
                $this->session->setFlashdata('product_details_error', trans("msg_error_cart_unapproved_products"));
            } else {
                $this->cartModel->addToCart($product);
                if (empty($isAjax)) {
                    return redirect()->to(generateUrl('cart'));
                }
            }
        }
        if (empty($isAjax)) {
            return redirect()->back();
        } else {
            $data = [
                'result' => 1,
                'productCount' => getCartProductCount()
            ];
            echo json_encode($data);
        }
    }

    /**
     * Add to Cart qQuote
     */
    public function addToCartQuote()
    {
        $quoteRequestId = inputPost('id');
        if (!empty($this->cartModel->addToCartQuote($quoteRequestId))) {
            return redirect()->to(generateUrl('cart'));
        }
        return redirect()->back();
    }

    /**
     * Remove from Cart
     */
    public function removeFromCart()
    {
        $cartItemId = inputPost('cart_item_id');
        $this->cartModel->removeFromCart($cartItemId);
    }

    /**
     * Remove Cart Discount Coupon
     */
    public function removeCartDiscountCoupon()
    {
        $this->cartModel->removeCoupon();
    }

    /**
     * Update Cart Product Quantity
     */
    public function updateCartProductQuantity()
    {
        $productId = inputPost('product_id');
        $cartItemId = inputPost('cart_item_id');
        $quantity = inputPost('quantity', true);
        $this->cartModel->updateCartProductQuantity($productId, $cartItemId, $quantity);
    }

    /**
     * Coupon Code Post
     */
    public function couponCodePost()
    {
        $couponCode = inputPost('coupon_code');
        $this->cartModel->applyCoupon($couponCode, $this->sessionCartItems);
        return redirect()->to(generateUrl('cart'))->withInput();
    }

    /**
     * Shipping
     */
    public function shipping()
    {
        $profileModel = new ProfileModel();
        $shippingModel = new ShippingModel();
        $this->cartModel->validateCart();
        $data['title'] = trans("shopping_cart");
        $data['description'] = trans("shopping_cart") . ' - ' . $this->baseVars->appName;
        $data['keywords'] = trans("shopping_cart") . ',' . $this->baseVars->appName;
        $data['cartItems'] = $this->sessionCartItems;
        $data['mdsPaymentType'] = 'sale';
        if (empty($data['cartItems'])) {
            return redirect()->to(generateUrl('cart'));
        }
        //check shipping status
        if ($this->productSettings->marketplace_shipping != 1) {
            return redirect()->to(generateUrl('cart'));
        }
        //check guest checkout
        if (empty(authCheck()) && $this->generalSettings->guest_checkout != 1) {
            return redirect()->to(generateUrl('cart'));
        }
        //check auth for digital products
        if (!authCheck() && $this->cartModel->checkCartHasDigitalProduct() == true) {
            setErrorMessage(trans("msg_digital_product_register_error"));
            return redirect()->to(generateUrl('register'));
        }
        //check physical products
        if ($this->cartModel->checkCartHasPhysicalProduct() == false) {
            return redirect()->to(generateUrl('cart'));
        }
        $data['cartTotal'] = $this->cartModel->getSessCartTotal();
        if ($data['cartTotal']->is_stock_available != 1) {
            return redirect()->to(generateUrl('cart'));
        }
        $state_id = 0;
        if (authCheck()) {
            $data['shippingAddresses'] = $profileModel->getShippingAddresses(user()->id);
            $firstId = 0;
            if (!empty($data['shippingAddresses']) && !empty($data['shippingAddresses'][0])) {
                $firstId = $data['shippingAddresses'][0]->id;
            }
            $data['selectedShippingAddressId'] = $firstId;
            $data['selectedBillingAddressId'] = $firstId;
            $data['selectedSameAddressForBilling'] = 1;
            if (!empty($data['shippingAddresses'][0]->state_id)) {
                $stateId = $data['shippingAddresses'][0]->state_id;
            }
            if (!empty(helperGetSession('mds_cart_shipping'))) {
                $selectedShipping = helperGetSession('mds_cart_shipping');
                if (!empty($selectedShipping->user_id) && $selectedShipping->user_id == user()->id) {
                    if (!empty($selectedShipping->shipping_address_id)) {
                        $data['selectedShippingAddressId'] = $selectedShipping->shipping_address_id;
                    }
                    if (!empty($selectedShipping->billing_address_id)) {
                        $data['selectedBillingAddressId'] = $selectedShipping->billing_address_id;
                    }
                    if (!empty($selectedShipping->use_same_address_for_billing)) {
                        $data['selectedSameAddressForBilling'] = $selectedShipping->use_same_address_for_billing;
                    }
                    $selectedAddress = $profileModel->getShippingAddressById($data['selectedShippingAddressId'], user()->id);
                    if (!empty($selectedAddress)) {
                        $stateId = $selectedAddress->state_id;
                    }
                }
            }
        } else {
            $mdsCartShipping = helperGetSession('mds_cart_shipping');
            if (!empty($mdsCartShipping)) {
                if (!empty($mdsCartShipping->guest_shipping_address) && countItems($mdsCartShipping->guest_shipping_address) > 0) {
                    if (!empty($mdsCartShipping->guest_shipping_address['state_id'])) {
                        $stateId = $mdsCartShipping->guest_shipping_address['state_id'];
                    }
                }
            }
        }
        if (!empty($stateId)) {
            $data['shippingMethods'] = $shippingModel->getSellerShippingMethodsArray($data['cartItems'], $stateId);
        }
        $data['selectedShippingMethodIds'] = array();
        if (!empty(helperGetSession('mds_selectedShippingMethodIds'))) {
            $data['selectedShippingMethodIds'] = helperGetSession('mds_selectedShippingMethodIds');
        }
        //cart seller ids
        $data['cartSellerIds'] = null;
        if (!empty(helperGetSession('mds_array_cart_seller_ids'))) {
            $data['cartSellerIds'] = helperGetSession('mds_array_cart_seller_ids');
        }
        echo view('partials/_header', $data);
        if (authCheck()) {
            echo view('cart/shipping_information', $data);
        } else {
            echo view('cart/shipping_information_guest', $data);
        }
        echo view('partials/_footer');
    }

    /**
     * Shipping Post
     */
    public function shippingPost()
    {
        $shippingModel = new ShippingModel();
        $result = $shippingModel->calculateCartShippingTotalCost();
        if (!empty($result) && $result['is_valid'] != 1) {
            setErrorMessage(trans("msg_error"));
            return redirect()->back();
        }
        $cartTotal = $this->cartModel->getSessCartTotal();
        if (!empty($cartTotal)) {
            $cartTotal->shipping_cost = $result['total_cost'];
            helperSetSession('mds_shopping_cart_total', $cartTotal);
            $this->cartModel->setShippingAddress($result['total_cost']);
        }
        return redirect()->to(generateUrl('cart', 'payment_method'));
    }

    /**
     * Payment Method
     */
    public function paymentMethod()
    {
        $data['title'] = trans("shopping_cart");
        $data['description'] = trans("shopping_cart") . ' - ' . $this->baseVars->appName;
        $data['keywords'] = trans("shopping_cart") . ',' . $this->baseVars->appName;
        $paymentType = inputGet('payment_type');
        if ($paymentType != 'membership' && $paymentType != 'promote') {
            $paymentType = 'sale';
        }
        if ($paymentType == 'sale') {
            $this->cartModel->validateCart();
            $data['vendorCashOnDelivery'] = 1;
            //sale payment
            $data['cartItems'] = $this->sessionCartItems;
            if (!empty($data['cartItems'])) {
                foreach ($data['cartItems'] as $item) {
                    $vendor = getUser($item->seller_id);
                    if (!empty($vendor)) {
                        if ($vendor->cash_on_delivery != 1) {
                            $data['vendorCashOnDelivery'] = 0;
                        }
                    }
                }
            }
            $data['mdsPaymentType'] = 'sale';
            if ($data['cartItems'] == null) {
                return redirect()->to(generateUrl('cart'));
            }
            //check auth for digital products
            if (!authCheck() && $this->cartModel->checkCartHasDigitalProduct() == true) {
                setErrorMessage(trans("msg_digital_product_register_error"));
                return redirect()->to(generateUrl('register'));
            }
            $data['cartTotal'] = $this->cartModel->getSessCartTotal();
            $userId = null;
            if (authCheck()) {
                $userId = user()->id;
            }
            $data['cartHasPhysicalProduct'] = $this->cartModel->checkCartHasPhysicalProduct();
            $data['cartHasDigitalProduct'] = $this->cartModel->checkCartHasDigitalProduct();
            $this->cartModel->unsetSessCartPaymentMethod();
            $data['showShippingCost'] = 1;
        } elseif ($paymentType == 'membership') {
            //membership payment
            if ($this->generalSettings->membership_plans_system != 1) {
                return redirect()->to(langBaseUrl());
            }
            $data['mdsPaymentType'] = 'membership';
            $planId = helperGetSession('modesy_selected_membership_plan_id');
            if (empty($planId)) {
                return redirect()->to(langBaseUrl());
            }
            $membershipModel = new MembershipModel();
            $data['plan'] = $membershipModel->getPlan($planId);
            if (empty($data['plan'])) {
                return redirect()->to(langBaseUrl());
            }
        } elseif ($paymentType == 'promote') {
            //promote payment
            if ($this->generalSettings->promoted_products != 1) {
                return redirect()->to(langBaseUrl());
            }
            $data['mdsPaymentType'] = 'promote';
            $data['promotedPlan'] = helperGetSession('modesy_selected_promoted_plan');
            if (empty($data['promotedPlan'])) {
                return redirect()->to(langBaseUrl());
            }
        }

        echo view('partials/_header', $data);
        echo view('cart/payment_method', $data);
        echo view('partials/_footer');
    }

    /**
     * Payment Method Post
     */
    public function paymentMethodPost()
    {
        $mdsPaymentType = inputPost('mds_payment_type');
        //validate payment method
        $arrayMethods = array();
        $gateways = getActivePaymentGateways();
        if (!empty($gateways)) {
            foreach ($gateways as $gateway) {
                array_push($arrayMethods, esc($gateway->name_key));
            }
        }
        if ($this->paymentSettings->bank_transfer_enabled) {
            array_push($arrayMethods, 'bank_transfer');
        }
        //check vendor enabled cash on delivery
        $vendorCashOnDelivery = 1;
        $cartItems = $this->sessionCartItems;
        if (!empty($cartItems)) {
            foreach ($cartItems as $item) {
                $vendor = getUser($item->seller_id);
                if (!empty($vendor)) {
                    if ($vendor->cash_on_delivery != 1) {
                        $vendorCashOnDelivery = 0;
                    }
                }
            }
        }
        if ($this->paymentSettings->cash_on_delivery_enabled && $mdsPaymentType == 'sale' && $vendorCashOnDelivery == 1 && empty($this->cartModel->checkCartHasDigitalProduct())) {
            array_push($arrayMethods, 'cash_on_delivery');
        }
        $paymentOption = inputPost('payment_option');
        if (!in_array($paymentOption, $arrayMethods)) {
            setErrorMessage(trans("msg_error"));
            return redirect()->to(generateUrl('cart', 'payment_method'));
        }
        $this->cartModel->setSessCartPaymentMethod();
        $redirect = langBaseUrl();
        if ($mdsPaymentType == 'sale') {
            $redirect = generateUrl('cart', 'payment');
        } elseif ($mdsPaymentType == 'membership') {
            $transactionNumber = 'bank-' . generateToken();
            helperSetSession('mds_membership_bank_transaction_number', $transactionNumber);
            $redirect = generateUrl('cart', 'payment') . '?payment_type=membership';
        } elseif ($mdsPaymentType == 'promote') {
            $transactionNumber = 'bank-' . generateToken();
            helperSetSession('mds_promote_bank_transaction_number', $transactionNumber);
            $redirect = generateUrl('cart', 'payment') . '?payment_type=promote';
        }
        return redirect()->to($redirect);
    }

    /**
     * Payment
     */
    public function payment()
    {
        $data['title'] = trans("shopping_cart");
        $data['description'] = trans("shopping_cart") . ' - ' . $this->baseVars->appName;
        $data['keywords'] = trans("shopping_cart") . ',' . $this->baseVars->appName;
        $data['mdsPaymentType'] = 'sale';
        
        //check guest checkout
        if (empty(authCheck()) && $this->generalSettings->guest_checkout != 1) {
            return redirect()->to(generateUrl('cart'));
        }
        //check is set cart payment method
        $data['cartPaymentMethod'] = $this->cartModel->getSessCartPaymentMethod();
        if (empty($data['cartPaymentMethod'])) {
            return redirect()->to(generateUrl('cart', 'payment_method'));
        }
        $paymentType = inputGet('payment_type');
        if ($paymentType != 'membership' && $paymentType != 'promote') {
            $paymentType = 'sale';
        }
        if ($paymentType == 'sale') {
            $this->cartModel->validateCart();
            //sale payment
            $data['cartItems'] = $this->cartModel->getSessCartItems();
            if ($data['cartItems'] == null) {
                return redirect()->to(generateUrl('cart'));
            }
            $data['cartTotal'] = $this->cartModel->getSessCartTotal();
            $data['cartHasPhysicalProduct'] = $this->cartModel->checkCartHasPhysicalProduct();

            $objAmount = $this->cartModel->convertCurrencyByPaymentGateway($data['cartTotal']->total, 'sale');
            $data['totalAmount'] = $objAmount->total;
            $data['currency'] = $objAmount->currency;
            if (filter_var($data['totalAmount'], FILTER_VALIDATE_INT) === false) {
                $data['totalAmount'] = number_format($data['totalAmount'], 2, '.', '');
            }
            //set payment session
            if (!empty($data['cartItems'])) {
                helperSetSession('mds_shopping_cart_final', $data['cartItems']);
            }
            if (!empty($data['cartTotal'])) {
                helperSetSession('mds_shopping_cart_total_final', $data['cartTotal']);
            }
            $data['showShippingCost'] = 1;
        } elseif ($paymentType == 'membership') {
            //membership payment
            if ($this->generalSettings->membership_plans_system != 1) {
                return redirect()->to(langBaseUrl());
            }
            $data['mdsPaymentType'] = 'membership';
            $planId = helperGetSession('modesy_selected_membership_plan_id');
            if (empty($planId)) {
                return redirect()->to(langBaseUrl());
            }
            $membershipModel = new MembershipModel();
            $data['plan'] = $membershipModel->getPlan($planId);
            if (empty($data['plan'])) {
                return redirect()->to(langBaseUrl());
            }
            //total amount
            $price = $data['plan']->price;
            if ($this->paymentSettings->currency_converter != 1) {
                $price = getPrice($price, 'decimal');
            }
            $objAmount = $this->cartModel->convertCurrencyByPaymentGateway($price, 'membership');
            $data['totalAmount'] = $objAmount->total;
            $data['currency'] = $objAmount->currency;
            $data['transactionNumber'] = helperGetSession('mds_membership_bank_transaction_number');
            $data['cartTotal'] = null;
        } elseif ($paymentType == 'promote') {
            //promote payment
            if ($this->generalSettings->promoted_products != 1) {
                return redirect()->to(langBaseUrl());
            }
            $data['mdsPaymentType'] = 'promote';
            $data['promotedPlan'] = helperGetSession('modesy_selected_promoted_plan');
            if (empty($data['promotedPlan'])) {
                return redirect()->to(langBaseUrl());
            }
            //total amount
            $objAmount = $this->cartModel->convertCurrencyByPaymentGateway($data['promotedPlan']->total_amount, 'promote');
            $data['totalAmount'] = $objAmount->total;
            $data['currency'] = $objAmount->currency;
            $data['transactionNumber'] = helperGetSession('mds_promote_bank_transaction_number');
            $data['cartTotal'] = null;
        }

        echo view('partials/_header', $data);
        echo view('cart/payment', $data);
        echo view('partials/_footer');
    }

    /**
     * Payment with Paypal
     */
    public function paypalPaymentPost()
    {
        $paypal = getPaymentGateway('paypal');
        if (empty($paypal)) {
            setErrorMessage("Payment method not found!");
            echo json_encode([
                'result' => 0
            ]);
            exit();
        }
        $paymentId = inputPost('payment_id');
        loadLibrary('Paypal');
        $paypalLib = new \Paypal($paypal);
        //validate the order
        if ($paypalLib->getOrder($paymentId)) {
            $dataTransaction = [
                'payment_method' => 'PayPal',
                'payment_id' => $paymentId,
                'currency' => inputPost('currency'),
                'payment_amount' => inputPost('payment_amount'),
                'payment_status' => inputPost('payment_status'),
            ];
            $mdsPaymentType = inputPost('mds_payment_type');
            //add order
            $response = $this->executePayment($dataTransaction, $mdsPaymentType, langBaseUrl());
            if ($response->result == 1) {
                setSuccessMessage($response->message);
                echo json_encode([
                    'result' => 1,
                    'redirectUrl' => $response->redirectUrl
                ]);
            } else {
                setErrorMessage($response->message);
                echo json_encode([
                    'result' => 0
                ]);
            }
        } else {
            setErrorMessage(trans("msg_error"));
            echo json_encode([
                'result' => 0
            ]);
        }
    }

    /**
     * Payment with Stripe
     */
    public function stripePaymentPost()
    {
        $stripe = getPaymentGateway('stripe');
        if (empty($stripe)) {
            setErrorMessage("Payment method not found!");
            echo json_encode([
                'result' => 0
            ]);
            exit();
        }
        $paymentSession = helperGetSession('mds_payment_cart_data');
        if (empty($paymentSession)) {
            setErrorMessage(trans("invalid_attempt"));
            echo json_encode([
                'result' => 0
            ]);
            exit();
        }
        $paymentObject = inputPost('paymentObject', true);
        if (!empty($paymentObject)) {
            $paymentObject = json_decode($paymentObject);
        }
        $clientSecret = helperGetSession('mds_stripe_client_secret');
        if (!empty($paymentObject) && $paymentObject->client_secret == $clientSecret) {
            $dataTransaction = [
                'payment_method' => $stripe->name,
                'payment_id' => $paymentObject->id,
                'currency' => strtoupper($paymentObject->currency ?? ''),
                'payment_amount' => getPrice($paymentObject->amount, 'decimal'),
                'payment_status' => 'Succeeded'
            ];
            //add order
            $response = $this->executePayment($dataTransaction, $paymentSession->payment_type, langBaseUrl());
            if ($response->result == 1) {
                setSuccessMessage($response->message);
                echo json_encode([
                    'result' => 1,
                    'redirectUrl' => $response->redirectUrl
                ]);
            } else {
                setErrorMessage($response->message);
                echo json_encode([
                    'result' => 0
                ]);
            }
        } else {
            setErrorMessage(trans("msg_error"));
            echo json_encode([
                'result' => 0
            ]);
        }
        helperDeleteSession('mds_stripe_client_secret');
    }

    /**
     * Payment with PayStack
     */
    public function paystackPaymentPost()
    {
        $paystack = getPaymentGateway('paystack');
        if (empty($paystack)) {
            setErrorMessage("Payment method not found!");
            echo json_encode([
                'result' => 0
            ]);
            exit();
        }
        loadLibrary('Paystack');
        $paystackLib = new \Paystack($paystack);
        $dataTransaction = [
            'payment_method' => 'PayStack',
            'payment_id' => inputPost('payment_id'),
            'currency' => inputPost('currency'),
            'payment_amount' => getPrice(inputPost('payment_amount'), 'decimal'),
            'payment_status' => inputPost('payment_status'),
        ];
        if (empty($paystackLib->verifyTransaction($dataTransaction['payment_id']))) {
            setErrorMessage('Invalid transaction code!');
            echo json_encode([
                'result' => 0
            ]);
        } else {
            $mdsPaymentType = inputPost('mds_payment_type');
            //add order
            $response = $this->executePayment($dataTransaction, $mdsPaymentType, langBaseUrl());
            if ($response->result == 1) {
                setSuccessMessage($response->message);
                echo json_encode([
                    'result' => 1,
                    'redirectUrl' => $response->redirectUrl
                ]);
            } else {
                setErrorMessage($response->message);
                echo json_encode([
                    'result' => 0
                ]);
            }
        }
    }

    /**
     * Payment with Razorpay
     */
    public function razorpayPaymentPost()
    {
        $razorpay = getPaymentGateway('razorpay');
        if (empty($razorpay)) {
            setErrorMessage("Payment method not found!");
            echo json_encode([
                'result' => 0
            ]);
            exit();
        }
        loadLibrary('Razorpay');
        $razorpayLib = new \Razorpay($razorpay);
        $dataTransaction = [
            'payment_method' => 'Razorpay',
            'payment_id' => inputPost('payment_id'),
            'razorpay_order_id' => inputPost('razorpay_order_id'),
            'razorpay_signature' => inputPost('razorpay_signature'),
            'currency' => inputPost('currency'),
            'payment_amount' => getPrice(inputPost('payment_amount'), 'decimal'),
            'payment_status' => 'Succeeded',
        ];
        if (empty($razorpayLib->verifyPaymentSignature($dataTransaction))) {
            setErrorMessage('Invalid signature passed!');
            echo json_encode([
                'result' => 0
            ]);
        } else {
            $mdsPaymentType = inputPost('mds_payment_type');
            //add order
            $response = $this->executePayment($dataTransaction, $mdsPaymentType, langBaseUrl());
            if ($response->result == 1) {
                setSuccessMessage($response->message);
                echo json_encode([
                    'result' => 1,
                    'redirectUrl' => $response->redirectUrl
                ]);
            } else {
                setErrorMessage($response->message);
                echo json_encode([
                    'result' => 0
                ]);
            }
        }
    }

    /**
     * Payment with Flutterwave
     */
    public function flutterwavePaymentPost()
    {
        $flutterwave = getPaymentGateway('flutterwave');
        if (empty($flutterwave)) {
            setErrorMessage("Payment method not found!");
            $this->redirectBackToPayment();
        }
        $paymentSession = helperGetSession('mds_payment_cart_data');
        if (empty($paymentSession)) {
            setErrorMessage(trans("invalid_attempt"));
            $this->redirectBackToPayment();
        }
        $transactionId = inputGet('transaction_id');
        $txRef = inputGet('tx_ref');
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.flutterwave.com/v3/transactions/' . $transactionId . '/verify',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . $flutterwave->secret_key
            ),
        ));
        $curlResponse = curl_exec($curl);
        curl_close($curl);
        $responseObj = json_decode($curlResponse);
        if (!empty($responseObj) && isset($responseObj->status) && $responseObj->status == 'success' && $paymentSession->mds_payment_token == $txRef) {
            $dataTransaction = [
                'payment_method' => $flutterwave->name,
                'payment_id' => $transactionId,
                'currency' => isset($responseObj->data->currency) ? $responseObj->data->currency : 'unset',
                'payment_amount' => isset($responseObj->data->amount) ? $responseObj->data->amount : 0,
                'payment_status' => 'Succeeded'
            ];
            //add order
            $response = $this->executePayment($dataTransaction, $paymentSession->payment_type, langBaseUrl());
            if ($response->result == 1) {
                setSuccessMessage($response->message);
            } else {
                setErrorMessage($response->message);
            }
            return redirect()->to($response->redirectUrl);
        } else {
            setErrorMessage(trans("msg_error"));
            $this->redirectBackToPayment();
        }
    }

    /**
     * Payment with Iyzico
     */
    public function iyzicoPaymentPost()
    {
        $token = inputGet('token');
        $paymentType = inputGet('payment_type');
        $conversationId = inputGet('conversation_id');
        $lang = inputGet('lang');
        $langBaseUrl = langBaseUrl();
        if ($lang != $this->activeLang->short_form) {
            $langBaseUrl = base_url() . '/' . $lang . '/';
        }
        $iyzico = getPaymentGateway('iyzico');
        if (empty($iyzico)) {
            setErrorMessage("Payment method not found!");
            $this->redirectBackToPayment($langBaseUrl);
        }
        $paymentSession = helperGetSession('mds_payment_cart_data');
        if (empty($paymentSession) || empty($paymentSession->mds_payment_token) || inputGet('mds_token') != $paymentSession->mds_payment_token) {
            setErrorMessage(trans("invalid_attempt"));
            $this->redirectBackToPayment($langBaseUrl);
        }
        require_once APPPATH . 'ThirdParty/iyzipay/vendor/autoload.php';
        require_once APPPATH . 'ThirdParty/iyzipay/vendor/iyzico/iyzipay-php/IyzipayBootstrap.php';
        \IyzipayBootstrap::init();
        $options = new \Iyzipay\Options();
        $options->setApiKey($iyzico->public_key);
        $options->setSecretKey($iyzico->secret_key);
        if ($iyzico->environment == 'sandbox') {
            $options->setBaseUrl('https://sandbox-api.iyzipay.com');
        } else {
            $options->setBaseUrl('https://api.iyzipay.com');
        }
        $request = new \Iyzipay\Request\RetrieveCheckoutFormRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId($conversationId);
        $request->setToken($token);
        $checkoutForm = \Iyzipay\Model\CheckoutForm::retrieve($request, $options);
        if ($checkoutForm->getPaymentStatus() == 'SUCCESS') {
            $paymentId = $checkoutForm->getPaymentId();
            $dataTransaction = [
                'payment_method' => 'Iyzico',
                'payment_id' => $checkoutForm->getPaymentId(),
                'currency' => $checkoutForm->getCurrency(),
                'payment_amount' => $checkoutForm->getPrice(),
                'payment_status' => 'Succeeded'
            ];
            //add order
            $response = $this->executePayment($dataTransaction, $paymentType, $langBaseUrl);
            if ($response->result == 1) {
                setSuccessMessage($response->message);
            } else {
                setErrorMessage($response->message);
            }
            return redirect()->to($response->redirectUrl);
        } else {
            setErrorMessage(trans("msg_error"));
            $this->redirectBackToPayment($langBaseUrl);
        }
    }

    /**
     * Payment with Midtrans
     */
    public function midtransPaymentPost()
    {
        $midtrans = getPaymentGateway('midtrans');
        if (empty($midtrans)) {
            setErrorMessage("Payment method not found!");
            echo json_encode([
                'result' => 0
            ]);
            exit();
        }
        $paymentSession = helperGetSession('mds_payment_cart_data');
        if (empty($paymentSession)) {
            setErrorMessage(trans("invalid_attempt"));
            echo json_encode([
                'result' => 0
            ]);
            exit();
        }
        $transactionId = inputPost('transaction_id');
        $curl = curl_init();
        $curlURL = 'https://api.sandbox.midtrans.com/v2/' . $transactionId . '/status';
        if ($midtrans->environment == 'production') {
            $curlURL = 'https://api.midtrans.com/v2/' . $transactionId . '/status';
        }
        curl_setopt_array($curl, array(
            CURLOPT_URL => $curlURL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "Accept: application/json",
                "Content-Type: application/json",
                "Authorization: Basic " . base64_encode($midtrans->secret_key)
            ],
        ));
        $curlResponse = curl_exec($curl);
        curl_close($curl);
        $responseObj = json_decode($curlResponse);
        if (!empty($responseObj) && $responseObj->status_code == 200 && $responseObj->order_id == $paymentSession->mds_payment_token) {
            $dataTransaction = [
                'payment_method' => $midtrans->name,
                'payment_id' => $transactionId,
                'currency' => 'IDR',
                'payment_amount' => isset($responseObj->gross_amount) ? $responseObj->gross_amount : 0,
                'payment_status' => 'Succeeded'
            ];
            //add order
            $response = $this->executePayment($dataTransaction, $paymentSession->payment_type, langBaseUrl());
            if ($response->result == 1) {
                setSuccessMessage($response->message);
                echo json_encode([
                    'result' => 1,
                    'redirectUrl' => $response->redirectUrl
                ]);
            } else {
                setErrorMessage($response->message);
                echo json_encode([
                    'result' => 0
                ]);
            }
        } else {
            setErrorMessage(trans("msg_error"));
            echo json_encode([
                'result' => 0
            ]);
        }
    }

    /**
     * Payment with Mercado Pago
     */
    public function mercadoPagoPaymentPost()
    {
        $lang = inputGet('mds_lang');
        $langBaseUrl = langBaseUrl();
        if ($lang != $this->activeLang->short_form) {
            $langBaseUrl = base_url() . '/' . $lang . '/';
        }
        $mercadoPago = getPaymentGateway('mercado_pago');
        if (empty($mercadoPago)) {
            setErrorMessage("Payment method not found!");
            $this->redirectBackToPayment($langBaseUrl);
        }
        $paymentSession = helperGetSession('mds_payment_cart_data');
        if (empty($paymentSession)) {
            setErrorMessage(trans("invalid_attempt"));
            $this->redirectBackToPayment($langBaseUrl);
        }
        require_once APPPATH . 'ThirdParty/mercado-pago/vendor/autoload.php';
        \MercadoPago\SDK::setAccessToken($mercadoPago->secret_key);
        $mdsSessId = inputGet('mds_sess_id');
        $paymentId = inputGet('payment_id');
        if (!empty($mdsSessId) && !empty($paymentId) && ($mdsSessId == $paymentSession->mds_payment_token)) {
            $payment = \MercadoPago\Payment::find_by_id($paymentId);
            if (!empty($payment) && $payment->status == 'approved' && $payment->transaction_amount >= $paymentSession->total_amount) {
                $dataTransaction = [
                    'payment_method' => 'Mercado Pago',
                    'payment_id' => $paymentId,
                    'currency' => $paymentSession->currency,
                    'payment_amount' => $payment->transaction_amount,
                    'payment_status' => 'Succeeded'
                ];
                //add order
                $response = $this->executePayment($dataTransaction, $paymentSession->payment_type, $langBaseUrl);
                if ($response->result == 1) {
                    setSuccessMessage($response->message);
                } else {
                    setErrorMessage($response->message);
                }
                return redirect()->to($response->redirectUrl);
            }
        }
        setErrorMessage(trans("msg_error"));
        $this->redirectBackToPayment($langBaseUrl);
    }

    /**
     * Execute Sale Payment
     */
    public function executePayment($dataTransaction, $paymentType, $baseUrl)
    {
        //response object
        $response = new \stdClass();
        $response->result = 0;
        $response->message = '';
        $response->redirectUrl = '';
        $baseUrl = $baseUrl . '/';
        $dataTransaction['paymentStatus'] = 'payment_received';

        //check if valid transaction
        if (!$this->orderModel->isValidTransaction('sale', $dataTransaction['payment_id'], $dataTransaction['payment_method'])) {
            $params = '';
            if ($paymentType == 'membership') {
                $params = '?payment_type=membership';
            }
            if ($paymentType == 'promote') {
                $params = '?payment_type=promote';
            }
            $response->message = 'Invalid transaction Id!';
            $response->result = 0;
            $response->redirectUrl = $baseUrl . getRoute('cart', true) . getRoute('payment') . $params;
            return $response;
        }
        if ($paymentType == 'sale') {
            //add order
            $orderId = $this->orderModel->addOrder($dataTransaction, false, null);
            $order = $this->orderModel->getOrder($orderId);
            if (!empty($order)) {
                //decrease product quantity after sale
                $this->orderModel->decreaseProductStockAfterSale($order->id);
                //set response and redirect URLs
                $response->result = 1;
                $response->redirectUrl = $baseUrl . getRoute('order_details', true) . $order->order_number;
                if ($order->buyer_id == 0) {
                    helperSetSession('mds_show_order_completed_page', 1);
                    $response->redirectUrl = $baseUrl . getRoute('order_completed', true) . $order->order_number;
                } else {
                    $response->message = trans("msg_order_completed");
                }
            } else {
                //could not added to the database
                $response->message = trans("msg_payment_database_error");
                $response->result = 0;
                $response->redirectUrl = $baseUrl . getRoute('cart', true) . getRoute('payment');
            }
        } elseif ($paymentType == 'membership') {
            $planId = helperGetSession('modesy_selected_membership_plan_id');
            $plan = null;
            if (!empty($planId)) {
                $plan = $this->membershipModel->getPlan($planId);
            }
            if (!empty($plan)) {
                //add user membership plan
                $this->membershipModel->addUserPlan($dataTransaction, $plan, user()->id);
                //add transaction
                $this->membershipModel->addMembershipTransaction($dataTransaction, $plan);
                //set response and redirect URLs
                $response->result = 1;
                $response->redirectUrl = $baseUrl . getRoute('membership_payment_completed') . '?method=gtw';
            } else {
                //could not added to the database
                $response->message = trans("msg_payment_database_error");
                $response->result = 0;
                $response->redirectUrl = $baseUrl . getRoute('cart', true) . getRoute('payment') . '?payment_type=membership';
            }
        } elseif ($paymentType == 'promote') {
            $promotedPlan = helperGetSession('modesy_selected_promoted_plan');
            if (!empty($promotedPlan)) {
                $promoteModel = new PromoteModel();
                //add to promoted products
                $promoteModel->addToPromotedProducts($promotedPlan);
                //add transaction
                $promoteModel->addPromoteTransaction($dataTransaction);
                //reset cache
                resetCacheDataOnChange();
                //set response and redirect URLs
                $response->result = 1;
                $response->redirectUrl = $baseUrl . getRoute('promote_payment_completed') . '?method=gtw&product_id=' . $promotedPlan->product_id;
            } else {
                //could not added to the database
                $response->message = trans("msg_payment_database_error");
                $response->result = 0;
                $response->redirectUrl = $baseUrl . getRoute('cart', true) . getRoute('payment') . '?payment_type=promote';
            }
        }
        //reset session for the payment
        helperDeleteSession('mds_payment_cart_data');
        //return response
        return $response;
    }

    /**
     * Payment with Bank Transfer
     */
    public function bankTransferPaymentPost()
    {
        $mdsPaymentType = inputPost('mds_payment_type');
        if ($mdsPaymentType == 'membership') {
            $planId = helperGetSession('modesy_selected_membership_plan_id');
            $plan = null;
            if (!empty($planId)) {
                $plan = $this->membershipModel->getPlan($planId);
            }
            if (!empty($plan)) {
                $dataTransaction = [
                    'payment_method' => 'Bank Transfer',
                    'payment_status' => 'awaiting_payment',
                    'payment_id' => helperGetSession('mds_membership_bank_transaction_number')
                ];
                //add user membership plan
                $this->membershipModel->addUserPlan($dataTransaction, $plan, user()->id);
                //add transaction
                $this->membershipModel->addMembershipTransactionBank($dataTransaction, $plan);
                return redirect()->to(generateUrl('membership_payment_completed') . '?method=bank_transfer&transaction_number=' . $dataTransaction['payment_id']);
            }
            setErrorMessage(trans("msg_error"));
            return redirect()->to(generateUrl('cart', 'payment') . '?payment_type=membership');
        } elseif ($mdsPaymentType == 'promote') {
            $promotedPlan = helperGetSession('modesy_selected_promoted_plan');
            if (!empty($promotedPlan)) {
                $transactionNumber = helperGetSession('mds_promote_bank_transaction_number');
                //add transaction
                $promoteModel = new PromoteModel();
                $promoteModel->addPromoteTransactionBank($promotedPlan, $transactionNumber);
                $type = helperGetSession('mds_promote_product_type');
                if (empty($type)) {
                    $type = 'new';
                }
                return redirect()->to(generateUrl('promote_payment_completed') . '?method=bank_transfer&transaction_number=' . $transactionNumber . '&product_id=' . $promotedPlan->product_id);
            }
            setErrorMessage(trans("msg_error"));
            return redirect()->to(generateUrl('cart', 'payment') . '?payment_type=promote');
        } else {
            //add order
            $orderId = $this->orderModel->addOrder(null, true, 'Bank Transfer');
            if (!empty($orderId)) {
                $order = $this->orderModel->getOrder($orderId);
                if (!empty($order)) {
                    //decrease product quantity after sale
                    $this->orderModel->decreaseProductStockAfterSale($order->id);
                    if ($order->buyer_id == 0) {
                        helperSetSession('mds_show_order_completed_page', 1);
                        return redirect()->to(generateUrl('order_completed') . '/' . $order->order_number);
                    } else {
                        setSuccessMessage(trans("msg_order_completed"));
                        return redirect()->to(generateUrl('order_details') . '/' . $order->order_number);
                    }
                }
            }
            setErrorMessage(trans("msg_error"));
            return redirect()->to(generateUrl('cart', 'payment'));
        }
    }

    /**
     * Cash on Delivery
     */
    public function cashOnDeliveryPaymentPost()
    {
        //add order
        $orderId = $this->orderModel->addOrder(null, true, 'Cash On Delivery');
        if (!empty($orderId)) {
            $order = $this->orderModel->getOrder($orderId);
            if (!empty($order)) {
                //decrease product quantity after sale
                $this->orderModel->decreaseProductStockAfterSale($order->id);
                if ($order->buyer_id == 0) {
                    helperSetSession('mds_show_order_completed_page', 1);
                    return redirect()->to(generateUrl('order_completed') . '/' . $order->order_number);
                } else {
                    setSuccessMessage(trans("msg_order_completed"));
                    return redirect()->to(generateUrl('order_details') . '/' . $order->order_number);
                }
            }
        }
        setErrorMessage(trans("msg_error"));
        return redirect()->to(generateUrl('cart', 'payment'));
    }

    /**
     * Order Completed
     */
    public function orderCompleted($orderNumber)
    {
        $data['title'] = trans("msg_order_completed");
        $data['description'] = trans("msg_order_completed") . ' - ' . $this->baseVars->appName;
        $data['keywords'] = trans("msg_order_completed") . ',' . $this->baseVars->appName;
        $data['order'] = $this->orderModel->getOrderByOrderNumber($orderNumber);
        if (empty($data['order'])) {
            return redirect()->to(langBaseUrl());
        }
        if (empty(helperGetSession('mds_show_order_completed_page'))) {
            return redirect()->to(langBaseUrl());
        }
        echo view('partials/_header', $data);
        echo view('cart/order_completed', $data);
        echo view('partials/_footer');
    }

    /**
     * Membership Payment Completed
     */
    public function membershipPaymentCompleted()
    {
        $data['title'] = trans("msg_payment_completed");
        $data['description'] = trans("msg_payment_completed") . ' - ' . $this->baseVars->appName;
        $data['keywords'] = trans("payment") . ',' . $this->baseVars->appName;
        $transactionInsertId = helperGetSession('mds_membership_transaction_insert_id');
        if (empty($transactionInsertId)) {
            return redirect()->to(langBaseUrl());
        }
        $data['transaction'] = $this->membershipModel->getMembershipTransaction($transactionInsertId);
        if (empty($data['transaction'])) {
            return redirect()->to(langBaseUrl());
        }
        $data['method'] = inputGet('method');
        $data['transactionNumber'] = inputGet('transaction_number');

        echo view('partials/_header', $data);
        echo view('cart/membership_payment_completed', $data);
        echo view('partials/_footer');
    }

    /**
     * Promote Payment Completed
     */
    public function promotePaymentCompleted()
    {
        $data['title'] = trans("msg_payment_completed");
        $data['description'] = trans("msg_payment_completed") . ' - ' . $this->baseVars->appName;
        $data['keywords'] = trans("payment") . ',' . $this->baseVars->appName;
        $transactionInsertId = helperGetSession('mds_promoted_transaction_insert_id');
        if (empty($transactionInsertId)) {
            return redirect()->to(langBaseUrl());
        }
        $promoteModel = new PromoteModel();
        $data['transaction'] = $promoteModel->getTransaction($transactionInsertId);
        if (empty($data['transaction'])) {
            return redirect()->to(langBaseUrl());
        }
        $data['method'] = inputGet('method');
        $data['transaction_number'] = inputGet('transaction_number');

        echo view('partials/_header', $data);
        echo view('cart/promote_payment_completed', $data);
        echo view('partials/_footer');
    }

    //get shipping method by location
    public function getShippingMethodsByLocation()
    {
        $data = [
            'result' => 0,
            'htmlContent' => ''
        ];
        $stateId = inputPost('state_id');
        $cartItems = $this->sessionCartItems;
        if (!empty($stateId)) {
            $shippingModel = new ShippingModel();
            $vars = ['shippingMethods' => $shippingModel->getSellerShippingMethodsArray($cartItems, $stateId)];
            $data['result'] = 1;
            $data['htmlContent'] = view('cart/_shipping_methods', $vars);
        }
        echo json_encode($data);
    }

    //redirect back to the cart payment
    public function redirectBackToPayment($baseUrl = null)
    {
        if (empty($baseUrl)) {
            $baseUrl = langBaseUrl();
        }
        redirectToUrl(getRoute('cart', true) . getRoute('payment'));
    }
}

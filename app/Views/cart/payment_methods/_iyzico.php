<?php if (!empty($paymentGateway) && $paymentGateway->name_key == "iyzico"):
    require_once APPPATH . 'ThirdParty/iyzipay/vendor/autoload.php';
    require_once APPPATH . 'ThirdParty/iyzipay/vendor/iyzico/iyzipay-php/IyzipayBootstrap.php';
    IyzipayBootstrap::init();
    $options = new \Iyzipay\Options();
    $options->setApiKey($paymentGateway->public_key);
    $options->setSecretKey($paymentGateway->secret_key);
    if ($paymentGateway->environment == 'sandbox') {
        $options->setBaseUrl('https://sandbox-api.iyzipay.com');
    } else {
        $options->setBaseUrl('https://api.iyzipay.com');
    }
    $conversationId = generateToken();
    $customer = getCartCustomerData();
    if ($mdsPaymentType == 'membership') {
        $itemBasketName = getMembershipPlanName($plan->title_array, selectedLangId());
        $itemBasketCategory = trans("membership_plan_payment");
        $itemBasketPrice = $totalAmount;
        $callbackUrl = base_url() . '/mds-iyzico-payment-callback?payment_type=membership&base_url=' . base_url() . '&lang=' . $activeLang->short_form . '&conversation_id=' . $conversationId . '&mds_token=' . $mdsPaymentToken;
    } elseif ($mdsPaymentType == 'promote') {
        $itemBasketName = $promotedPlan->purchased_plan;
        $itemBasketCategory = trans("promote_plan");
        $itemBasketPrice = $totalAmount;
        $callbackUrl = base_url() . '/mds-iyzico-payment-callback?payment_type=promote&base_url=' . base_url() . '&lang=' . $activeLang->short_form . '&conversation_id=' . $conversationId . '&mds_token=' . $mdsPaymentToken;
    } else {
        $productIds = '';
        $i = 0;
        if (!empty($cartItems)) {
            foreach ($cartItems as $cartItem) {
                if ($i != 0) {
                    $productIds .= ', ';
                }
                $productIds .= $cartItem->product_id;
                $i++;
            }
        }
        $itemBasketName = trans("product") . ' (' . $productIds . ')';
        $itemBasketCategory = trans("sale");
        $itemBasketPrice = $totalAmount;
        $callbackUrl = base_url() . '/mds-iyzico-payment-callback?payment_type=sale&base_url=' . langBaseUrl() . '&lang=' . $activeLang->short_form . '&conversation_id=' . $conversationId . '&mds_token=' . $mdsPaymentToken;
        $country = 'Turkey';
    }
    $buyerId = 'guest_' . uniqid();
    if (authCheck()) {
        $buyerId = user()->id;
    }
    $ip = getIPAddress();
    if (empty($ip)) {
        $ip = '85.34.78.112';
    }
    # create request class
    $request = new \Iyzipay\Request\CreateCheckoutFormInitializeRequest();
    $request->setLocale(\Iyzipay\Model\Locale::TR);
    $request->setConversationId($conversationId);
    $request->setPrice($itemBasketPrice);
    $request->setPaidPrice($itemBasketPrice);
    $request->setCurrency(\Iyzipay\Model\Currency::TL);
    $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
    $request->setCallbackUrl($callbackUrl);
    $request->setEnabledInstallments(array(2, 3, 6, 9));

    $buyer = new \Iyzipay\Model\Buyer();
    $buyer->setId($customer->id);
    $buyer->setName($customer->first_name);
    $buyer->setSurname($customer->last_name);
    $buyer->setGsmNumber($customer->phone_number);
    $buyer->setEmail($customer->email);
    $buyer->setIdentityNumber('11111111111');
    $buyer->setRegistrationAddress('not_set');
    $buyer->setIp($ip);
    $buyer->setCity('not_set');
    $buyer->setCountry('not_set');
    $buyer->setZipCode('not_set');
    $request->setBuyer($buyer);

    $shippingAddress = new \Iyzipay\Model\Address();
    $shippingAddress->setContactName('not_set');
    $shippingAddress->setCity('not_set');
    $shippingAddress->setCountry('not_set');
    $shippingAddress->setAddress('not_set');
    $shippingAddress->setZipCode('');
    $request->setShippingAddress($shippingAddress);

    $billingAddress = new \Iyzipay\Model\Address();
    $billingAddress->setContactName('not_set');
    $billingAddress->setCity('not_set');
    $billingAddress->setCountry('not_set');
    $billingAddress->setAddress('not_set');
    $billingAddress->setZipCode('');
    $request->setBillingAddress($billingAddress);

    $basketItems = array();
    $BasketItem = new \Iyzipay\Model\BasketItem();
    $BasketItem->setId('0');
    $BasketItem->setName($itemBasketName);
    $BasketItem->setCategory1($itemBasketCategory);
    $BasketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
    $BasketItem->setPrice($itemBasketPrice);
    $basketItems[0] = $BasketItem;

    $request->setBasketItems($basketItems);
    # make request
    $checkoutFormInitialize = \Iyzipay\Model\CheckoutFormInitialize::create($request, $options);

    if ($checkoutFormInitialize->getStatus() == 'failure') {
        $this->session->set_flashdata('error', $checkoutFormInitialize->getErrorMessage());
    } else {
        echo $checkoutFormInitialize->getcheckoutFormContent();
    } ?>
    <div class="row">
        <div class="col-12">
            <?= view('partials/_messages'); ?>
        </div>
    </div>
    <div id="iyzipay-checkout-form" class="responsive"></div>
<?php endif;
resetFlashData(); ?>
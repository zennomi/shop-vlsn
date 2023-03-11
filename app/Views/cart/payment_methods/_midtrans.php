<?php if (!empty($paymentGateway) && $paymentGateway->name_key == 'midtrans'):
    require_once APPPATH . 'ThirdParty/midtrans/vendor/autoload.php';
    $showMidtrans = true;
    try {
        \Midtrans\Config::$serverKey = $paymentGateway->secret_key;
        if ($paymentGateway->environment == 'production') {
            \Midtrans\Config::$isProduction = true;
        } else {
            \Midtrans\Config::$isProduction = false;
        }
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $customer = getCartCustomerData();
        $grossAmount = @intval($totalAmount);
        $params = [
            'transaction_details' => [
                'order_id' => $mdsPaymentToken,
                'gross_amount' => $grossAmount
            ],
            'customer_details' => [
                'first_name' => !empty($customer->first_name) ? $customer->first_name : '',
                'last_name' => !empty($customer->last_name) ? $customer->last_name : '',
                'email' => !empty($customer->email) ? $customer->email : '',
                'phone' => !empty($customer->phone_number) ? $customer->phone_number : '',
            ],
        ];
        $snapToken = \Midtrans\Snap::getSnapToken($params);
    } catch (Exception $ex) {
        $showMidtrans = false; ?>
        <div class="alert alert-danger" role="alert">
            There was a problem starting Midtrans! Please make sure you select the correct mode and check your API keys again.
        </div>
    <?php }
    if ($showMidtrans == true):
        if ($paymentGateway->environment == 'production'):?>
            <script type="text/javascript" src="https://app.midtrans.com/snap/snap.js" data-client-key="<?= $paymentGateway->public_key; ?>"></script>
        <?php else: ?>
            <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?= $paymentGateway->public_key; ?>"></script>
        <?php endif; ?>
        <div id="payment-button-container" class="payment-button-cnt">
            <div class="payment-icons-container">
                <label class="payment-icons">
                    <?php $logos = @explode(',', $paymentGateway->logos);
                    if (!empty($logos) && countItems($logos) > 0):
                        foreach ($logos as $logo): ?>
                            <img src="<?= base_url('assets/img/payment/' . esc(trim($logo ?? '')) . '.svg'); ?>" alt="<?= esc(trim($logo ?? '')); ?>">
                        <?php endforeach;
                    endif; ?>
                </label>
            </div>
            <p class="p-complete-payment text-muted"><?php echo trans("msg_complete_payment"); ?></p>
            <button type="button" id="pay-button" class="btn btn-lg btn-payment btn-midtrans"><?= trans("pay"); ?>&nbsp;<?= priceDecimal($grossAmount, $currency); ?></button>
        </div>
        <script type="text/javascript">
            var payButton = document.getElementById('pay-button');
            document.getElementById('pay-button').onclick = function () {
                snap.pay("<?=!empty($snapToken) ? $snapToken : ''; ?>", {
                    enabledPayments: ['credit_card'],
                    onSuccess: function (result) {
                        if (result.status_code == 200) {
                            var dataArray = {
                                'transaction_id': result.transaction_id,
                                'order_id': result.order_id,
                                'currency': '<?= $currency; ?>',
                                'payment_amount': '<?= $grossAmount; ?>',
                                'payment_status': result.transaction_status,
                                'mds_payment_type': '<?= $mdsPaymentType; ?>'
                            };
                            $.ajax({
                                type: 'POST',
                                url: MdsConfig.baseURL + '/midtrans-payment-post',
                                data: setAjaxData(dataArray),
                                success: function (response) {
                                    var obj = JSON.parse(response);
                                    if (obj.result == 1) {
                                        window.location.href = obj.redirectUrl;
                                    } else {
                                        location.reload();
                                    }
                                }
                            });
                        } else {
                            alert(result.status_message);
                        }
                    },
                    onPending: function (result) {
                        alert(result.status_message);
                    },
                    onError: function (result) {
                        alert(result.status_message);
                    }
                });
            };
        </script>
    <?php endif;
endif; ?>
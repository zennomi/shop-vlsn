<?php $totalAmount = $totalAmount * 100;
if (filter_var($totalAmount, FILTER_VALIDATE_INT) === false) {
    $totalAmount = intval($totalAmount);
}
if (!empty($paymentGateway) && $paymentGateway->name_key == 'razorpay'):
    loadLibrary('Razorpay');
    $razorpay = new Razorpay($paymentGateway);
    $array = [
        'receipt' => $mdsPaymentToken,
        'amount' => $totalAmount,
        'currency' => $currency
    ];
    $razorpayOrderId = $razorpay->createOrder($array);
    if (!empty($razorpayOrderId)): ?>
        <div class="row">
            <div class="col-12">
                <?= view('partials/_messages'); ?>
            </div>
        </div>
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
            <p class="p-complete-payment"><?= trans("msg_complete_payment"); ?></p>
            <button type="button" id="rzp-button1" class="btn btn-lg btn-payment btn-razorpay"><?= trans("pay"); ?>&nbsp;<?= priceFormatted($totalAmount, $currency); ?></button>
        </div>
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        <script>
            var options = {
                "key": "<?= $paymentGateway->public_key; ?>",
                "amount": "<?= $totalAmount; ?>",
                "currency": "<?= $currency; ?>",
                "name": "<?= $generalSettings->application_name; ?>",
                "description": "<?= trans("pay"); ?>",
                "image": "<?= getLogoEmail(); ?>",
                "order_id": "<?= $razorpayOrderId; ?>",
                "handler": function (response) {
                    var dataArray = {
                        'payment_id': response.razorpay_payment_id,
                        'razorpay_order_id': response.razorpay_order_id,
                        'razorpay_signature': response.razorpay_signature,
                        'currency': '<?= $currency; ?>',
                        'payment_amount': '<?= $totalAmount; ?>',
                        'payment_status': '',
                        'mds_payment_type': '<?= $mdsPaymentType; ?>'
                    };
                    $.ajax({
                        type: 'POST',
                        url: MdsConfig.baseURL + '/razorpay-payment-post',
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
                },
                "theme": {
                    "color": "#528FF0"
                }
            };
            var rzp1 = new Razorpay(options);
            document.getElementById('rzp-button1').onclick = function (e) {
                rzp1.open();
                e.preventDefault();
            }
        </script>
    <?php endif;
endif; ?>

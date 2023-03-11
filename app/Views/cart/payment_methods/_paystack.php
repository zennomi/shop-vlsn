<?php $totalAmount = $totalAmount * 100;
if (filter_var($totalAmount, FILTER_VALIDATE_INT) === false) {
    $totalAmount = intval($totalAmount);
}
if (!empty($paymentGateway) && $paymentGateway->name_key == 'paystack'):
    $customer = getCartCustomerData(); ?>
    <div class="row">
        <div class="col-12">
            <?= view('partials/_messages'); ?>
        </div>
    </div>
    <form>
        <script src="https://js.paystack.co/v1/inline.js"></script>
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
            <button type="button" class="btn btn-lg btn-payment btn-paystack" onclick="payWithPaystack()"><?= trans("pay"); ?>&nbsp;<?= priceFormatted($totalAmount, $currency); ?></button>
        </div>
    </form>
    <script>
        function payWithPaystack() {
            var handler = PaystackPop.setup({
                key: '<?= $paymentGateway->public_key; ?>',
                email: '<?= !empty($customer) ? $customer->email : ""; ?>',
                amount: '<?= $totalAmount; ?>',
                currency: '<?= $currency; ?>',
                ref: '<?= generateToken(); ?>',
                callback: function (response) {
                    var data = {
                        'payment_id': response.reference,
                        'currency': '<?= $currency; ?>',
                        'payment_amount': '<?= $totalAmount; ?>',
                        'payment_status': response.status,
                        'mds_payment_type': '<?= $mdsPaymentType; ?>'
                    };
                    $.ajax({
                        type: 'POST',
                        url: MdsConfig.baseURL + '/paystack-payment-post',
                        data: setAjaxData(data),
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
            });
            handler.openIframe();
        }
    </script>
<?php endif; ?>

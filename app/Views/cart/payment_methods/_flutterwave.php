<?php if (!empty($paymentGateway) && $paymentGateway->name_key == 'flutterwave'):
    $customer = getCartCustomerData(); ?>
    <form>
        <script src="https://checkout.flutterwave.com/v3.js"></script>
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
            <p class="p-complete-payment text-muted"><?= trans("msg_complete_payment"); ?></p>
            <button type="button" onClick="makePayment()" class="btn btn-lg btn-payment btn-flutterwave"><?= trans("pay"); ?>&nbsp;<?= priceDecimal($totalAmount, $currency); ?></button>
        </div>
    </form>
    <?php $ipAddress = getIPAddress();
    $consumerMac = !empty($ipAddress) ? $ipAddress : uniqid();
    $consumerId = authCheck() ? user()->id : 0; ?>
    <script>
        function makePayment() {
            FlutterwaveCheckout({
                public_key: "<?= $paymentGateway->public_key;?>",
                tx_ref: "<?= $mdsPaymentToken; ?>",
                amount: <?= $totalAmount; ?>,
                currency: "<?= $currency; ?>",
                payment_options: "card, mobilemoneyghana, ussd",
                redirect_url: "<?= base_url('flutterwave-payment-post'); ?>",
                meta: {
                    consumer_id: <?= $consumerId; ?>,
                    consumer_mac: "<?= $consumerMac; ?>",
                },
                customer: {
                    email: "<?= !empty($customer) ? $customer->email : ''; ?>",
                    phone_number: "<?=  !empty($customer) ? $customer->phone_number : ''; ?>",
                    name: "<?=  !empty($customer) ? $customer->first_name . ' ' . $customer->last_name : ''; ?>"
                },
                callback: function (data) {
                },
                onclose: function () {
                },
                customizations: {
                    title: "<?= $generalSettings->application_name; ?>",
                    description: "Payment for items in cart",
                    logo: "<?= getLogo(); ?>",
                },
            });
        }
    </script>
<?php endif; ?>
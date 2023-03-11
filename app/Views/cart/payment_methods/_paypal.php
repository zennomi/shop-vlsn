<?php if (!empty($paymentGateway) && $paymentGateway->name_key == 'paypal'): ?>
    <script src="https://www.paypal.com/sdk/js?client-id=<?= $paymentGateway->public_key; ?>&currency=<?= $currency; ?>"></script>
    <div class="row">
        <div class="col-12">
            <?= view('partials/_messages'); ?>
        </div>
    </div>
    <div id="payment-button-container" class="payment-button-cnt">
        <div id="paypal-button-container" style="max-width: 340px;margin: 0 auto"></div>
        <div class="col-12 paypal-loader hidden">
            <div class="row">
                <div class="spinner">
                    <div class="bounce1"></div>
                    <div class="bounce2"></div>
                    <div class="bounce3"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-center">
                    <strong class="payment-loader-text"><?= trans("processing"); ?></strong>
                </div>
            </div>
        </div>
    </div>
    <?php if (!empty($totalAmount)):
        $price = str_replace('.00', '', $totalAmount);
    endif; ?>
    <script>
        paypal.Buttons({
            createOrder: function (data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '<?= $price; ?>'
                        }
                    }]
                });
            },
            onApprove: function (data, actions) {
                return actions.order.capture().then(function (details) {
                    $('.paypal-loader').show();
                    var dataArray = {
                        'payment_id': data.orderID,
                        'currency': '<?= $currency; ?>',
                        'payment_amount': '<?= $price; ?>',
                        'payment_status': details.status,
                        'mds_payment_type': '<?= $mdsPaymentType; ?>'
                    };
                    $.ajax({
                        type: 'POST',
                        url: MdsConfig.baseURL + '/paypal-payment-post',
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
                });
            },
            onError: function (error) {
                alert(error);
            }
        }).render('#paypal-button-container');
    </script>
<?php endif; ?>
<style>
    .paypal-loader .spinner {
        margin-bottom: 0 !important;
    }

    .payment-loader-text {
        font-size: 13px;
        font-weight: 600;
    }
</style>
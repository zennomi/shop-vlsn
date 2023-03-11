<?php if (!empty($paymentGateway) && $paymentGateway->name_key == 'stripe'):
    require_once APPPATH . 'ThirdParty/stripe/vendor/autoload.php';
    $totalAmount = $totalAmount * 100;
    if (filter_var($totalAmount, FILTER_VALIDATE_INT) === false) {
        $totalAmount = intval($totalAmount);
    }
    //if JPY
    if ($currency == 'JPY') {
        $totalAmount = $totalAmount / 100;
    }
    $showStripe = true;
    try {
        \Stripe\Stripe::setApiKey($paymentGateway->secret_key);
        $intent = \Stripe\PaymentIntent::create([
            'amount' => $totalAmount,
            'currency' => $currency,
        ]);
        $clientSecret = !empty($intent->client_secret) ? $intent->client_secret : '';
        helperSetSession('mds_stripe_client_secret', $clientSecret);
    } catch (Exception $e) {
        $showStripe = false; ?>
        <div class="alert alert-danger" role="alert">
            <?= $e->getMessage(); ?>
        </div>
    <?php } ?>
    <div class="row">
        <div class="col-12">
            <?= view('partials/_messages'); ?>
        </div>
    </div>
    <?php if ($showStripe == true): ?>
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 stripe-checkout">
            <form id="payment-form">
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
                <div class="form-group">
                    <input type="text" name="name" id="sp_input_name" class="form-control shadow-sm" placeholder="<?= trans("full_name"); ?>">
                </div>
                <div class="form-group">
                    <input type="email" name="email" id="sp_input_email" class="form-control shadow-sm" placeholder="<?= trans("email"); ?>">
                </div>
                <div class="form-group">
                    <div id="card-element" class="form-control input-card-element shadow-sm"></div>
                </div>
                <button id="submit" class="btn btn-primary" id="card-button">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <?= trans("pay"); ?>&nbsp;<?= priceFormatted($totalAmount, $currency); ?>
                </button>
            </form>
        </div>
    </div>
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        var stripe = Stripe("<?= $paymentGateway->public_key; ?>", {
            locale: '<?= $activeLang->short_form ?>'
        });
        var elements = stripe.elements();
        var style = {
            base: {
                color: "#32325d",
                lineHeight: '38px',
            },
            invalid: {
                color: "#fa755a",
                iconColor: "#fa755a"
            },
        };
        var card = elements.create("card", {style: style});
        card.mount("#card-element");
        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function (ev) {
            ev.preventDefault();
            var validation = true;
            var buyerName = $("#sp_input_name").val();
            var buyerEmail = $("#sp_input_email").val();
            if (buyerName == null || buyerName.trim() < 2) {
                $("#sp_input_name").addClass("is-invalid");
                return false;
            } else {
                $("#sp_input_name").removeClass("is-invalid");
            }
            if (buyerEmail == null || buyerEmail.trim() < 2) {
                $("#sp_input_email").addClass("is-invalid");
                return false;
            } else {
                $("#sp_input_email").removeClass("is-invalid");
            }
            $('.stripe-checkout #submit').prop("disabled", true);
            $('.stripe-checkout .spinner-border').css('display', 'inline-block');
            var clientSecret = "<?= $clientSecret; ?>";
            stripe.confirmCardPayment(clientSecret, {
                payment_method: {
                    type: 'card',
                    card: card,
                    billing_details: {
                        name: buyerName,
                        email: buyerEmail
                    }
                }
            }).then(function (result) {
                if (result.error) {
                    $('.stripe-checkout #submit').prop("disabled", false);
                    $('.stripe-checkout .spinner-border').css('display', 'none');
                    alert(result.error.message);
                } else {
                    if (result.paymentIntent.status === 'succeeded') {
                        var data = {
                            'paymentObject': JSON.stringify(result.paymentIntent)
                        };
                        $.ajax({
                            type: 'POST',
                            url: MdsConfig.baseURL + '/stripe-payment-post',
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
                    }
                }
            });
        });
        $(document).on("input keyup paste change", "#payment-form input", function () {
            var val = $(this).val();
            if (val == null || val.trim() < 2) {
                $(this).addClass("is-invalid");
            } else {
                $(this).removeClass("is-invalid");
            }
        });
    </script>
<?php endif;
endif; ?>

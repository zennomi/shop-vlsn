<?php $mercadoUrl = '';
if (!empty($paymentGateway) && $paymentGateway->name_key == 'mercado_pago'):
    if ($paymentGateway->base_currency == 'ARS') {
        $mercadoUrl = 'https://www.mercadopago.com.ar/integrations/v1/web-payment-checkout.js';
    } elseif ($paymentGateway->base_currency == 'BRL') {
        $mercadoUrl = 'https://www.mercadopago.com.br/integrations/v1/web-payment-checkout.js';
    } elseif ($paymentGateway->base_currency == 'CLP') {
        $mercadoUrl = 'https://www.mercadopago.cl/integrations/v1/web-payment-checkout.js';
    } elseif ($paymentGateway->base_currency == 'COP') {
        $mercadoUrl = 'https://www.mercadopago.com.co/integrations/v1/web-payment-checkout.js';
    } elseif ($paymentGateway->base_currency == 'MXN') {
        $mercadoUrl = 'https://www.mercadopago.com.mx/integrations/v1/web-payment-checkout.js';
    } elseif ($paymentGateway->base_currency == 'PEN') {
        $mercadoUrl = 'https://www.mercadopago.com.pe/integrations/v1/web-payment-checkout.js';
    } elseif ($paymentGateway->base_currency == 'UYU') {
        $mercadoUrl = 'https://www.mercadopago.com.uy/integrations/v1/web-payment-checkout.js';
    }
    $showPayment = true;
    try {
        require_once APPPATH . 'ThirdParty/mercado-pago/vendor/autoload.php';
        MercadoPago\SDK::setAccessToken($paymentGateway->secret_key);
        $preference = new MercadoPago\Preference();
        $preference->back_urls = [
            'success' => base_url() . '/mercado-pago-payment-post?mds_lang=' . $activeLang->short_form . "&mds_sess_id=" . $mdsPaymentToken,
            'failure' => base_url() . '/mercado-pago-payment-post?mds_lang=' . $activeLang->short_form . "&mds_sess_id=" . $mdsPaymentToken,
            'pending' => base_url() . '/mercado-pago-payment-post?mds_lang=' . $activeLang->short_form . "&mds_sess_id=" . $mdsPaymentToken
        ];
        $preference->auto_return = 'approved';
        //sale title
        $title = '';
        if ($mdsPaymentType == 'membership') {
            $title = trans("membership_plan_payment");
        } elseif ($mdsPaymentType == 'promote') {
            $title = trans("promote_plan");
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
            $title = 'Product (' . $productIds . ')';
        }
        if (empty($title)) {
            $title = trans("sale");
        }
        $item = new MercadoPago\Item();
        $item->title = $title;
        $item->quantity = 1;
        $item->currency_id = $paymentGateway->base_currency;
        $item->unit_price = $totalAmount;
        $preference->items = array($item);
        $preference->save();
    } catch (Exception $ex) {
        $showPayment = false;
    } ?>
    <div class="row">
        <div class="col-12">
            <?= view('partials/_messages'); ?>
        </div>
    </div>
    <?php if ($showPayment): ?>
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
        <script src="<?= $mercadoUrl; ?>" data-preference-id="<?= $preference->id; ?>" data-button-label="<?= trans("pay"); ?>&nbsp;<?= strip_tags(priceDecimal($totalAmount, $currency) ?? ''); ?>"></script>
    </div>
<?php else: ?>
    <div class="alert alert-danger" role="alert">
        There was a problem starting Mercado Pago! Please make sure that you added correct API keys and selected the correct currency.
    </div>
<?php endif;
endif; ?>
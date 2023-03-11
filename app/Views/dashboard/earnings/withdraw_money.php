<div class="row">
    <div class="col-sm-12">
        <?= view('dashboard/includes/_messages'); ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-10">
        <div class="box">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= esc($title); ?></h3>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-12 col-md-7">
                        <div class="withdraw-money-container">
                            <form action="<?= base_url('withdraw-money-post'); ?>" method="post" class="validate_price">
                                <?= csrf_field(); ?>
                                <div class="form-group">
                                    <label><?= trans("withdraw_amount"); ?></label>
                                    <?php $minValue = 0;
                                    if ($paymentSettings->payout_paypal_enabled) {
                                        $minValue = $paymentSettings->min_payout_paypal;
                                    } elseif ($paymentSettings->payout_bitcoin_enabled) {
                                        $minValue = $paymentSettings->min_payout_bitcoin;
                                    } elseif ($paymentSettings->payout_iban_enabled) {
                                        $minValue = $paymentSettings->min_payout_iban;
                                    } elseif ($paymentSettings->payout_swift_enabled) {
                                        $minValue = $paymentSettings->min_payout_swift;
                                    } ?>
                                    <div class="input-group">
                                        <span class="input-group-addon"><?= esc($defaultCurrency->symbol); ?></span>
                                        <input type="hidden" name="currency" value="<?= esc($defaultCurrency->code); ?>">
                                        <input type="text" name="amount" id="product_price_input" aria-describedby="basic-addon2" class="form-control form-input price-input validate-price-input" placeholder="<?= $baseVars->inputInitialPrice; ?>" onpaste="return false;" maxlength="32" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label><?= trans("withdraw_method"); ?></label>
                                    <select name="payout_method" class="form-control custom-select" onchange="update_payout_input(this.value);" required>
                                        <?php if ($paymentSettings->payout_paypal_enabled): ?>
                                            <option value="paypal"><?= trans("paypal"); ?></option>
                                        <?php endif; ?>
                                        <?php if ($paymentSettings->payout_bitcoin_enabled): ?>
                                            <option value="bitcoin"><?= trans("bitcoin"); ?></option>
                                        <?php endif; ?>
                                        <?php if ($paymentSettings->payout_iban_enabled): ?>
                                            <option value="iban"><?= trans("iban"); ?></option>
                                        <?php endif; ?>
                                        <?php if ($paymentSettings->payout_swift_enabled): ?>
                                            <option value="swift"><?= trans("swift"); ?></option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-md btn-success"><?= trans("submit"); ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-5">
                        <div class="minimum-payout-container">
                            <h2 class="title"><?= trans("min_poyout_amounts"); ?></h2>
                            <?php if ($paymentSettings->payout_paypal_enabled): ?>
                                <p><b><?= trans("paypal"); ?></b>:<strong><?= priceFormatted($paymentSettings->min_payout_paypal, $paymentSettings->default_currency) ?></strong></p>
                            <?php endif;
                            if ($paymentSettings->payout_bitcoin_enabled): ?>
                                <p><b><?= trans("bitcoin"); ?></b>:<strong><?= priceFormatted($paymentSettings->min_payout_bitcoin, $paymentSettings->default_currency) ?></strong></p>
                            <?php endif;
                            if ($paymentSettings->payout_iban_enabled): ?>
                                <p><b><?= trans("iban"); ?></b>:<strong><?= priceFormatted($paymentSettings->min_payout_iban, $paymentSettings->default_currency) ?></strong></p>
                            <?php endif;
                            if ($paymentSettings->payout_swift_enabled): ?>
                                <p><b><?= trans("swift"); ?></b>:<strong><?= priceFormatted($paymentSettings->min_payout_swift, $paymentSettings->default_currency) ?></strong></p>
                            <?php endif; ?>
                            <hr>
                            <?php if (authCheck()): ?>
                                <p><b><?= trans("your_balance"); ?>:</b><strong><?= priceFormatted(user()->balance, $paymentSettings->default_currency) ?></strong></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
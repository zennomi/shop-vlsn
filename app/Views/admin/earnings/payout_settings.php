<div class="row">
    <div class="col-sm-12 title-section">
        <h3><?= trans('payout_settings'); ?></h3>
    </div>
</div>
<div class="row">
    <div class="col-lg-6 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans("paypal"); ?></h3>
            </div>
            <form action="<?= base_url('EarningsController/payoutSettingsPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4 col-xs-12">
                                <label><?= trans("status"); ?></label>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="payout_paypal_enabled" value="1" id="paypal_enabled_1" class="square-purple" <?= $paymentSettings->payout_paypal_enabled == 1 ? 'checked' : ''; ?>>
                                <label for="paypal_enabled_1" class="option-label"><?= trans('enable'); ?></label>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="payout_paypal_enabled" value="0" id="paypal_enabled_2" class="square-purple" <?= $paymentSettings->payout_paypal_enabled != 1 ? 'checked' : ''; ?>>
                                <label for="paypal_enabled_2" class="option-label"><?= trans('disable'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('min_poyout_amount'); ?> (<?= $defaultCurrency->symbol; ?>)</label>
                        <input type="text" name="min_payout_paypal" class="form-control form-input price-input" value="<?= getPrice($paymentSettings->min_payout_paypal, 'input'); ?>" onpaste="return false;" maxlength="32" required>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" name="submit" value="paypal" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-lg-6 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans("bitcoin"); ?></h3>
            </div>
            <form action="<?= base_url('EarningsController/payoutSettingsPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4 col-xs-12">
                                <label><?= trans("status"); ?></label>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="payout_bitcoin_enabled" value="1" id="bitcoin_enabled_1" class="square-purple" <?= $paymentSettings->payout_bitcoin_enabled == 1 ? 'checked' : ''; ?>>
                                <label for="bitcoin_enabled_1" class="option-label"><?= trans('enable'); ?></label>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="payout_bitcoin_enabled" value="0" id="bitcoin_enabled_2" class="square-purple" <?= $paymentSettings->payout_bitcoin_enabled != 1 ? 'checked' : ''; ?>>
                                <label for="bitcoin_enabled_2" class="option-label"><?= trans('disable'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('min_poyout_amount'); ?> (<?= $defaultCurrency->symbol; ?>)</label>
                        <input type="text" name="min_payout_bitcoin" class="form-control form-input price-input" value="<?= getPrice($paymentSettings->min_payout_bitcoin, 'input'); ?>" onpaste="return false;" maxlength="32" required>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" name="submit" value="bitcoin" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('iban'); ?></h3>
            </div>
            <form action="<?= base_url('EarningsController/payoutSettingsPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4 col-xs-12">
                                <label><?= trans("status"); ?></label>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="payout_iban_enabled" value="1" id="iban_enabled_1" class="square-purple" <?= $paymentSettings->payout_iban_enabled == 1 ? 'checked' : ''; ?>>
                                <label for="iban_enabled_1" class="option-label"><?= trans('enable'); ?></label>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="payout_iban_enabled" value="0" id="iban_enabled_2" class="square-purple" <?= $paymentSettings->payout_iban_enabled != 1 ? 'checked' : ''; ?>>
                                <label for="iban_enabled_2" class="option-label"><?= trans('disable'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('min_poyout_amount'); ?> (<?= $defaultCurrency->symbol; ?>)</label>
                        <input type="text" name="min_payout_iban" class="form-control form-input price-input" value="<?= getPrice($paymentSettings->min_payout_iban, 'input'); ?>" onpaste="return false;" maxlength="32" required>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" name="submit" value="iban" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-lg-6 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('swift'); ?></h3>
            </div>
            <form action="<?= base_url('EarningsController/payoutSettingsPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4 col-xs-12">
                                <label><?= trans("status"); ?></label>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="payout_swift_enabled" value="1" id="swift_enabled_1" class="square-purple" <?= $paymentSettings->payout_swift_enabled == 1 ? 'checked' : ''; ?>>
                                <label for="swift_enabled_1" class="option-label"><?= trans('enable'); ?></label>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="payout_swift_enabled" value="0" id="swift_enabled_2" class="square-purple" <?= $paymentSettings->payout_swift_enabled != 1 ? 'checked' : ''; ?>>
                                <label for="swift_enabled_2" class="option-label"><?= trans('disable'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('min_poyout_amount'); ?> (<?= $defaultCurrency->symbol; ?>)</label>
                        <input type="text" name="min_payout_swift" class="form-control form-input price-input" value="<?= getPrice($paymentSettings->min_payout_swift, 'input'); ?>" onpaste="return false;" maxlength="32" required>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" name="submit" value="swift" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
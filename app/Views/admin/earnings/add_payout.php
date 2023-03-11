<div class="row">
    <div class="col-lg-6 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans('add_payout'); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('payout-requests'); ?>" class="btn btn-success btn-add-new">
                        <i class="fa fa-list-ul"></i>&nbsp;&nbsp;<?= trans('payout_requests'); ?>
                    </a>
                </div>
            </div>
            <form action="<?= base_url('EarningsController/addPayoutPost'); ?>" method="post" class="validate_price">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label><?= trans("user"); ?></label>
                        <select name="user_id" class="form-control" required>
                            <option value="" selected><?= trans("select"); ?></option>
                            <?php if (!empty($users)):
                                foreach ($users as $user): ?>
                                    <option value="<?= $user->id; ?>"><?= trans("id"); ?>:&nbsp;<?= $user->id; ?>&nbsp;-&nbsp;<?= trans("username"); ?>:&nbsp;<?= esc(getUsername($user)); ?>&nbsp;-&nbsp;<?= trans("balance"); ?>:&nbsp;<?= priceFormatted($user->balance, $paymentSettings->default_currency); ?></option>
                                <?php endforeach;
                            endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><?= trans("withdraw_method"); ?></label>
                        <select name="payout_method" class="form-control custom-select" required>
                            <option value="" selected><?= trans("select"); ?></option>
                            <option value="paypal"><?= trans("paypal"); ?></option>
                            <option value="bitcoin"><?= trans("bitcoin"); ?></option>
                            <option value="iban"><?= trans("iban"); ?></option>
                            <option value="swift"><?= trans("swift"); ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><?= trans("withdraw_amount"); ?>&nbsp;(<?= $paymentSettings->default_currency; ?>)</label>
                        <input type="text" name="amount" class="form-control form-input price-input" placeholder="<?= $baseVars->inputInitialPrice; ?>" onpaste="return false;" maxlength="32" required>
                    </div>
                    <div class="form-group">
                        <label><?= trans("status"); ?></label>
                        <select name="status" class="form-control custom-select" required>
                            <option value="" selected><?= trans("select"); ?></option>
                            <option value="0"><?= trans("pending"); ?></option>
                            <option value="1"><?= trans("completed"); ?></option>
                        </select>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('add_payout'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
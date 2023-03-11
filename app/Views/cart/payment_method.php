<div id="wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="shopping-cart shopping-cart-shipping">
                    <div class="row">
                        <div class="col-sm-12 col-lg-8">
                            <div class="left">
                                <h1 class="cart-section-title"><?= trans("checkout"); ?></h1>
                                <?php if (!authCheck()): ?>
                                    <div class="row m-b-15">
                                        <div class="col-12 col-md-6">
                                            <p><?= trans("checking_out_as_guest"); ?></p>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <p class="text-right"><?= trans("have_account"); ?>&nbsp;<a href="javascript:void(0)" class="link-underlined" data-toggle="modal" data-target="#loginModal"><?= trans("login"); ?></a></p>
                                        </div>
                                    </div>
                                <?php endif;
                                if (!empty($cartHasPhysicalProduct) && $productSettings->marketplace_shipping == 1 && $mdsPaymentType == 'sale'): ?>
                                    <div class="tab-checkout tab-checkout-closed">
                                        <a href="<?= generateUrl('cart', 'shipping'); ?>"><h2 class="title">1.&nbsp;&nbsp;<?= trans("shipping_information"); ?></h2></a>
                                        <a href="<?= generateUrl('cart', 'shipping'); ?>" class="link-underlined edit-link"><?= trans("edit"); ?></a>
                                    </div>
                                <?php endif; ?>
                                <div class="tab-checkout tab-checkout-open">
                                    <h2 class="title">
                                        <?php if (!empty($cartHasPhysicalProduct) && $productSettings->marketplace_shipping == 1 && $mdsPaymentType == 'sale') {
                                            echo '2.';
                                        } else {
                                            echo '1.';
                                        } ?>
                                        &nbsp;<?= trans("payment_method"); ?></h2>
                                    <form action="<?= base_url('payment-method-post'); ?>" method="post" id="form_validate" class="validate_terms">
                                        <?= csrf_field(); ?>
                                        <input type="hidden" name="mds_payment_type" value="<?= $mdsPaymentType ?>">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <ul class="payment-options-list">
                                                        <?php $gateways = getActivePaymentGateways();
                                                        $i = 0;
                                                        if (!empty($gateways)):
                                                            foreach ($gateways as $gateway):?>
                                                                <li>
                                                                    <div class="option-payment">
                                                                        <div class="list-left">
                                                                            <div class="custom-control custom-radio">
                                                                                <input type="radio" class="custom-control-input" id="option_<?= $gateway->id; ?>" name="payment_option" value="<?= esc($gateway->name_key); ?>" required <?= $i == 0 ? 'checked' : ''; ?>>
                                                                                <label class="custom-control-label label-payment-option" for="option_<?= $gateway->id; ?>"><?= esc($gateway->name); ?></label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="list-right">
                                                                            <label for="option_<?= $gateway->id; ?>">
                                                                                <?php $logos = @explode(',', $gateway->logos);
                                                                                if (!empty($logos) && countItems($logos) > 0):
                                                                                    foreach ($logos as $logo): ?>
                                                                                        <img src="<?= base_url('assets/img/payment/' . esc(trim($logo ?? '')) . '.svg'); ?>" alt="<?= esc(trim($logo ?? '')); ?>">
                                                                                    <?php endforeach;
                                                                                endif; ?>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                                <?php $i++;
                                                            endforeach;
                                                        endif;
                                                        if ($paymentSettings->bank_transfer_enabled): ?>
                                                            <li>
                                                                <div class="option-payment">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" class="custom-control-input" id="option_bank" name="payment_option" value="bank_transfer" required <?= $i == 0 ? 'checked' : ''; ?>>
                                                                        <label class="custom-control-label label-payment-option" for="option_bank"><?= trans("bank_transfer"); ?><br><small><?= trans("bank_transfer_exp"); ?></small></label>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        <?php endif;
                                                        if (authCheck() && $paymentSettings->cash_on_delivery_enabled && empty($cartHasDigitalProduct) && $mdsPaymentType == 'sale' && $vendorCashOnDelivery == 1): ?>
                                                            <li>
                                                                <div class="option-payment">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" class="custom-control-input" id="option_cash_on_delivery" name="payment_option" value="cash_on_delivery" required>
                                                                        <label class="custom-control-label label-payment-option" for="option_cash_on_delivery"><?= trans("cash_on_delivery"); ?><br><small><?= trans("cash_on_delivery_exp"); ?></small></label>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        <?php endif; ?>
                                                    </ul>
                                                </div>
                                                <div class="form-group">
                                                    <div class="custom-control custom-checkbox custom-control-validate-input">
                                                        <input type="checkbox" class="custom-control-input" name="terms" id="checkbox_terms" required>
                                                        <label for="checkbox_terms" class="custom-control-label"><?= trans("terms_conditions_exp"); ?>&nbsp;
                                                            <?php $pageTerms = getPageByDefaultName('terms_conditions', selectedLangId());
                                                            if (!empty($pageTerms)): ?>
                                                                <a href="<?= generateUrl($pageTerms->page_default_name); ?>" class="link-terms" target="_blank"><strong><?= esc($pageTerms->title); ?></strong></a>
                                                            <?php endif; ?>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-group m-t-15">
                                                    <?php if ($mdsPaymentType == 'sale'): ?>
                                                        <a href="<?= generateUrl('cart'); ?>" class="link-underlined link-return-cart"><&nbsp;<?= trans("return_to_cart"); ?></a>
                                                    <?php endif; ?>
                                                    <button type="submit" name="submit" value="update" class="btn btn-lg btn-custom btn-continue-payment float-right"><?= trans("continue_to_payment") ?></button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-checkout tab-checkout-closed-bordered">
                                    <h2 class="title">
                                        <?php if (!empty($cartHasPhysicalProduct) && $productSettings->marketplace_shipping == 1 && $mdsPaymentType == 'sale') {
                                            echo '3.';
                                        } else {
                                            echo '2.';
                                        } ?>
                                        &nbsp;<?= trans("payment"); ?>
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <?php if ($mdsPaymentType == 'membership'):
                            echo view('cart/_order_summary_membership');
                        elseif ($mdsPaymentType == 'promote'):
                            echo view('cart/_order_summary_promote');
                        else:
                            echo view('cart/_order_summary');
                        endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
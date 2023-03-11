<div id="wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="page-confirm">
                    <div class="circle-loader">
                        <div class="checkmark draw"></div>
                    </div>
                    <h1 class="title"><?= trans("msg_payment_completed"); ?></h1>
                    <?php if(!empty(helperGetSession('modesy_membership_request_type')) && helperGetSession('modesy_membership_request_type') == 'renew'): ?>
                    <p class="m-t-15 text-success"><?= trans("msg_membership_renewed"); ?></p>
                    <?php else: ?>
                        <p class="m-t-15 text-success"><?= trans("msg_start_selling"); ?></p>
                    <?php endif;
                    if(!empty($transactionNumber)): ?>
                        <p class="p-order-number"><?= trans("transaction_number"); ?><br><?= esc($transactionNumber); ?></p>
                    <?php endif;
                    if($method != 'gtw'): ?>
                        <p class="p-complete-payment"><?= trans("msg_bank_transfer_text_transaction_completed"); ?></p>
                        <div class="bank-account-container">
                            <?= $paymentSettings->bank_transfer_accounts; ?>
                        </div>
                    <?php endif; ?>
                    <div class="m-t-45 text-center">
                        <a href="<?= base_url('invoice-membership/'. $transaction->id); ?>" class="btn btn-lg btn-info color-white" target="_blank"><i class="icon-text-o"></i>&nbsp;&nbsp;<?= trans("view_invoice"); ?></a>
                        <?php if(!empty(helperGetSession('modesy_membership_request_type')) && helperGetSession('modesy_membership_request_type') == 'renew'): ?>
                            <a href="<?= generateDashUrl('shop_settings');?>" class="btn btn-lg btn-custom"><?= trans("go_back_to_shop_settings"); ?></a>
                        <?php else: ?>
                            <a href="<?= base_url();?>" class="btn btn-lg btn-custom"><?= trans("goto_home"); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .circle-loader{margin-bottom:3.5em;border:1px solid rgba(0,0,0,0.2);border-left-color:#5cb85c;animation:loader-spin 1.2s infinite linear;position:relative;display:inline-block;vertical-align:top;border-radius:50%;width:7em;height:7em}.load-complete{-webkit-animation:none;animation:none;border-color:#5cb85c;transition:border 500ms ease-out}.checkmark{display:none}.checkmark.draw:after{animation-duration:800ms;animation-timing-function:ease;animation-name:checkmark;transform:scaleX(-1) rotate(135deg)}.checkmark:after{opacity:1;height:3.5em;width:1.75em;transform-origin:left top;border-right:3px solid #5cb85c;border-top:3px solid #5cb85c;content:'';left:1.75em;top:3.5em;position:absolute}@keyframes loader-spin{0%{transform:rotate(0deg)}100%{transform:rotate(360deg)}}@keyframes checkmark{0%{height:0;width:0;opacity:1}20%{height:0;width:1.75em;opacity:1}40%{height:3.5em;width:1.75em;opacity:1}100%{height:3.5em;width:1.75em;opacity:1}}.error-circle{margin-bottom:3.5em;border:1px solid #dc3545;position:relative;display:inline-block;vertical-align:top;border-radius:50%;width:7em;height:7em;line-height:7em;color:#dc3545}.error-circle i{font-size:30px}
</style>
<?php helperDeleteSession('modesy_selected_membership_plan_id');
helperDeleteSession('mds_membership_bank_transaction_number');
helperDeleteSession('mds_membership_transaction_insert_id'); ?>



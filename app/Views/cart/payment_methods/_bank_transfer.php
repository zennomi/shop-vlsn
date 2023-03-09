<?php if ($cartPaymentMethod->payment_option == 'bank_transfer'):
    if ($mdsPaymentType == 'promote'): ?>
        <?php if ($cartPaymentMethod->payment_option == 'bank_transfer'): ?>
            <form action="<?= base_url('bank-transfer-payment-post'); ?>" method="post">
                <?= csrf_field(); ?>
                <input type="hidden" name="mds_payment_type" value="<?= $mdsPaymentType; ?>">
                <input type="hidden" name="payment_id" value="<?= $transactionNumber; ?>">
                <div class="bank-account-container">
                    <?= $paymentSettings->bank_transfer_accounts; ?>
                </div>
                <div id="payment-button-container" class="payment-button-cnt">
                    <p class="p-transaction-number"><span><?= trans("transaction_number"); ?>:&nbsp;<?= esc($transactionNumber); ?></span></p>
                    <p class="p-complete-payment"><?= trans("msg_promote_bank_transfer_text"); ?></p>
                    <button type="submit" name="submit" value="update" class="btn btn-lg btn-custom btn-payment"><?= trans("place_order") ?></button>
                </div>
            </form>
        <?php endif;
    else: ?>
        <div class="row">
            <div class="col-12">
                <?= view('partials/_messages'); ?>
            </div>
        </div>
        <div class="bank-account-container">
            <?= $paymentSettings->bank_transfer_accounts; ?>
            <p class="p-complete-payment"><?= trans("msg_bank_transfer_text"); ?></p>
        </div>
        <form action="<?= base_url('bank-transfer-payment-post'); ?>" method="post">
            <?= csrf_field(); ?>
            <input type="hidden" name="mds_payment_type" value="<?= $mdsPaymentType; ?>">
            <div id="payment-button-container" class="payment-button-cnt">
                <button type="submit" name="submit" value="update" class="btn btn-lg btn-custom btn-payment"><?= trans("place_order") ?></button>
            </div>
        </form>
    <?php endif;
endif; ?>
<script>
    $('form').submit(function () {
        $(".btn-place-order").prop('disabled', true);
    });
</script>




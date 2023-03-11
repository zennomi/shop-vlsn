<?php if ($cartPaymentMethod->payment_option == 'cash_on_delivery'):
    if ($mdsPaymentType != 'promote'): ?>
        <div class="row">
            <div class="col-12">
                <?= view('partials/_messages'); ?>
            </div>
        </div>
        <form action="<?= base_url('cash-on-delivery-payment-post'); ?>" method="post">
            <?= csrf_field(); ?>
            <div id="payment-button-container" class="payment-button-cnt">
                <p class="m-b-30 font-600">
                    <?= trans("cash_on_delivery_warning"); ?>
                </p>
                <button type="submit" name="submit" value="update" class="btn btn-lg btn-custom btn-payment m-t-30"><?= trans("place_order") ?></button>
            </div>
        </form>
    <?php endif;
endif; ?>
<script>
    $('form').submit(function () {
        $(".btn-place-order").prop('disabled', true);
    });
</script>
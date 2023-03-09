<script src="<?= base_url('assets/js/jquery-3.5.1.min.js'); ?>"></script>
<script>
    $(window).bind("load", function () {
        $("#payment-button-container").css("visibility", "visible");
    });
</script>
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
                                <?php endif; ?>
                                <?php if (!empty($cartHasPhysicalProduct) && $productSettings->marketplace_shipping == 1 && $mdsPaymentType == 'sale'): ?>
                                    <div class="tab-checkout tab-checkout-closed">
                                        <a href="<?= generateUrl('cart', 'shipping'); ?>"><h2 class=" title">1.&nbsp;&nbsp;<?= trans("shipping_information"); ?></h2></a>
                                        <a href="<?= generateUrl('cart', 'shipping'); ?>" class="link-underlined"><?= trans("edit"); ?></a>
                                    </div>
                                <?php endif; ?>
                                <div class="tab-checkout tab-checkout-closed">
                                    <?php if ($mdsPaymentType == 'membership' || $mdsPaymentType == 'promote'): ?>
                                        <a href="<?= generateUrl('cart', 'payment_method'); ?>?payment_type=<?= $mdsPaymentType; ?>"><h2 class="title">
                                                <?php if (!empty($cartHasPhysicalProduct) && $mdsPaymentType == 'sale') {
                                                    echo '2.';
                                                } else {
                                                    echo '1.';
                                                } ?>
                                                &nbsp;<?= trans("payment_method"); ?></h2></a>
                                        <a href="<?= generateUrl('cart', 'payment_method'); ?>?payment_type=<?= $mdsPaymentType; ?>" class="link-underlined"><?= trans("edit"); ?></a>
                                    <?php else: ?>
                                        <a href="<?= generateUrl('cart', 'payment_method'); ?>"><h2 class=" title">
                                                <?php if (!empty($cartHasPhysicalProduct) && $productSettings->marketplace_shipping == 1 && $mdsPaymentType == 'sale') {
                                                    echo '2.';
                                                } else {
                                                    echo '1.';
                                                } ?>
                                                &nbsp;<?= trans("payment_method"); ?></h2></a>
                                        <a href="<?= generateUrl('cart', 'payment_method'); ?>" class="link-underlined"><?= trans("edit"); ?></a>
                                    <?php endif; ?>
                                </div>
                                <div class="tab-checkout tab-checkout-open">
                                    <h2 class="title">
                                        <?php if (!empty($cartHasPhysicalProduct) && $productSettings->marketplace_shipping == 1 && $mdsPaymentType == 'sale') {
                                            echo '3.';
                                        } else {
                                            echo '2.';
                                        } ?>&nbsp;
                                        <?= trans("payment"); ?>
                                    </h2>
                                    <div class="row">
                                        <div class="col-12">
                                            <?php
                                            $data = ['totalAmount' => $totalAmount, 'currency' => $currency, 'mdsPaymentType' => $mdsPaymentType, 'cartTotal' => $cartTotal, 'mdsPaymentToken' => generateToken()];
                                            if (!empty($cartPaymentMethod->payment_option)) {
                                                $data['paymentGateway'] = getPaymentGateway($cartPaymentMethod->payment_option);
                                            }
                                            if ($cartPaymentMethod->payment_option == 'bank_transfer') {
                                                echo view('cart/payment_methods/_bank_transfer', $data);
                                            } elseif (authCheck() && $cartPaymentMethod->payment_option == 'cash_on_delivery') {
                                                echo view('cart/payment_methods/_cash_on_delivery', $data);
                                            } else {
                                                $sessData = new stdClass();
                                                $sessData->mds_payment_token = $data['mdsPaymentToken'];
                                                $sessData->currency = $data['currency'];
                                                $sessData->total_amount = $data['totalAmount'];
                                                $sessData->payment_type = $mdsPaymentType;
                                                helperSetSession('mds_payment_cart_data', $sessData);
                                                //load view
                                                if (empty($cartPaymentMethod->payment_option)) {
                                                    redirectToBackUrl(generateUrl('cart', 'payment_method'));
                                                }
                                                echo view('cart/payment_methods/_' . $cartPaymentMethod->payment_option, $data);
                                            } ?>
                                        </div>
                                    </div>
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
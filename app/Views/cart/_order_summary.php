<div class="col-sm-12 col-lg-4 order-summary-container">
    <h2 class="cart-section-title"><?= trans("order_summary"); ?> (<?= getCartProductCount(); ?>)</h2>
    <div class="right">
        <?php $isPhysical = false; ?>
        <div class="cart-order-details">
            <?php if (!empty($cartItems)):
                foreach ($cartItems as $cartItem):
                    $product = getActiveProduct($cartItem->product_id);
                    if (!empty($product)):
                        if ($product->product_type == 'physical') {
                            $isPhysical = true;
                        } ?>
                        <div class="item">
                            <div class="item-left">
                                <div class="img-cart-product">
                                    <a href="<?= generateProductUrl($product); ?>">
                                        <img src="<?= base_url(IMG_BG_PRODUCT_SMALL); ?>" data-src="<?= getProductMainImage($cartItem->product_id, 'image_small'); ?>" alt="<?= getProductTitle($product); ?>" class="lazyload img-fluid img-product" onerror="this.src='<?= base_url(IMG_BG_PRODUCT_SMALL); ?>'">
                                    </a>
                                </div>
                            </div>
                            <div class="item-right">
                                <?php if ($product->product_type == 'digital'): ?>
                                    <div class="list-item">
                                        <label class="label-instant-download label-instant-download-sm"><i class="icon-download-solid"></i><?= trans("instant_download"); ?></label>
                                    </div>
                                <?php endif; ?>
                                <div class="list-item">
                                    <a href="<?= generateProductUrl($product); ?>"><?= esc($cartItem->product_title); ?></a>
                                </div>
                                <div class="list-item seller">
                                    <?= trans("by"); ?>&nbsp;<a href="<?= generateProfileUrl($product->user_slug); ?>"><?= esc($product->user_username); ?></a>
                                </div>
                                <div class="list-item m-t-15">
                                    <label><?= trans("quantity"); ?>:</label>
                                    <strong class="lbl-price"><?= $cartItem->quantity; ?></strong>
                                </div>
                                <div class="list-item">
                                    <label><?= trans("price"); ?>:</label>
                                    <strong class="lbl-price"><?= priceDecimal($cartItem->total_price, $cartItem->currency); ?></strong>
                                </div>
                                <?php if (!empty($cartItem->product_vat)): ?>
                                    <div class="list-item">
                                        <label><?= trans("vat"); ?>:</label>
                                        <strong><?= priceDecimal($cartItem->product_vat, $cartItem->currency); ?></strong>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif;
                endforeach;
            endif; ?>
        </div>
        <div class="row-custom m-t-30 m-b-10">
            <strong><?= trans("subtotal"); ?><span class="float-right"><?= priceDecimal($cartTotal->subtotal, $cartTotal->currency); ?></span></strong>
        </div>
        <?php if (!empty($cartTotal->vat)): ?>
            <div class="row-custom m-b-10">
                <strong><?= trans("vat"); ?><span class="float-right"><?= priceDecimal($cartTotal->vat, $cartTotal->currency); ?></span></strong>
            </div>
        <?php endif;
        if (!empty($showShippingCost) && !empty($cartTotal->shipping_cost)): ?>
            <div class="row-custom m-b-10">
                <strong><?= trans("shipping"); ?><span class="float-right"><?= priceDecimal($cartTotal->shipping_cost, $cartTotal->currency); ?></span></strong>
            </div>
        <?php endif; ?>
        <?php if ($cartTotal->coupon_discount > 0): ?>
            <div class="row-custom m-b-15">
                <strong><?= trans("coupon"); ?>&nbsp;&nbsp;[<?= getCartDiscountCoupon(); ?>]&nbsp;&nbsp;<a href="javascript:void(0)" class="font-weight-normal" onclick="removeCartDiscountCoupon();">[<?= trans("remove"); ?>]</a><span class="float-right">-&nbsp;<?= priceDecimal($cartTotal->coupon_discount, $cartTotal->currency); ?></span></strong>
            </div>
        <?php endif; ?>
        <div class="row-custom">
            <p class="line-seperator"></p>
        </div>
        <?php if (!empty($showShippingCost) && !empty($cartTotal->shipping_cost)): ?>
            <div class="row-custom">
                <strong><?= trans("total"); ?><span class="float-right"><?= priceDecimal($cartTotal->total, $cartTotal->currency); ?></span></strong>
            </div>
        <?php else: ?>
            <div class="row-custom">
                <strong><?= trans("total"); ?><span class="float-right"><?= priceDecimal($cartTotal->total_before_shipping, $cartTotal->currency); ?></span></strong>
            </div>
        <?php endif; ?>
    </div>
</div>
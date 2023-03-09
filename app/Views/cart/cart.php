<div id="wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <?php if ($cartItems != null): ?>
                    <div class="shopping-cart">
                        <div class="row">
                            <div class="col-sm-12 col-lg-8">
                                <div class="left">
                                    <h1 class="cart-section-title"><?= trans("my_cart"); ?> (<?= getCartProductCount(); ?>)</h1>
                                    <?php if (!empty($cartItems)):
                                        foreach ($cartItems as $cartItem):
                                            $product = getActiveProduct($cartItem->product_id);
                                            if (!empty($product)): ?>
                                                <div class="item">
                                                    <div class="cart-item-image">
                                                        <div class="img-cart-product">
                                                            <a href="<?= generateProductUrl($product); ?>">
                                                                <img src="<?= base_url(IMG_BG_PRODUCT_SMALL); ?>" data-src="<?= getProductMainImage($cartItem->product_id, 'image_small'); ?>" alt="<?= esc($cartItem->product_title); ?>" class="lazyload img-fluid img-product" onerror="this.src='<?= base_url(IMG_BG_PRODUCT_SMALL); ?>'">
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="cart-item-details">
                                                        <?php if ($product->product_type == 'digital'): ?>
                                                            <div class="list-item">
                                                                <label class="label-instant-download label-instant-download-sm"><i class="icon-download-solid"></i><?= trans("instant_download"); ?></label>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div class="list-item">
                                                            <a href="<?= generateProductUrl($product); ?>"><?= esc($cartItem->product_title); ?></a>
                                                            <?php if (empty($cartItem->is_stock_available)): ?>
                                                                <div class="lbl-enough-quantity"><?= trans("out_of_stock"); ?></div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="list-item seller">
                                                            <?= trans("by"); ?>&nbsp;<a href="<?= generateProductUrlBySlug($product->user_slug); ?>"><?= esc($product->user_username); ?></a>
                                                        </div>
                                                        <?php if ($cartItem->purchase_type != 'bidding'): ?>
                                                            <div class="list-item m-t-15">
                                                                <label><?= trans("unit_price"); ?>:</label>
                                                                <strong class="lbl-price">
                                                                    <?= priceDecimal($cartItem->unit_price, $cartItem->currency);
                                                                    if (!empty($cartItem->discount_rate)): ?>
                                                                        <span class="discount-rate-cart">
                                                                        (<?= discountRateFormat($cartItem->discount_rate); ?>)
                                                                    </span>
                                                                    <?php endif; ?>
                                                                </strong>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div class="list-item">
                                                            <label><?= trans("total"); ?>:</label>
                                                            <strong class="lbl-price"><?= priceDecimal($cartItem->total_price, $cartItem->currency); ?></strong>
                                                        </div>
                                                        <?php if (!empty($product->vat_rate)): ?>
                                                            <div class="list-item">
                                                                <label><?= trans("vat"); ?>&nbsp;(<?= $product->vat_rate; ?>%):</label>
                                                                <strong class="lbl-price"><?= priceDecimal($cartItem->product_vat, $cartItem->currency); ?></strong>
                                                            </div>
                                                        <?php endif; ?>
                                                        <a href="javascript:void(0)" class="btn btn-sm btn-outline-gray btn-cart-remove" onclick="removeFromCart('<?= $cartItem->cart_item_id; ?>');"><i class="icon-close"></i> <?= trans("remove"); ?></a>
                                                    </div>
                                                    <div class="cart-item-quantity">
                                                        <?php if ($cartItem->purchase_type == 'bidding'): ?>
                                                            <span><?= trans("quantity") . ': ' . $cartItem->quantity; ?></span>
                                                        <?php else:
                                                            if ($product->listing_type != 'license_key' && $product->product_type != 'digital'):?>
                                                                <div class="number-spinner">
                                                                    <div class="input-group">
                                                                        <span class="input-group-btn">
                                                                            <button type="button" class="btn btn-default btn-spinner-minus" data-cart-item-id="<?= $cartItem->cart_item_id; ?>" data-dir="dwn">-</button>
                                                                        </span>
                                                                        <input type="text" id="q-<?= $cartItem->cart_item_id; ?>" class="form-control text-center" value="<?= $cartItem->quantity; ?>" data-product-id="<?= $cartItem->product_id; ?>" data-cart-item-id="<?= $cartItem->cart_item_id; ?>">
                                                                        <span class="input-group-btn">
                                                                            <button type="button" class="btn btn-default btn-spinner-plus" data-cart-item-id="<?= $cartItem->cart_item_id; ?>" data-dir="up">+</button>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            <?php endif;
                                                        endif; ?>
                                                    </div>
                                                </div>
                                            <?php endif;
                                        endforeach;
                                    endif; ?>
                                </div>
                                <a href="<?= langBaseUrl(); ?>" class="btn btn-md btn-custom m-t-15"><i class="icon-arrow-left m-r-2"></i><?= trans("keep_shopping") ?></a>
                            </div>
                            <div class="col-sm-12 col-lg-4">
                                <div class="right">
                                    <div class="row-custom m-b-15">
                                        <strong><?= trans("subtotal"); ?><span class="float-right"><?= priceDecimal($cartTotal->subtotal, $cartTotal->currency); ?></span></strong>
                                    </div>
                                    <?php if (!empty($cartTotal->vat)): ?>
                                        <div class="row-custom m-b-15">
                                            <strong><?= trans("vat"); ?><span class="float-right"><?= priceDecimal($cartTotal->vat, $cartTotal->currency); ?></span></strong>
                                        </div>
                                    <?php endif;
                                    if ($cartTotal->coupon_discount > 0): ?>
                                        <div class="row-custom">
                                            <strong><?= trans("coupon"); ?>&nbsp;&nbsp;[<?= getCartDiscountCoupon(); ?>]&nbsp;&nbsp;<a href="javascript:void(0)" class="font-weight-normal" onclick="removeCartDiscountCoupon();">[<?= trans("remove"); ?>]</a><span class="float-right">-&nbsp;<?= priceDecimal($cartTotal->coupon_discount, $cartTotal->currency); ?></span></strong>
                                        </div>
                                    <?php endif; ?>
                                    <div class="row-custom">
                                        <p class="line-seperator"></p>
                                    </div>
                                    <div class="row-custom m-b-10">
                                        <strong><?= trans("total"); ?><span class="float-right"><?= priceDecimal($cartTotal->total_before_shipping, $cartTotal->currency); ?></span></strong>
                                    </div>
                                    <div class="row-custom m-t-30 m-b-15">
                                        <?php if (empty($cartTotal->is_stock_available)): ?>
                                            <a href="javascript:void(0)" class="btn btn-block"><?= trans("continue_to_checkout"); ?></a>
                                        <?php else:
                                            if (empty(authCheck()) && $generalSettings->guest_checkout != 1): ?>
                                                <a href="#" class="btn btn-block" data-toggle="modal" data-target="#loginModal"><?= trans("continue_to_checkout"); ?></a>
                                            <?php else:
                                                if ($cartHasPhysicalProduct == true && $productSettings->marketplace_shipping == 1): ?>
                                                    <a href="<?= generateUrl('cart', 'shipping'); ?>" class="btn btn-block"><?= trans("continue_to_checkout"); ?></a>
                                                <?php else: ?>
                                                    <a href="<?= generateUrl('cart', 'payment_method'); ?>" class="btn btn-block"><?= trans("continue_to_checkout"); ?></a>
                                                <?php endif;
                                            endif;
                                        endif; ?>
                                    </div>
                                    <?php $envPaymentIcons = getenv('PAYMENT_ICONS');
                                    if (!empty($envPaymentIcons)):
                                        $paymentIconsArray = explode(',', $envPaymentIcons ?? '');
                                        if (!empty($paymentIconsArray) && countItems($paymentIconsArray) > 0):?>
                                            <div class="payment-icons">
                                                <?php foreach ($paymentIconsArray as $icon):
                                                    if (file_exists(FCPATH . 'assets/img/payment/' . $icon . '.svg')):?>
                                                        <img src="<?= base_url('assets/img/payment/' . $icon . '.svg'); ?>" alt="<?= $icon; ?>">
                                                    <?php endif;
                                                endforeach; ?>
                                            </div>
                                        <?php
                                        endif;
                                    endif; ?>
                                    <hr class="m-t-30 m-b-30">
                                    <form action="<?= base_url('coupon-code-post'); ?>" method="post" id="form_validate" class="m-0">
                                        <?= csrf_field(); ?>
                                        <label class="font-600"><?= trans("discount_coupon") ?></label>
                                        <div class="cart-discount-coupon">
                                            <input type="text" name="coupon_code" class="form-control form-input" value="<?= esc(old('coupon_code')); ?>" maxlength="254" placeholder="<?= trans("coupon_code") ?>" required>
                                            <button type="submit" class="btn btn-custom m-l-5"><?= trans("apply") ?></button>
                                        </div>
                                    </form>
                                    <div class="cart-coupon-error">
                                        <?php if (!empty(helperGetSession('error_coupon_code'))): ?>
                                            <div class="text-danger">
                                                <?= helperGetSession('error_coupon_code'); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="shopping-cart-empty">
                        <p><strong class="font-600"><?= trans("your_cart_is_empty"); ?></strong></p>
                        <a href="<?= langBaseUrl(); ?>" class="btn btn-lg btn-custom"><i class="icon-arrow-left"></i>&nbsp;<?= trans("shop_now"); ?></a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
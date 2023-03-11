<div class="col-sm-12 col-lg-4 order-summary-container">
    <h2 class="cart-section-title"><?= trans("order_summary"); ?> (1)</h2>
    <div class="right">
        <?php if (!empty($promotedPlan)):
            $product = getActiveProduct($promotedPlan->product_id);
            if (!empty($product)):?>
                <div class="cart-order-details">
                    <div class="item">
                        <div class="item-left">
                            <div class="img-cart-product">
                                <a href="<?= generateProductUrl($product); ?>">
                                    <img src="<?= base_url(IMG_BG_PRODUCT_SMALL); ?>" data-src="<?= getProductMainImage($product->id, 'image_small'); ?>" alt="<?= getProductTitle($product); ?>" class="lazyload img-fluid img-product" onerror="this.src='<?= base_url(IMG_BG_PRODUCT_SMALL); ?>'">
                                </a>
                            </div>
                        </div>
                        <div class="item-right">
                            <div class="list-item">
                                <a href="<?= generateProductUrl($product); ?>"><?= getProductTitle($product); ?></a>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="item-right">
                            <div class="list-item m-t-15">
                                <label><?= trans("promote_plan"); ?>:</label>
                                <strong class="lbl-price"><?= $promotedPlan->purchased_plan; ?></strong>
                            </div>
                            <div class="list-item">
                                <label><?= trans("price"); ?>:</label>
                                <strong class="lbl-price"><?= priceDecimal($promotedPlan->total_amount, $selectedCurrency->code, true); ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-custom m-t-30">
                    <strong><?= trans("subtotal"); ?><span class="float-right"><?= priceDecimal($promotedPlan->total_amount, $selectedCurrency->code, true); ?></span></strong>
                </div>
                <div class="row-custom">
                    <p class="line-seperator"></p>
                </div>
                <div class="row-custom">
                    <strong><?= trans("total"); ?><span class="float-right"><?= priceDecimal($promotedPlan->total_amount, $selectedCurrency->code, true); ?></span></strong>
                </div>
            <?php endif;
        endif; ?>
    </div>
</div>
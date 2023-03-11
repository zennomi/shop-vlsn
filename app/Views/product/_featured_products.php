<div id="promoted_posts">
    <h3 class="title"><?= trans("featured_products"); ?></h3>
    <p class="title-exp"><?= trans("featured_products_exp"); ?></p>
    <div id="row_promoted_products" class="row row-product">
        <?php if (!empty($promotedProducts)):
            foreach ($promotedProducts as $product): ?>
                <div class="col-6 col-sm-4 col-md-3 col-mds-5 col-product">
                    <?= view('product/_product_item', ['product' => $product, 'promotedBadge' => false, 'isSlider' => 0, 'discountLabel' => 0]); ?>
                </div>
            <?php endforeach;
        endif; ?>
    </div>
    <input type="hidden" id="promoted_products_offset" value="<?= countItems($promotedProducts); ?>">
    <div id="load_promoted_spinner" class="col-12 load-more-spinner">
        <div class="row">
            <div class="spinner">
                <div class="bounce1"></div>
                <div class="bounce2"></div>
                <div class="bounce3"></div>
            </div>
        </div>
    </div>
    <?php if ($promotedProductsCount > countItems($promotedProducts)): ?>
        <div class="row-custom text-center promoted-load-more-container">
            <a href="javascript:void(0)" class="link-see-more" onclick="loadMorePromotedProducts();"><span><?= trans("load_more"); ?>&nbsp;<i class="icon-arrow-down"></i></span></a>
        </div>
    <?php endif; ?>
</div>
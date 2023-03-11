<?php if (!empty($specialOffers)):
    if (countItems($specialOffers) > 4): ?>
        <div class="col-12 section section-category-products">
            <div class="section-header">
                <h3 class="title"><?= trans("special_offers"); ?></h3>
            </div>
            <div class="row-custom category-slider-container" <?= $baseVars->rtl == true ? 'dir="rtl"' : ''; ?>>
                <div class="row row-product" id="slider_special_offers">
                    <?php foreach ($specialOffers as $product): ?>
                        <div class="col-6 col-sm-4 col-md-3 col-mds-5 col-product">
                            <?= view('product/_product_item', ['product' => $product, 'promotedBadge' => false, 'isSlider' => 1, 'discountLabel' => 1]); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div id="slider_special_offers_nav" class="index-products-slider-nav">
                    <button class="prev"><i class="icon-arrow-left"></i></button>
                    <button class="next"><i class="icon-arrow-right"></i></button>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="col-12 section section-category-products">
            <div class="section-header">
                <h3 class="title"><?= trans("special_offers"); ?></h3>
            </div>
            <div class="row row-product">
                <?php foreach ($specialOffers as $product): ?>
                    <div class="col-6 col-sm-4 col-md-3 col-mds-5 col-product">
                        <?= view('product/_product_item', ['product' => $product, 'promotedBadge' => false, 'isSlider' => 1, 'discountLabel' => 1]); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif;
endif; ?>
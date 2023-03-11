<?php if (!empty($indexCategories) && !empty($categoriesProductsArray)):
    foreach ($indexCategories as $category):
        $numItems = !empty($categoriesProductsArray[$category->id]) ? countItems($categoriesProductsArray[$category->id]) : 0;
        if ($numItems > 4): ?>
            <div class="col-12 section section-category-products">
                <div class="section-header">
                    <h3 class="title">
                        <a href="<?= generateCategoryUrl($category); ?>"><?= getCategoryName($category); ?></a>
                    </h3>
                </div>
                <div class="row-custom category-slider-container" <?= $baseVars->rtl == true ? 'dir="rtl"' : ''; ?>>
                    <div class="row row-product" id="category_products_slider_<?= $category->id; ?>">
                        <?php if (!empty($categoriesProductsArray[$category->id])):
                            foreach ($categoriesProductsArray[$category->id] as $product): ?>
                                <div class="col-6 col-sm-4 col-md-3 col-mds-5 col-product">
                                    <?= view('product/_product_item', ['product' => $product, 'promotedBadge' => false, 'isSlider' => 1, 'discountLabel' => 0]); ?>
                                </div>
                            <?php endforeach;
                        endif; ?>
                    </div>
                    <div id="category-products-slider-nav-<?= $category->id; ?>" class="index-products-slider-nav">
                        <button class="prev"><i class="icon-arrow-left"></i></button>
                        <button class="next"><i class="icon-arrow-right"></i></button>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="col-12 section section-category-products">
                <div class="section-header">
                    <h3 class="title">
                        <a href="<?= generateCategoryUrl($category); ?>"><?= getCategoryName($category); ?></a>
                    </h3>
                </div>
                <div class="row row-product">
                    <?php if (!empty($categoriesProductsArray[$category->id])):
                        foreach ($categoriesProductsArray[$category->id] as $item): ?>
                            <div class="col-6 col-sm-4 col-md-3 col-mds-5 col-product">
                                <?= view('product/_product_item', ['product' => $item, 'promotedBadge' => false, 'isSlider' => 0, 'discountLabel' => 0]); ?>
                            </div>
                        <?php endforeach;
                    endif; ?>
                </div>
            </div>
        <?php endif;
    endforeach;
endif; ?>
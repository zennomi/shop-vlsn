<div class="product-item">
    <div class="row-custom<?= !empty($product->image_second) ? ' product-multiple-image' : ''; ?>">
        <a class="item-wishlist-button item-wishlist-enable <?= isProductInWishlist($product) == 1 ? 'item-wishlist' : ''; ?>" data-product-id="<?= $product->id; ?>"></a>
        <div class="img-product-container">
            <?php if (!empty($isSlider)): ?>
                <a href="<?= generateProductUrl($product); ?>">
                    <img src="<?= base_url(IMG_BG_PRODUCT_SMALL); ?>" data-lazy="<?= getProductItemImage($product); ?>" alt="<?= getProductTitle($product); ?>" class="img-fluid img-product">
                    <?php if (!empty($product->image_second)): ?>
                        <img src="<?= base_url(IMG_BG_PRODUCT_SMALL); ?>" data-lazy="<?= getProductItemImage($product, true); ?>" alt="<?= getProductTitle($product); ?>" class="img-fluid img-product img-second">
                    <?php endif; ?>
                </a>
            <?php else: ?>
                <a href="<?= generateProductUrl($product); ?>">
                    <img src="<?= base_url(IMG_BG_PRODUCT_SMALL); ?>" data-src="<?= getProductItemImage($product); ?>" alt="<?= getProductTitle($product); ?>" class="lazyload img-fluid img-product">
                    <?php if (!empty($product->image_second)): ?>
                        <img src="<?= base_url(IMG_BG_PRODUCT_SMALL); ?>" data-src="<?= getProductItemImage($product, true); ?>" alt="<?= getProductTitle($product); ?>" class="lazyload img-fluid img-product img-second">
                    <?php endif; ?>
                </a>
            <?php endif; ?>
            <div class="product-item-options">
                <a href="javascript:void(0)" class="item-option btn-add-remove-wishlist" data-toggle="tooltip" data-placement="left" data-product-id="<?= $product->id; ?>" data-type="list" title="<?= trans("wishlist"); ?>">
                    <?php if (isProductInWishlist($product) == 1): ?>
                        <i class="icon-heart"></i>
                    <?php else: ?>
                        <i class="icon-heart-o"></i>
                    <?php endif; ?>
                </a>
                <?php if (($product->listing_type == 'sell_on_site' || $product->listing_type == 'bidding') && $product->is_free_product != 1):
                    if (!empty($product->has_variation) || $product->listing_type == 'bidding'):?>
                        <a href="<?= generateProductUrl($product); ?>" class="item-option" data-toggle="tooltip" data-placement="left" data-product-id="<?= $product->id; ?>" data-reload="0" title="<?= trans("view_options"); ?>">
                            <i class="icon-cart"></i>
                        </a>
                    <?php else:
                        $itemUniqueID = uniqid();
                        if ($product->stock > 0):?>
                            <a href="javascript:void(0)" id="btn_add_cart_<?= $itemUniqueID; ?>" class="item-option btn-item-add-to-cart" data-id="<?= $itemUniqueID; ?>" data-toggle="tooltip" data-placement="left" data-product-id="<?= $product->id; ?>" data-reload="0" title="<?= trans("add_to_cart"); ?>">
                                <i class="icon-cart"></i>
                            </a>
                        <?php endif;
                    endif;
                endif; ?>
            </div>
            <?php if (!empty($product->discount_rate) && !empty($discountLabel)): ?>
                <span class="badge badge-discount">-<?= $product->discount_rate; ?>%</span>
            <?php endif; ?>
        </div>
        <?php if ($product->is_promoted && $generalSettings->promoted_products == 1 && isset($promotedBadge) && $promotedBadge == true): ?>
            <span class="badge badge-dark badge-promoted"><?= trans("featured"); ?></span>
        <?php endif; ?>
    </div>
    <div class="row-custom item-details">
        <h3 class="product-title">
            <a href="<?= generateProductUrl($product); ?>"><?= getProductTitle($product); ?></a>
        </h3>
        <p class="product-user text-truncate">
            <a href="<?= generateProfileUrl($product->user_slug); ?>"><?= esc($product->user_username); ?></a>
        </p>
        <div class="product-item-rating">
            <?php if ($generalSettings->reviews == 1):
                echo view('partials/_review_stars', ['rating' => $product->rating]);
            endif; ?>
            <span class="item-wishlist"><i class="icon-heart-o"></i><?= $product->wishlist_count; ?></span>
        </div>
        <div class="item-meta">
            <?= view('product/_price_product_item', ['product' => $product]); ?>
        </div>
    </div>
</div>
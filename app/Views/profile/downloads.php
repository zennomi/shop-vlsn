<?= view('profile/_cover_image'); ?>
    <div id="wrapper">
        <div class="container">
            <?php if (empty($user->cover_image)): ?>
                <div class="row">
                    <div class="col-12">
                        <nav class="nav-breadcrumb" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= langBaseUrl(); ?>"><?= trans("home"); ?></a></li>
                                <li class="breadcrumb-item active" aria-current="page"><?= trans("profile"); ?></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            <?php endif; ?>
            <div class="row">
                <div class="col-12">
                    <div class="profile-page-top">
                        <?= view('profile/_profile_user_info'); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <?= view('profile/_tabs'); ?>
                </div>
                <div class="col-12">
                    <div class="profile-tab-content">
                        <?php if (!empty($items)):
                            foreach ($items as $item):
                                $product = getActiveProduct($item->product_id);
                                if (!empty($product)):?>
                                    <div class="product-item product-item-horizontal">
                                        <div class="row">
                                            <div class="col-12 col-sm-5 col-md-4 col-lg-3 col-mds-5">
                                                <div class="item-image">
                                                    <a href="<?= generateProductUrl($product); ?>">
                                                        <div class="img-product-container">
                                                            <img src="<?= base_url(IMG_BG_PRODUCT_SMALL); ?>" data-src="<?= getProductMainImage($product->id, 'image_small'); ?>" alt="<?= getProductTitle($product); ?>" class="lazyload img-fluid img-product" onerror="this.src='<?= base_url(IMG_BG_PRODUCT_SMALL); ?>'">
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-7 col-md-8 col-lg-9">
                                                <div class="row-custom item-details">
                                                    <h3 class="product-title m-0">
                                                        <a href="<?= generateProductUrl($product); ?>"><?= getProductTitle($product); ?></a>
                                                    </h3>
                                                    <p class="product-user text-truncate m-t-0">
                                                        <a href="<?= generateProfileUrl($product->user_slug); ?>"><?= esc($product->user_username); ?></a>
                                                    </p>
                                                    <?php if ($generalSettings->reviews == 1) {
                                                        echo view('partials/_review_stars', ['rating' => $product->rating]);
                                                    } ?>
                                                    <div class="item-meta m-t-5">
                                                        <?= view('product/_price_product_item', ['product' => $product]); ?>
                                                    </div>
                                                </div>
                                                <div class="row-custom m-t-15 m-b-15">
                                                    <form action="<?= base_url('download-purchased-digital-file-post'); ?>" method="post">
                                                        <?= csrf_field(); ?>
                                                        <input type="hidden" name="sale_id" value="<?= $item->id; ?>">
                                                        <?php if ($product->listing_type == 'license_key'): ?>
                                                            <button name="submit" value="license_certificate" class="btn btn-md btn-custom"><i class="icon-download-solid"></i><?= trans("download_license_key"); ?></button>
                                                        <?php else: ?>
                                                            <div class="btn-group btn-group-download">
                                                                <button type="button" class="btn btn-md btn-custom dropdown-toggle" data-toggle="dropdown">
                                                                    <i class="icon-download-solid"></i><?= trans("download"); ?>&nbsp;&nbsp;<i class="icon-arrow-down m-0"></i>
                                                                </button>
                                                                <div class="dropdown-menu">
                                                                    <button name="submit" value="main_files" class="dropdown-item"><?= trans("main_files"); ?></button>
                                                                    <button name="submit" value="license_certificate" class="dropdown-item"><?= trans("license_certificate"); ?></button>
                                                                </div>
                                                            </div>
                                                        <?php endif; ?>
                                                    </form>
                                                </div>
                                                <?php if ($generalSettings->reviews == 1 && $item->seller_id != $item->buyer_id): ?>
                                                    <div class="row-custom">
                                                        <div class="rate-product">
                                                            <p class="p-rate-product"><?= trans("rate_this_product"); ?></p>
                                                            <div class="rating-stars">
                                                                <?php $review = getReview($item->product_id, user()->id); ?>
                                                                <label class="label-star label-star-open-modal" data-star="5" data-product-id="<?= $item->product_id; ?>" data-toggle="modal" data-target="#rateProductModal"><i class="<?= !empty($review) && $review->rating >= 5 ? 'icon-star' : 'icon-star-o'; ?>"></i></label>
                                                                <label class="label-star label-star-open-modal" data-star="4" data-product-id="<?= $item->product_id; ?>" data-toggle="modal" data-target="#rateProductModal"><i class="<?= !empty($review) && $review->rating >= 4 ? 'icon-star' : 'icon-star-o'; ?>"></i></label>
                                                                <label class="label-star label-star-open-modal" data-star="3" data-product-id="<?= $item->product_id; ?>" data-toggle="modal" data-target="#rateProductModal"><i class="<?= !empty($review) && $review->rating >= 3 ? 'icon-star' : 'icon-star-o'; ?>"></i></label>
                                                                <label class="label-star label-star-open-modal" data-star="2" data-product-id="<?= $item->product_id; ?>" data-toggle="modal" data-target="#rateProductModal"><i class="<?= !empty($review) && $review->rating >= 2 ? 'icon-star' : 'icon-star-o'; ?>"></i></label>
                                                                <label class="label-star label-star-open-modal" data-star="1" data-product-id="<?= $item->product_id; ?>" data-toggle="modal" data-target="#rateProductModal"><i class="<?= !empty($review) && $review->rating >= 1 ? 'icon-star' : 'icon-star-o'; ?>"></i></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif;
                            endforeach;
                        else:?>
                            <p class="text-center text-muted"><?= trans("msg_dont_have_downloadable_files"); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="product-list-pagination">
                        <?= view('partials/_pagination'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= view('partials/_modal_rate_product'); ?>
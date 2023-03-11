<?= view('profile/_cover_image'); ?>
    <div id="wrapper">
        <div class="container">
            <?php if (empty($user->cover_image)): ?>
                <div class="row">
                    <div class="col-12">
                        <nav class="nav-breadcrumb" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= langBaseUrl(); ?>"><?= trans("home"); ?></a></li>
                                <li class="breadcrumb-item active" aria-current="page"><?= trans("wishlist"); ?></li>
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
                        <div class="row row-product-items row-product">
                            <?php if (!empty($products)):
                                foreach ($products as $product): ?>
                                    <div class="col-6 col-sm-4 col-md-3 col-mds-5 col-product">
                                        <?= view('product/_product_item', ['product' => $product, 'promotedBadge' => true]); ?>
                                    </div>
                                <?php endforeach;
                            else:?>
                                <div class="col-12">
                                    <p class="text-center text-muted"><?= trans("no_products_found"); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="product-list-pagination">
                        <?= view('partials/_pagination'); ?>
                    </div>
                    <div class="row-custom">
                        <?= view('partials/_ad_spaces', ['adSpace' => 'profile', 'class' => 'm-t-30']); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= view('partials/_modal_send_message', ['subject' => null]); ?>
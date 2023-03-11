<div id="wrapper">
    <div class="container">
        <div class="row">

            <div class="col-12">
                <nav class="nav-breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= langBaseUrl(); ?>"><?= trans("home"); ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= $title; ?></li>
                    </ol>
                </nav>
                <h1 class="page-title"><?= $title; ?></h1>
            </div>
            <div class="col-12">
                <div class="page-contact">
                    <div class="row">
                        <?php if (!empty($products)):
                            foreach ($products as $product): ?>
                                <div class="col-6 col-sm-4 col-md-3 col-mds-5 col-product">
                                    <?= view('product/_product_item', ['product' => $product, 'promotedBadge' => false]); ?>
                                </div>
                            <?php endforeach;
                        else: ?>
                            <div class="col-12">
                                <p class="text-center"><?= trans("no_products_found"); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="row">
                    <?= view('partials/_pagination'); ?>
                </div>
            </div>
        </div>
    </div>
</div>
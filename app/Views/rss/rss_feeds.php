<div id="wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="blog-content">
                    <nav class="nav-breadcrumb" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= langBaseUrl(); ?>"><?= trans("home"); ?></a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?= esc($title); ?></li>
                        </ol>
                    </nav>
                    <h1 class="page-title"><?= esc($title); ?></h1>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="page-text-content">
                                <div class="rss-item">
                                    <div class="left">
                                        <a href="<?= langBaseUrl(); ?>/rss/<?= getRoute("latest_products"); ?>" target="_blank"><i class="icon-rss"></i>&nbsp;&nbsp;<?= trans("latest_products"); ?></a>
                                    </div>
                                    <div class="right">
                                        <p><?= langBaseUrl() . '/rss/' . getRoute("latest_products"); ?></p>
                                    </div>
                                </div>
                                <div class="rss-item">
                                    <div class="left">
                                        <a href="<?= langBaseUrl(); ?>/rss/<?= getRoute("featured_products"); ?>" target="_blank"><i class="icon-rss"></i>&nbsp;&nbsp;<?= trans("featured_products"); ?></a>
                                    </div>
                                    <div class="right">
                                        <p><?= langBaseUrl() . '/rss/' . getRoute("featured_products"); ?></p>
                                    </div>
                                </div>
                                <?php if (!empty($parentCategories)):
                                    foreach ($parentCategories as $category): ?>
                                        <div class="rss-item">
                                            <div class="left">
                                                <a href="<?= langBaseUrl(); ?>/rss/<?= getRoute("category"); ?>/<?= esc($category->slug); ?>" target="_blank"><i class="icon-rss"></i>&nbsp;&nbsp;<?= getCategoryName($category); ?></a>
                                            </div>
                                            <div class="right">
                                                <p><?= langBaseUrl(); ?>/rss/<?= getRoute("category"); ?>/<?= esc($category->slug); ?></p>
                                            </div>
                                        </div>
                                    <?php
                                    endforeach;
                                endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
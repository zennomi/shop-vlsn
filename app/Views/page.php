<div id="wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="blog-content">
                    <nav class="nav-breadcrumb" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= langBaseUrl(); ?>"><?= trans("home"); ?></a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?= esc($page->title); ?></li>
                        </ol>
                    </nav>
                    <?php if ($page->title_active == 1): ?>
                        <h1 class="page-title"><?= esc($page->title); ?></h1>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="page-text-content post-text-responsive">
                                <?= $page->page_content; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
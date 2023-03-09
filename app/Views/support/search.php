<div id="wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="support">
                    <nav class="nav-breadcrumb" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= langBaseUrl(); ?>"><?= trans("home"); ?></a></li>
                            <li class="breadcrumb-item"><a href="<?= generateUrl('help_center'); ?>"><?= trans("help_center"); ?></a></li>
                        </ol>
                    </nav>
                    <h1 class="title-category"><?= trans("search_results"); ?>:&nbsp;<span><?= esc($q); ?></span>
                        <?php if (!empty($contents)): ?>
                            <br><span class="number-of-results"><?= trans("number_of_results") . ': ' . $numRows; ?></span>
                        <?php endif; ?>
                    </h1>
                    <div class="row">
                        <div class="col-12">
                            <ul class="support-search-results">
                                <?php if (!empty($contents)):
                                    foreach ($contents as $item): ?>
                                        <li>
                                            <div class="title">
                                                <a href="<?= generateUrl('help_center') . '/' . esc($item->category_slug) . '/' . esc($item->slug); ?>"><?= esc($item->title); ?></a>
                                            </div>
                                            <div class="category">
                                                <a href="<?= generateUrl('help_center') . '/' . esc($item->category_slug); ?>"><?= esc($item->category_name); ?></a>
                                            </div>
                                        </li>
                                    <?php endforeach;
                                endif; ?>
                            </ul>
                            <?php if (empty($contents)): ?>
                                <p class="text-center text-muted m-t-15">
                                    <?= trans("no_results_found"); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 m-t-30">
                            <div class="float-left">
                                <div class="all-help-topics">
                                    <a href="<?= generateUrl('help_center'); ?>"><i class="icon-angle-left"></i><?= trans("all_help_topics"); ?></a>
                                </div>
                            </div>
                            <div class="float-right">
                                <?= view('partials/_pagination'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
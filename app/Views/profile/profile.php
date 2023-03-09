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
            <?php if (isVendor($user) && $generalSettings->multi_vendor_system == 1): ?>
                <div class="col-12">
                    <div class="profile-tab-content">
                        <div class="row">
                            <div class="col-12 col-md-3 col-sidebar-products">
                                <div id="collapseFilters" class="product-filters">
                                    <div class="filter-item">
                                        <div class="profile-search">
                                            <input type="search" name="search" id="input_search_vendor" class="form-control form-input profile-search" placeholder="<?= trans("search"); ?>">
                                            <button id="btn_search_vendor" class="btn btn-default btn-search" data-current-url="<?= current_url(); ?>" data-query-string="<?= generateFilterUrl($queryStringArray, 'rmv_psrc', ''); ?>"><i class="icon-search"></i></button>
                                        </div>
                                    </div>
                                    <?php if (!empty($categories) && !empty($categories[0])):
                                        $categoryId = 0; ?>
                                        <div class="filter-item">
                                            <h4 class="title"><?= trans("category"); ?></h4>
                                            <?php if (!empty($category)):
                                                $categoryId = $category->id;
                                                $url = generateProfileUrl($user->slug) . generateFilterUrl($queryStringArray, 'rmv_p_cat', '');
                                                if (!empty($parent_category)) {
                                                    $url = generateProfileUrl($user->slug) . generateFilterUrl($queryStringArray, 'p_cat', $parent_category->id);
                                                } ?>
                                                <a href="<?= $url . '#products'; ?>" class="filter-list-categories-parent">
                                                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-left-short" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5z"/>
                                                    </svg>
                                                    <span><?= getCategoryName($category); ?></span>
                                                </a>
                                            <?php endif; ?>
                                            <div class="filter-list-container">
                                                <ul class="filter-list filter-custom-scrollbar<?= !empty($category) ? ' filter-list-subcategories' : ' filter-list-categories'; ?>">
                                                    <?php foreach ($categories as $item):
                                                        if ($categoryId != $item->id):?>
                                                            <li>
                                                                <a href="<?= generateProfileUrl($user->slug) . generateFilterUrl($queryStringArray, 'p_cat', $item->id) . '#products'; ?>" <?= !empty($category) && $category->id == $item->id ? 'class="active"' : ''; ?>><?= getCategoryName($item); ?></a>
                                                            </li>
                                                        <?php endif;
                                                    endforeach; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    <?php endif;
                                    if ($generalSettings->marketplace_system == 1 || $generalSettings->bidding_system == 1 || $productSettings->classified_price == 1):
                                        $filterPmin = clrNum(inputGet('p_min'));
                                        $filterPmax = clrNum(inputGet('p_max')); ?>
                                        <div class="filter-item">
                                            <h4 class="title"><?= trans("price"); ?></h4>
                                            <div class="price-filter-inputs">
                                                <div class="row align-items-baseline row-price-inputs">
                                                    <div class="col-4 col-md-4 col-lg-5 col-price-inputs">
                                                        <span><?= trans("min"); ?></span>
                                                        <input type="input" id="price_min" value="<?= !empty($filterPmin) ? $filterPmin : ''; ?>" class="form-control price-filter-input" placeholder="<?= trans("min"); ?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                                    </div>
                                                    <div class="col-4 col-md-4 col-lg-5 col-price-inputs">
                                                        <span><?= trans("max"); ?></span>
                                                        <input type="input" id="price_max" value="<?= !empty($filterPmax) ? $filterPmax : ''; ?>" class="form-control price-filter-input" placeholder="<?= trans("max"); ?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                                    </div>
                                                    <div class="col-4 col-md-4 col-lg-2 col-price-inputs text-left">
                                                        <button type="button" id="btn_filter_price" data-current-url="<?= current_url(); ?>" data-query-string="<?= generateFilterUrl($queryStringArray, 'rmv_prc', ''); ?>" data-page="profile" class="btn btn-sm btn-default btn-filter-price float-left"><i class="icon-arrow-right"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="row-custom m-b-30">
                                    <?php if (authCheck()):
                                        if ($user->id != user()->id):?>
                                            <a href="javascript:void(0)" class="text-muted link-abuse-report" data-toggle="modal" data-target="#reportSellerModal"><?= trans("report_this_seller"); ?></a>
                                        <?php endif;
                                    else: ?>
                                        <a href="javascript:void(0)" class="text-muted link-abuse-report" data-toggle="modal" data-target="#loginModal"><?= trans("report_this_seller"); ?></a>
                                    <?php endif; ?>
                                </div>
                                <div class="row-custom">
                                    <?= view('partials/_ad_spaces', ['adSpace' => 'profile_sidebar', 'class' => 'm-t-30']); ?>
                                </div>
                            </div>
                            <div class="col-12 col-md-9 col-content-products">
                                <div class="row">
                                    <div class="col-12 product-list-header">
                                        <div class="filter-reset-tag-container">
                                            <?php $showResetLink = false;
                                            if (!empty($queryStringObjectArray)):
                                                foreach ($queryStringObjectArray as $filter):
                                                    if ($filter->key != 'sort' && $filter->key != 'p_cat'):
                                                        $showResetLink = true;
                                                        if ($filter->key == 'p_min'): ?>
                                                            <div class="filter-reset-tag">
                                                                <div class="left">
                                                                    <a href="<?= current_url() . generateFilterUrl($queryStringArray, $filter->key, $filter->value) . "#products"; ?>" rel="nofollow"><i class="icon-close"></i></a>
                                                                </div>
                                                                <div class="right">
                                                                    <span class="reset-tag-title"><?= trans("price") . '(' . $selectedCurrency->symbol . ')'; ?></span>
                                                                    <span><?= trans("min") . ': ' . esc($filter->value); ?></span>
                                                                </div>
                                                            </div>
                                                        <?php elseif ($filter->key == 'p_max'): ?>
                                                            <div class="filter-reset-tag">
                                                                <div class="left">
                                                                    <a href="<?= current_url() . generateFilterUrl($queryStringArray, $filter->key, $filter->value) . "#products"; ?>" rel="nofollow"><i class="icon-close"></i></a>
                                                                </div>
                                                                <div class="right">
                                                                    <span class="reset-tag-title"><?= trans("price") . '(' . $selectedCurrency->symbol . ')'; ?></span>
                                                                    <span><?= trans("max") . ': ' . esc($filter->value); ?></span>
                                                                </div>
                                                            </div>
                                                        <?php elseif ($filter->key == 'search'): ?>
                                                            <div class="filter-reset-tag">
                                                                <div class="left">
                                                                    <a href="<?= current_url() . generateFilterUrl($queryStringArray, $filter->key, $filter->value) . "#products"; ?>" rel="nofollow"><i class="icon-close"></i></a>
                                                                </div>
                                                                <div class="right">
                                                                    <span class="reset-tag-title"><?= trans("search"); ?></span>
                                                                    <span><?= esc($filter->value); ?></span>
                                                                </div>
                                                            </div>
                                                        <?php endif;
                                                    endif;
                                                endforeach;
                                            endif;
                                            if ($showResetLink): ?>
                                                <a href="<?= current_url() . "#products"; ?>" class="link-reset-filters" rel="nofollow"><?= trans("reset_filters"); ?></a>
                                            <?php endif; ?>
                                        </div>

                                        <div class="product-sort-by">
                                            <span class="span-sort-by"><?= trans("sort_by"); ?></span>
                                            <?php $filterSort = inputGet('sort'); ?>
                                            <div class="sort-select">
                                                <select id="select_sort_items" class="custom-select" data-current-url="<?= current_url(); ?>" data-query-string="<?= generateFilterUrl($queryStringArray, 'rmv_srt', ''); ?>" data-page="profile">
                                                    <option value="most_recent"<?= $filterSort == 'most_recent' ? ' selected' : ''; ?>><?= trans("most_recent"); ?></option>
                                                    <option value="lowest_price"<?= $filterSort == 'lowest_price' ? ' selected' : ''; ?>><?= trans("lowest_price"); ?></option>
                                                    <option value="highest_price"<?= $filterSort == 'highest_price' ? ' selected' : ''; ?>><?= trans("highest_price"); ?></option>
                                                    <option value="rating"<?= $filterSort == 'rating' ? ' selected' : ''; ?>><?= trans("highest_rating"); ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <button class="btn btn-filter-products-mobile" type="button" data-toggle="collapse" data-target="#collapseFilters" aria-expanded="false" aria-controls="collapseFilters">
                                            <i class="icon-filter"></i>&nbsp;<?= trans("filter_products"); ?>
                                        </button>
                                    </div>
                                </div>
                                <div class="product-list-content">
                                    <div class="row row-product">
                                        <?php $i = 0;
                                        if (!empty($products)):
                                            foreach ($products as $product):
                                                if ($i == 8):
                                                    echo view('partials/_ad_spaces', ['adSpace' => 'products_1', 'class' => 'mb-4']);
                                                endif; ?>
                                                <div class="col-6 col-sm-4 col-md-4 col-lg-3 col-product">
                                                    <?= view('product/_product_item', ['product' => $product, 'promoted_badge' => true]); ?>
                                                </div>
                                                <?php $i++;
                                            endforeach;
                                        endif;
                                        if (empty($products)): ?>
                                            <div class="col-12">
                                                <p class="no-records-found"><?= trans("no_products_found"); ?></p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?= view('partials/_ad_spaces', ['adSpace' => 'products_2', 'class' => 'mt-3']); ?>
                                <div class="product-list-pagination">
                                    <div class="float-right">
                                        <?= view('partials/_pagination'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="col-12">
                    <div class="profile-tab-content"></div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (authCheck() && !empty($user) && $user->id != user()->id): ?>
    <div class="modal fade" id="reportSellerModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content modal-custom modal-report-abuse">
                <form id="form_report_seller" method="post">
                    <input type="hidden" name="id" value="<?= $user->id; ?>">
                    <div class="modal-header">
                        <h5 class="modal-title"><?= trans("report_this_seller"); ?></h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true"><i class="icon-close"></i> </span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div id="response_form_report_seller" class="col-12"></div>
                            <div class="col-12">
                                <div class="form-group m-0">
                                    <label class="control-label"><?= trans("description"); ?></label>
                                    <textarea name="description" class="form-control form-textarea" placeholder="<?= trans("abuse_report_exp"); ?>" minlength="5" maxlength="10000" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer text-right">
                        <button type="submit" class="btn btn-md btn-custom"><?= trans("submit"); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    var pagination_links = document.querySelectorAll(".pagination a");
    var i;
    for (i = 0; i < pagination_links.length; i++) {
        pagination_links[i].href = pagination_links[i].href + "#products";
    }
</script>


<div id="wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="nav-breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-products">
                        <li class="breadcrumb-item"><a href="<?= langBaseUrl(); ?>"><?= trans("home"); ?></a></li>
                        <?php if (!empty($parentCategoriesTree)):
                            foreach ($parentCategoriesTree as $item):
                                if ($item->id == $category->id):?>
                                    <li class="breadcrumb-item active"><?= getCategoryName($item); ?></li>
                                <?php else: ?>
                                    <li class="breadcrumb-item"><a href="<?= generateCategoryUrl($item); ?>"><?= getCategoryName($item); ?></a></li>
                                <?php endif;
                            endforeach;
                        else:?>
                            <li class="breadcrumb-item active"><?= trans("products"); ?></li>
                        <?php endif; ?>
                    </ol>
                </nav>
            </div>
        </div>
        <?php $search = cleanStr(inputGet('search'));
        if (!empty($search)):?>
            <input type="hidden" name="search" value="<?= esc($search); ?>">
        <?php endif; ?>
        <div class="row">
            <div class="col-12 product-list-header">
                <?php if (!empty($category)): ?>
                    <h1 class="page-title product-list-title"><?= getCategoryName($category); ?></h1>
                <?php else: ?>
                    <h1 class="page-title product-list-title"><?= trans("products"); ?></h1>
                <?php endif; ?>
                <div class="product-sort-by">
                    <span class="span-sort-by"><?= trans("sort_by"); ?></span>
                    <?php $filterSort = strSlug(inputGet('sort')); ?>
                    <div class="sort-select">
                        <select id="select_sort_items" class="custom-select" data-current-url="<?= current_url(); ?>" data-query-string="<?= generateFilterUrl($queryStringArray, 'rmv_srt', ''); ?>" data-page="products">
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
        <div class="row">
            <?php $arrayOptionNames = array(); ?>
            <div class="col-12 col-md-3 col-sidebar-products">
                <div id="collapseFilters" class="product-filters">
                    <?php if (!empty($category) || !empty($categories)): ?>
                        <div class="filter-item">
                            <h4 class="title"><?= trans("category"); ?></h4>
                            <?php if (!empty($category)):
                                $url = generateUrl("products");
                                if (!empty($parentCategory)) {
                                    $url = generateCategoryUrl($parentCategory);
                                } ?>
                                <a href="<?= $url . generateFilterUrl($queryStringArray, '', ''); ?>" class="filter-list-categories-parent">
                                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-left-short" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5z"/>
                                    </svg>
                                    <span><?= getCategoryName($category); ?></span>
                                </a>
                            <?php endif;
                            if (countItems($categories) > 0): ?>
                                <div class="filter-list-container">
                                    <ul class="filter-list filter-custom-scrollbar<?= !empty($category) ? ' filter-list-subcategories' : ' filter-list-categories'; ?>">
                                        <?php foreach ($categories as $item): ?>
                                            <li>
                                                <a href="<?= generateCategoryUrl($item) . generateFilterUrl($queryStringArray, '', ''); ?>" <?= !empty($category) && $category->id == $item->id ? 'class="active"' : ''; ?>><?= getCategoryName($item); ?></a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif;

                    $arrayFieldNames = array();
                    if (!empty($customFilters)):
                        foreach ($customFilters as $customFilter):
                            $filterName = @parseSerializedNameArray($customFilter->name_array, selectedLangId());
                            @$arrayFieldNames[$customFilter->product_filter_key] = $filterName;
                            $options = getProductFiltersOptions($customFilter, selectedLangId(), $customFilters, $queryStringArray);
                            if (!empty($options)): ?>
                                <div class="filter-item">
                                    <h4 class="title"><?= esc($filterName); ?></h4>
                                    <div class="filter-list-container">
                                        <?php if (countItems($options) > 11): ?>
                                            <input type="text" class="form-control filter-search-input" placeholder="<?= trans("search") . ' ' . esc($filterName); ?>" data-filter-id="product_filter_<?= $customFilter->id; ?>">
                                        <?php endif; ?>
                                        <ul id="product_filter_<?= $customFilter->id; ?>" class="filter-list filter-custom-scrollbar">
                                            <?php foreach ($options as $option):
                                                $optionName = getCustomFieldOptionName($option);
                                                @$arrayOptionNames[$customFilter->product_filter_key . '_' . $option->option_key] = $optionName; ?>
                                                <li>
                                                    <a href="<?= current_url() . generateFilterUrl($queryStringArray, $customFilter->product_filter_key, $option->option_key); ?>" rel="nofollow">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" <?= isCustomFieldOptionSelected($queryStringObjectArray, $customFilter->product_filter_key, $option->option_key) ? 'checked' : ''; ?>>
                                                            <label class="custom-control-label"><?= esc($optionName); ?></label>
                                                        </div>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            <?php endif;
                        endforeach;
                    endif;
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
                                        <button type="button" id="btn_filter_price" data-current-url="<?= current_url(); ?>" data-query-string="<?= generateFilterUrl($queryStringArray, 'rmv_prc', ''); ?>" data-page="products" class="btn btn-sm btn-default btn-filter-price float-left"><i class="icon-arrow-right"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="row-custom">
                    <?= view('partials/_ad_spaces', ['adSpace' => 'products_sidebar', 'class' => 'm-b-15']); ?>
                </div>
            </div>
            <div class="col-12 col-md-9 col-content-products">
                <div class="filter-reset-tag-container">
                    <?php $showResetLink = false;
                    if (!empty($queryStringObjectArray)):
                        foreach ($queryStringObjectArray as $filter):
                            if ($filter->key != 'sort'):
                                $showResetLink = true;
                                if ($filter->key == 'p_min'): ?>
                                    <div class="filter-reset-tag">
                                        <div class="left">
                                            <a href="<?= current_url() . generateFilterUrl($queryStringArray, $filter->key, $filter->value); ?>" rel="nofollow"><i class="icon-close"></i></a>
                                        </div>
                                        <div class="right">
                                            <span class="reset-tag-title"><?= trans("price") . '(' . $selectedCurrency->symbol . ')'; ?></span>
                                            <span><?= trans("min") . ': ' . esc($filter->value); ?></span>
                                        </div>
                                    </div>
                                <?php elseif ($filter->key == "p_max"): ?>
                                    <div class="filter-reset-tag">
                                        <div class="left">
                                            <a href="<?= current_url() . generateFilterUrl($queryStringArray, $filter->key, $filter->value); ?>" rel="nofollow"><i class="icon-close"></i></a>
                                        </div>
                                        <div class="right">
                                            <span class="reset-tag-title"><?= trans("price") . '(' . $selectedCurrency->symbol . ')'; ?></span>
                                            <span><?= trans("max") . ': ' . esc($filter->value); ?></span>
                                        </div>
                                    </div>
                                <?php elseif ($filter->key == "search"): ?>
                                    <div class="filter-reset-tag">
                                        <div class="left">
                                            <a href="<?= current_url() . generateFilterUrl($queryStringArray, $filter->key, $filter->value); ?>" rel="nofollow"><i class="icon-close"></i></a>
                                        </div>
                                        <div class="right">
                                            <span class="reset-tag-title"><?= trans("search"); ?></span>
                                            <span><?= esc($filter->value); ?></span>
                                        </div>
                                    </div>
                                <?php else:
                                    if (!empty($arrayOptionNames[$filter->key . '_' . $filter->value])):?>
                                        <div class="filter-reset-tag">
                                            <div class="left">
                                                <a href="<?= current_url() . generateFilterUrl($queryStringArray, $filter->key, $filter->value); ?>" rel="nofollow"><i class="icon-close"></i></a>
                                            </div>
                                            <div class="right">
                                                <span class="reset-tag-title"><?= isset($arrayFieldNames[$filter->key]) ? $arrayFieldNames[$filter->key] : ucfirst($filter->key); ?></span>
                                                <span><?= $arrayOptionNames[$filter->key . '_' . $filter->value]; ?></span>
                                            </div>
                                        </div>
                                    <?php endif;
                                endif;
                            endif;
                        endforeach;
                    endif;
                    if ($showResetLink): ?>
                        <a href="<?= current_url(); ?>" class="link-reset-filters" rel="nofollow"><?= trans("reset_filters"); ?></a>
                    <?php endif; ?>
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
                                    <?= view('product/_product_item', ['product' => $product, 'promotedBadge' => true]); ?>
                                </div>
                                <?php $i++;
                            endforeach;
                        else: ?>
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
<?php $formAction = adminUrl('products?list=' . esc($list));
if ($list == 'featured') {
    $formAction = adminUrl('featured-products');
} ?>
<div class="row table-filter-container">
    <div class="col-sm-12">
        <form action="<?= $formAction; ?>" method="get">
            <?php if ($list != 'featured'): ?>
                <input type="hidden" name="list" value="<?= esc($list); ?>">
            <?php endif; ?>
            <div class="item-table-filter" style="width: 80px; min-width: 80px;">
                <label><?= trans("show"); ?></label>
                <select name="show" class="form-control">
                    <option value="15" <?= inputGet('show', true) == '15' ? 'selected' : ''; ?>>15</option>
                    <option value="30" <?= inputGet('show', true) == '30' ? 'selected' : ''; ?>>30</option>
                    <option value="60" <?= inputGet('show', true) == '60' ? 'selected' : ''; ?>>60</option>
                    <option value="100" <?= inputGet('show', true) == '100' ? 'selected' : ''; ?>>100</option>
                </select>
            </div>
            <div class="item-table-filter">
                <label><?= trans('product_type'); ?></label>
                <select name="product_type" class="form-control custom-select">
                    <option value="" selected><?= trans("all"); ?></option>
                    <option value="physical" <?= inputGet('product_type') == 'physical' ? 'selected' : ''; ?>><?= trans("physical"); ?></option>
                    <option value="digital" <?= inputGet('product_type') == 'digital' ? 'selected' : ''; ?>><?= trans("digital"); ?></option>
                </select>
            </div>
            <div class="item-table-filter">
                <label><?= trans('category'); ?></label>
                <select id="categories" name="category" class="form-control" onchange="getFilterSubCategories(this.value);">
                    <option value=""><?= trans("all"); ?></option>
                    <?php $categories = $categoryModel->getParentCategories();
                    foreach ($categories as $item): ?>
                        <option value="<?= $item->id; ?>" <?= inputGet('category', true) == $item->id ? 'selected' : ''; ?>>
                            <?= getCategoryName($item); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="item-table-filter">
                <div class="form-group">
                    <label class="control-label"><?= trans('subcategory'); ?></label>
                    <select id="subcategories" name="subcategory" class="form-control">
                        <option value=""><?= trans("all"); ?></option>
                        <?php if (!empty(inputGet('category'))):
                            $subCategories = $categoryModel->getSubCategoriesByParentId(inputGet('category'));
                            if (!empty($subCategories)):
                                foreach ($subCategories as $item):?>
                                    <option value="<?= $item->id; ?>" <?= inputGet('subcategory') == $item->id ? 'selected' : ''; ?>><?= esc($item->name); ?></option>
                                <?php endforeach;
                            endif;
                        endif; ?>
                    </select>
                </div>
            </div>
            <div class="item-table-filter">
                <label><?= trans('stock'); ?></label>
                <select name="stock" class="form-control custom-select">
                    <option value="" selected><?= trans("all"); ?></option>
                    <option value="in_stock" <?= inputGet("stock") == 'in_stock' ? 'selected' : ''; ?>><?= trans("in_stock"); ?></option>
                    <option value="out_of_stock" <?= inputGet("stock") == 'out_of_stock' ? 'selected' : ''; ?>><?= trans("out_of_stock"); ?></option>
                </select>
            </div>
            <div class="item-table-filter">
                <label><?= trans("search"); ?></label>
                <input name="q" class="form-control" placeholder="<?= trans("search"); ?>" type="search" value="<?= esc(inputGet('q')); ?>">
            </div>
            <div class="item-table-filter md-top-10" style="width: 65px; min-width: 65px;">
                <label style="display: block">&nbsp;</label>
                <button type="submit" class="btn bg-purple"><?= trans("filter"); ?></button>
            </div>
        </form>
    </div>
</div>
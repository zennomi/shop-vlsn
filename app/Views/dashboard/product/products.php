<div class="box">
    <div class="box-header with-border">
        <div class="left">
            <h3 class="box-title"><?= esc($title); ?></h3>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <div class="row table-filter-container">
                        <div class="col-sm-12">
                            <form action="<?= generateDashUrl('products'); ?>" method="get">
                                <?php if (!empty(inputGet('st'))): ?>
                                    <input type="hidden" name="st" value="<?= strSlug(inputGet('st')); ?>">
                                <?php endif; ?>
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
                                    <select id="categories" name="category" class="form-control custom-select" onchange="getFilterSubCategoriesDashboard(this.value);">
                                        <option value=""><?= trans("all"); ?></option>
                                        <?php if (!empty($parentCategories)):
                                            foreach ($parentCategories as $item): ?>
                                                <option value="<?= $item->id; ?>" <?= inputGet('category', true) == $item->id ? 'selected' : ''; ?>><?= getCategoryName($item); ?></option>
                                            <?php endforeach;
                                        endif; ?>
                                    </select>
                                </div>
                                <div class="item-table-filter">
                                    <div class="form-group">
                                        <label class="control-label"><?= trans('subcategory'); ?></label>
                                        <select id="subcategories" name="subcategory" class="form-control custom-select">
                                            <option value=""><?= trans("all"); ?></option>
                                            <?php if (!empty(inputGet('category'))):
                                                $subCategories = getSubCategories(inputGet('category'));
                                                if (!empty($subCategories)):
                                                    foreach ($subCategories as $item):?>
                                                        <option value="<?= $item->id; ?>" <?= inputGet('subcategory', true) == $item->id ? 'selected' : ''; ?>><?= esc($item->name); ?></option>
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
                                    <button type="submit" class="btn bg-purple btn-filter"><?= trans("filter"); ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <table class="table table-bordered table-striped table-products" role="grid">
                        <thead>
                        <tr role="row">
                            <th width="20"><?= trans('id'); ?></th>
                            <th><?= trans('product'); ?></th>
                            <th><?= trans('sku'); ?></th>
                            <th><?= trans('product_type'); ?></th>
                            <th><?= trans('category'); ?></th>
                            <?php if (!empty($generalSettings->promoted_products)): ?>
                                <th><?= trans('purchased_plan'); ?></th>
                            <?php endif; ?>
                            <th><?= trans("stock") . '/' . trans("status"); ?></th>
                            <th><?= trans('page_views'); ?></th>
                            <th><?= trans('date'); ?></th>
                            <th class="max-width-120"><?= trans('options'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($products)):
                            foreach ($products as $item): ?>
                                <tr>
                                    <td><?= esc($item->id); ?></td>
                                    <td class="td-product">
                                        <?php if ($item->is_promoted == 1): ?>
                                            <label class="label label-success"><?= trans("featured"); ?></label>
                                        <?php endif; ?>
                                        <div class="img-table">
                                            <a href="<?= generateProductUrl($item); ?>" target="_blank">
                                                <img src="<?= getProductMainImage($item->id, 'image_small'); ?>" data-src="" alt="" class="lazyload img-responsive post-image"/>
                                            </a>
                                        </div>
                                        <a href="<?= generateProductUrl($item); ?>" target="_blank" class="table-product-title"><?= getProductTitle($item); ?></a>
                                    </td>
                                    <td><?= esc($item->sku); ?></td>
                                    <td><?= trans($item->product_type); ?></td>
                                    <td>
                                        <?php $category = getCategory($item->category_id);
                                        if (!empty($category)) {
                                            echo esc($category->name);
                                        } ?>
                                    </td>
                                    <?php if (!empty($generalSettings->promoted_products)): ?>
                                        <td>
                                            <?php if ($item->is_draft != 1):
                                                if ($item->is_promoted == 1 && $item->promote_plan != 'none'):
                                                    echo esc($item->promote_plan);
                                                else: ?>
                                                    <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalPricing" onclick="$('.pricing_product_id').val(<?= $item->id; ?>);"><i class="fa fa-plus"></i>&nbsp;<?= trans("promote"); ?></button>
                                                <?php endif;
                                            endif; ?>
                                        </td>
                                    <?php endif; ?>
                                    <td class="white-space-nowrap">
                                        <div class="m-b-5"><?= getProductStockStatus($item); ?></div>
                                        <?php if (!empty($productListStatus) && $productListStatus == 'pending'):
                                            if ($item->is_rejected == 1): ?>
                                                <div class="m-b-5">
                                                    <label class="label label-danger"><?= trans("rejected"); ?></label>
                                                </div>
                                                <button type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="#modalReason<?= $item->id; ?>"><i class="fa fa-info-circle"></i>&nbsp;&nbsp;<?= trans("show_reason"); ?></button>
                                                <div id="modalReason<?= $item->id; ?>" class="modal fade" role="dialog">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                                                                <h4 class="modal-title"><?= trans("reason"); ?></h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p class="m-t-10"><?= esc($item->reject_reason); ?></p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default" data-dismiss="modal"><?= trans("close"); ?></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <label class="label label-default"><?= trans("pending"); ?></label>
                                            <?php endif;
                                        endif; ?>
                                    </td>
                                    <td><?= $item->pageviews; ?></td>
                                    <td><?= formatDate($item->created_at); ?></td>
                                    <td style="width: 120px;">
                                        <div class="btn-group btn-group-option">
                                            <a href="<?= generateDashUrl('edit_product') . '/' . $item->id; ?>" class="btn btn-sm btn-default btn-edit" data-toggle="tooltip" title="<?= trans('edit'); ?>"><i class="fa fa-edit"></i></a>
                                            <li><a href="javascript:void(0)" class="btn btn-sm btn-default btn-delete" onclick="deleteItem('DashboardController/deleteProduct','<?= $item->id; ?>','<?= trans("confirm_delete", true); ?>');"><i class="fa fa-trash"></i></a></li>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach;
                        endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if (empty($products)): ?>
                    <p class="text-center">
                        <?= trans("no_records_found"); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?php if (!empty($products)): ?>
                    <div class="number-of-entries">
                        <span><?= trans("number_of_entries"); ?>:</span>&nbsp;&nbsp;<strong><?= $numRows; ?></strong>
                    </div>
                <?php endif; ?>
                <div class="table-pagination">
                    <?= view('partials/_pagination'); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= view('dashboard/product/_modal_promote'); ?>
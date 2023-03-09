<?php $categoryModel = new \App\Models\CategoryModel(); ?>
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?= $title; ?></h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" role="grid">
                        <?= view('admin/product/_filter_products', ['categoryModel' => $categoryModel]); ?>
                        <thead>
                        <tr role="row">
                            <th width="20"><input type="checkbox" class="checkbox-table" id="checkAll"></th>
                            <th width="20"><?= trans('id'); ?></th>
                            <th><?= trans('product'); ?></th>
                            <th><?= trans('sku'); ?></th>
                            <th><?= trans('product_type'); ?></th>
                            <th><?= trans('category'); ?></th>
                            <th><?= trans('user'); ?></th>
                            <th><?= trans('stock'); ?></th>
                            <th><?= trans('page_views'); ?></th>
                            <th><?= trans('date'); ?></th>
                            <th class="max-width-120"><?= trans('options'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($products)):
                            foreach ($products as $item): ?>
                                <tr>
                                    <td><input type="checkbox" name="checkbox-table" class="checkbox-table" value="<?= $item->id; ?>"></td>
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
                                        <a href="<?= generateProductUrl($item); ?>" target="_blank" class="table-product-title">
                                            <?= getProductTitle($item); ?>
                                        </a>
                                    </td>
                                    <td><?= esc($item->sku); ?></td>
                                    <td><?= trans($item->product_type); ?></td>
                                    <td>
                                        <?php $category = $categoryModel->getCategory($item->category_id);
                                        if (!empty($category)) {
                                            echo esc($category->name);
                                        } ?>
                                    </td>
                                    <td>
                                        <?php $user = getUser($item->user_id);
                                        if (!empty($user)): ?>
                                            <a href="<?= generateProfileUrl($user->slug); ?>" target="_blank" class="table-username">
                                                <?= esc(getUsername($user)); ?>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="white-space-nowrap">
                                        <?php if ($item->product_type == "digital"): ?>
                                            <span class="text-success"><?= trans("in_stock"); ?></span>
                                        <?php else:
                                            if ($item->stock < 1): ?>
                                                <span class="text-danger"><?= $item->listing_type == 'ordinary_listing' ? trans("sold") : trans("out_of_stock"); ?></span>
                                            <?php else: ?>
                                                <span class="text-success"><?= trans("in_stock"); ?>&nbsp;<?= $item->listing_type != 'ordinary_listing' ? '(' . $item->stock . ')' : ''; ?></span>
                                            <?php endif;
                                        endif; ?>
                                    </td>
                                    <td><?= $item->pageviews; ?></td>
                                    <td><?= formatDate($item->created_at); ?></td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn bg-purple dropdown-toggle btn-select-option" type="button" data-toggle="dropdown"><?= trans("select_option"); ?>
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu options-dropdown">
                                                <li>
                                                    <a href="<?= adminUrl('product-details/' . $item->id); ?>"><i class="fa fa-info option-icon"></i><?= trans("view_details"); ?></a>
                                                </li>
                                                <?php if ($item->is_promoted == 1): ?>
                                                    <li>
                                                        <a href="javascript:void(0)" onclick="removeFromFeatured('<?= esc($item->id); ?>');"><i class="fa fa-minus option-icon"></i><?= trans("remove_from_featured"); ?></a>
                                                    </li>
                                                <?php else: ?>
                                                    <li>
                                                        <a href="javascript:void(0)" onclick="$('#day_count_product_id').val('<?= esc($item->id); ?>');" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus option-icon"></i><?= trans('add_to_featured'); ?></a>
                                                    </li>
                                                <?php endif;
                                                if ($item->is_special_offer == 1): ?>
                                                    <li>
                                                        <a href="javascript:void(0)" onclick="addRemoveSpecialOffer('<?= esc($item->id); ?>');"><i class="fa fa-minus option-icon"></i><?= trans("remove_from_special_offers"); ?></a>
                                                    </li>
                                                <?php else: ?>
                                                    <li>
                                                        <a href="javascript:void(0)" onclick="addRemoveSpecialOffer('<?= esc($item->id); ?>');"><i class="fa fa-plus option-icon"></i><?= trans('add_to_special_offers'); ?></a>
                                                    </li>
                                                <?php endif; ?>
                                                <li>
                                                    <a href="<?= generateDashUrl('edit_product') . '/' . $item->id; ?>" target="_blank"><i class="fa fa-edit option-icon"></i><?= trans("edit"); ?></a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)" onclick="deleteItem('ProductController/deleteProduct','<?= $item->id; ?>','<?= trans("confirm_product", true); ?>');"><i class="fa fa-times option-icon"></i><?= trans('delete'); ?></a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)" onclick="deleteItem('ProductController/deleteProductPermanently','<?= $item->id; ?>','<?= trans("confirm_product_permanent", true); ?>');"><i class="fa fa-trash option-icon"></i><?= trans('delete_permanently'); ?></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach;
                        endif; ?>
                        </tbody>
                    </table>
                    <?php if (empty($products)): ?>
                        <p class="text-center">
                            <?= trans("no_records_found"); ?>
                        </p>
                    <?php endif; ?>
                    <div class="col-sm-12 table-ft">
                        <div class="row">
                            <div class="pull-right">
                                <?= view('partials/_pagination'); ?>
                            </div>
                            <?php if (countItems($products) > 0): ?>
                                <div class="pull-left">
                                    <button class="btn btn-sm btn-danger btn-table-delete" onclick="deleteSelectedProducts('<?= trans("confirm_products", true); ?>');"><?= trans('delete'); ?></button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('ProductController/addRemoveFeaturedProduct'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?= trans('add_to_featured'); ?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><?= trans('day_count'); ?></label>
                        <input type="hidden" class="form-control" name="product_id" id="day_count_product_id" value="">
                        <input type="hidden" class="form-control" name="is_ajax" value="0">
                        <input type="number" class="form-control" name="day_count" placeholder="<?= trans('day_count'); ?>" value="1" min="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success"><?= trans("submit"); ?></button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><?= trans("close"); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
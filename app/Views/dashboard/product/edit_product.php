<?php if ($product->is_draft == 1): ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="wizard-product">
                <h1 class="product-form-title"><?= esc($title); ?></h1>
                <div class="row">
                    <div class="col-md-12 wizard-add-product">
                        <ul class="wizard-progress">
                            <li class="active" id="step_general"><strong><?= trans("general_information"); ?></strong></li>
                            <li id="step_dedails"><strong><?= trans("details"); ?></strong></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<div class="row">
    <div class="col-sm-12">
        <div class="box box-add-product">
            <div class="box-body">
                <?php if ($product->is_draft != 1): ?>
                    <h1 class="product-form-title"><?= esc($title); ?></h1>
                <?php endif; ?>
                <div class="alert-message-lg">
                    <?php view('dashboard/includes/_messages'); ?>
                </div>
                <div class="row">
                    <div class="col-sm-12 m-b-30">
                        <label class="control-label"><?= trans("images"); ?></label>
                        <?= view('dashboard/product/_image_update'); ?>
                    </div>
                </div>
                <form action="<?= base_url('edit-product-post'); ?>" method="post" id="form_validate" class="validate_price" onkeypress="return event.keyCode != 13;">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="id" value="<?= $product->id; ?>">
                    <input type="hidden" name="sys_lang_id" value="<?= selectedLangId(); ?>">
                    <input type="hidden" name="back_url" value="<?= getCurrentUrl(); ?>">
                    <?php if ($generalSettings->physical_products_system == 1 && $generalSettings->digital_products_system == 0): ?>
                        <input type="hidden" name="product_type" value="physical">
                    <?php elseif ($generalSettings->physical_products_system == 0 && $generalSettings->digital_products_system == 1): ?>
                        <input type="hidden" name="product_type" value="digital">
                    <?php else: ?>
                        <div class="form-group">
                            <label class="control-label"><?= trans('product_type'); ?></label>
                            <div class="row">
                                <?php if ($generalSettings->physical_products_system == 1): ?>
                                    <div class="col-12 col-sm-6 col-custom-field">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="product_type" value="physical" id="product_type_1" class="custom-control-input" <?= $product->product_type == 'physical' ? 'checked' : ''; ?> required>
                                            <label for="product_type_1" class="custom-control-label"><?= trans('physical'); ?></label>
                                            <p class="form-element-exp"><?= trans('physical_exp'); ?></p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if ($generalSettings->digital_products_system == 1): ?>
                                    <div class="col-12 col-sm-6 col-custom-field">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="product_type" value="digital" id="product_type_2" class="custom-control-input" <?= $product->product_type == 'digital' ? 'checked' : ''; ?> required>
                                            <label for="product_type_2" class="custom-control-label"><?= trans('digital'); ?></label>
                                            <p class="form-element-exp"><?= trans('digital_exp'); ?></p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($activeProductSystemArray['activeSystemCount'] > 1): ?>
                        <div class="form-group">
                            <label class="control-label"><?= trans('listing_type'); ?></label>
                            <div class="row">
                                <?php if ($generalSettings->marketplace_system == 1): ?>
                                    <div class="col-12 col-sm-6 col-custom-field listing_sell_on_site">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="listing_type" value="sell_on_site" id="listing_type_1" class="custom-control-input" <?= $product->listing_type == 'sell_on_site' ? 'checked' : ''; ?> required>
                                            <label for="listing_type_1" class="custom-control-label"><?= trans('add_product_for_sale'); ?></label>
                                            <p class="form-element-exp"><?= trans('add_product_for_sale_exp'); ?></p>
                                        </div>
                                    </div>
                                <?php endif;
                                if ($generalSettings->classified_ads_system == 1): ?>
                                    <div class="col-12 col-sm-6 col-custom-field listing_ordinary_listing" <?= $product->product_type == 'digital' ? 'style="display:none;"' : ''; ?>>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="listing_type" value="ordinary_listing" id="listing_type_2" class="custom-control-input" <?= $product->listing_type == 'ordinary_listing' ? 'checked' : ''; ?> required>
                                            <label for="listing_type_2" class="custom-control-label"><?= trans('add_product_services_listing'); ?></label>
                                            <p class="form-element-exp"><?= trans('add_product_services_listing_exp'); ?></p>
                                        </div>
                                    </div>
                                <?php endif;
                                if ($generalSettings->bidding_system == 1): ?>
                                    <div class="col-12 col-sm-6 col-custom-field listing_bidding" <?= $product->product_type == 'digital' ? 'style="display:none;"' : ''; ?>>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="listing_type" value="bidding" id="listing_type_3" class="custom-control-input" <?= $product->listing_type == 'bidding' ? 'checked' : ''; ?> required>
                                            <label for="listing_type_3" class="custom-control-label"><?= trans('add_product_get_price_requests'); ?></label>
                                            <p class="form-element-exp"><?= trans('add_product_get_price_requests_exp'); ?></p>
                                        </div>
                                    </div>
                                <?php endif;
                                if ($generalSettings->digital_products_system == 1 && $generalSettings->selling_license_keys_system == 1): ?>
                                    <div class="col-12 col-sm-6 col-custom-field listing_license_keys" <?= $product->product_type == 'physical' ? 'style="display:none;"' : ''; ?>>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="listing_type" value="license_key" id="listing_type_4" class="custom-control-input" <?= $product->listing_type == 'license_key' ? 'checked' : ''; ?> required>
                                            <label for="listing_type_4" class="custom-control-label"><?= trans('add_product_sell_license_keys'); ?></label>
                                            <p class="form-element-exp"><?= trans('add_product_sell_license_keys_exp'); ?></p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="listing_type" value="<?= $activeProductSystemArray['activeSystemValue']; ?>">
                    <?php endif; ?>

                    <div class="form-group form-group-category">
                        <label class="control-label"><?= trans("category"); ?></label>
                        <div id="category_select_container">
                            <?php if (!empty($category)):
                                $parentArray = array();
                                if (!empty($category->parent_tree)) {
                                    $parentArray = explode(',', $category->parent_tree);
                                }
                                array_push($parentArray, $category->id);
                                $level = 1;
                                foreach ($parentArray as $parentId):
                                    $parentItem = getCategory($parentId);
                                    if (!empty($parentItem)):
                                        $subCategories = getSubCategoriesByParentId($parentItem->parent_id);;
                                        if (!empty($subCategories)): ?>
                                            <select name="category_id[]" class="form-control subcategory-select custom-select<?= $level == 1 ? ' category-select-first' : ''; ?>" data-level="<?= $level; ?>" onchange="getSubCategoriesDashboard(this.value, 1, <?= selectedLangId(); ?>);" <?= $level == 1 ? 'required' : ''; ?>>
                                                <option value=""><?= trans('select_category'); ?></option>
                                                <?php foreach ($subCategories as $subCategory): ?>
                                                    <option value="<?= $subCategory->id; ?>" <?= $subCategory->id == $parentItem->id ? 'selected' : ''; ?>><?= getCategoryName($subCategory); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php endif;
                                    endif;
                                    $level++;
                                endforeach;
                                if (!empty($category)):
                                    $subCategories = getSubCategoriesByParentId($category->id);
                                    if (!empty($subCategories)): ?>
                                        <select name="category_id[]" class="form-control subcategory-select custom-select" data-level="<?= $level; ?>" onchange="getSubCategoriesDashboard(this.value, 1, <?= selectedLangId(); ?>);">
                                            <option value=""><?= trans('select_category'); ?></option>
                                            <?php foreach ($subCategories as $subCategory): ?>
                                                <option value="<?= $subCategory->id; ?>"> <?= getCategoryName($subCategory); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php endif;
                                endif;
                            else:?>
                                <select id="categories" name="category_id[]" class="form-control custom-select m-0" onchange="getSubCategoriesDashboard(this.value, 1, <?= selectedLangId(); ?>);" required>
                                    <option value=""><?= trans('select_category'); ?></option>
                                    <?php if (!empty($parentCategories)):
                                        foreach ($parentCategories as $item): ?>
                                            <option value="<?= esc($item->id); ?>"><?= getCategoryName($item); ?></option>
                                        <?php endforeach;
                                    endif; ?>
                                </select>
                                <div id="category_select_container"></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (isAdmin()): ?>
                        <div class="form-group">
                            <label class="control-label"><?= trans('slug'); ?></label>
                            <input type="text" name="slug" class="form-control form-input" value="<?= esc($product->slug); ?>" placeholder="<?= trans("slug"); ?>" maxlength="200">
                        </div>
                    <?php endif; ?>

                    <?php if ($product->is_draft != 1 && $product->status == 1): ?>
                        <div class="form-group">
                            <label class="control-label"><?= trans('status'); ?></label>
                            <select name="is_sold" class="form-control custom-select" required>
                                <option value="0" <?= $product->is_sold != 1 ? 'selected' : ''; ?>><?= trans('active'); ?></option>
                                <option value="1" <?= $product->is_sold == 1 ? 'selected' : ''; ?>><?= trans('sold'); ?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?= trans('visibility'); ?></label>
                            <select name="visibility" class="form-control custom-select" required>
                                <option value="1" <?= $product->visibility == 1 ? 'selected' : ''; ?>><?= trans('visible'); ?></option>
                                <option value="0" <?= $product->visibility == 0 ? 'selected' : ''; ?>><?= trans('hidden'); ?></option>
                            </select>
                        </div>
                    <?php endif; ?>

                    <div class="panel-group panel-group-product">
                        <?php $languages = array();
                        array_push($languages, $activeLang);
                        if (!empty($activeLanguages)):
                            foreach ($activeLanguages as $language):
                                if (!empty($language->id != selectedLangId())) {
                                    array_push($languages, $language);
                                }
                            endforeach;
                        endif;
                        if (!empty($languages)):
                            foreach ($languages as $language):
                                $productDetails = getProductDetails($product->id, $language->id, false); ?>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" href="#collapse_<?= $language->id; ?>"><?= trans("details"); ?><?= countItems($activeLanguages) > 1 ? ':&nbsp;' . esc($language->name) : ''; ?>&nbsp;<?= selectedLangId() != $language->id ? '(' . trans("optional") . ')' : ''; ?><i class="fa fa-caret-down pull-right"></i></a>
                                        </h4>
                                    </div>
                                    <div id="collapse_<?= $language->id; ?>" class="panel-collapse collapse <?= selectedLangId() == $language->id ? 'in' : ''; ?>">
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label class="control-label"><?= trans("title"); ?></label>
                                                <input type="text" name="title_<?= $language->id; ?>" value="<?= !empty($productDetails) ? esc($productDetails->title) : ''; ?>" class="form-control form-input" placeholder="<?= trans("title"); ?>" <?= selectedLangId() == $language->id ? 'required' : ''; ?> maxlength="490">
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label"><?= trans("description"); ?></label>
                                                <div class="row">
                                                    <div class="col-sm-12 m-b-5">
                                                        <button type="button" id="btn_add_image_editor" class="btn btn-sm btn-info" data-editor-id="editor_<?= $language->id; ?>" data-toggle="modal" data-target="#fileManagerModal"><i class="icon-image"></i>&nbsp;&nbsp;<?= trans("add_image"); ?></button>
                                                    </div>
                                                </div>
                                                <textarea name="description_<?= $language->id; ?>" id="editor_<?= $language->id; ?>" class="tinyMCEsmall text-editor"><?= !empty($productDetails) ? $productDetails->description : ''; ?></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label"><?= trans("seo"); ?></label>
                                                <input type="text" name="seo_title_<?= $language->id; ?>" value="<?= !empty($productDetails) ? esc($productDetails->seo_title) : ''; ?>" class="form-control form-input m-b-5" placeholder="<?= trans("title"); ?>" maxlength="490">
                                                <input type="text" name="seo_description_<?= $language->id; ?>" value="<?= !empty($productDetails) ? esc($productDetails->seo_description) : ''; ?>" class="form-control form-input m-b-5" placeholder="<?= trans("description"); ?>" maxlength="490">
                                                <input type="text" name="seo_keywords_<?= $language->id; ?>" value="<?= !empty($productDetails) ? esc($productDetails->seo_keywords) : ''; ?>" class="form-control form-input m-b-5" placeholder="<?= trans("keywords"); ?>" maxlength="490">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach;
                        endif; ?>
                    </div>
                    <div class="col-sm-12 m-t-30">
                        <?php if ($product->is_draft == 1): ?>
                            <button type="submit" class="btn btn-lg btn-success pull-right"><?= trans("save_and_continue"); ?></button>
                        <?php else: ?>
                            <a href="<?= generateDashUrl('product', 'product_details') . '/' . $product->id; ?>" class="btn btn-lg btn-primary pull-right"><?= trans("edit_details"); ?>&nbsp;&nbsp;<i class="fa fa-long-arrow-right"></i> </a>
                            <button type="submit" class="btn btn-lg btn-success pull-right m-r-10"><?= trans("save_changes"); ?></button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= view('dashboard/product/_product_part'); ?>

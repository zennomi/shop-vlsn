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
<div class="row">
    <div class="col-sm-12">
        <div class="box box-add-product">
            <div class="box-body">
                <div class="alert-message-lg">
                    <?= view('dashboard/includes/_messages'); ?>
                </div>
                <div class="row">
                    <div class="col-sm-12 m-b-30">
                        <label class="control-label"><?= trans("images"); ?></label>
                        <?= view('dashboard/product/_image_upload'); ?>
                    </div>
                </div>
                <form action="<?= base_url('add-product-post'); ?>" method="post" id="form_validate" onkeypress="return event.keyCode != 13;">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="back_url" value="<?= getCurrentUrl(); ?>">
                    <input type="hidden" name="sys_lang_id" value="<?= selectedLangId(); ?>">
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
                                            <input type="radio" name="product_type" value="physical" id="product_type_1" class="custom-control-input" required>
                                            <label for="product_type_1" class="custom-control-label"><?= trans('physical'); ?></label>
                                            <p class="form-element-exp"><?= trans('physical_exp'); ?></p>
                                        </div>
                                    </div>
                                <?php endif;
                                if ($generalSettings->digital_products_system == 1): ?>
                                    <div class="col-12 col-sm-6 col-custom-field">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="product_type" value="digital" id="product_type_2" class="custom-control-input" required>
                                            <label for="product_type_2" class="custom-control-label"><?= trans('digital'); ?></label>
                                            <p class="form-element-exp"><?= trans('digital_exp'); ?></p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif;
                    if ($activeProductSystemArray['activeSystemCount'] > 1): ?>
                        <div class="form-group">
                            <label class="control-label"><?= trans('listing_type'); ?></label>
                            <div class="row">
                                <?php if ($generalSettings->marketplace_system == 1): ?>
                                    <div class="col-12 col-sm-6 col-custom-field listing_sell_on_site">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="listing_type" value="sell_on_site" id="listing_type_1" class="custom-control-input" required>
                                            <label for="listing_type_1" class="custom-control-label"><?= trans('add_product_for_sale'); ?></label>
                                            <p class="form-element-exp"><?= trans('add_product_for_sale_exp'); ?></p>
                                        </div>
                                    </div>
                                <?php endif;
                                if ($generalSettings->classified_ads_system == 1 && $generalSettings->physical_products_system == 1): ?>
                                    <div class="col-12 col-sm-6 col-custom-field listing_ordinary_listing">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="listing_type" value="ordinary_listing" id="listing_type_2" class="custom-control-input" required>
                                            <label for="listing_type_2" class="custom-control-label"><?= trans('add_product_services_listing'); ?></label>
                                            <p class="form-element-exp"><?= trans('add_product_services_listing_exp'); ?></p>
                                        </div>
                                    </div>
                                <?php endif;
                                if ($generalSettings->bidding_system == 1): ?>
                                    <div class="col-12 col-sm-6 col-custom-field listing_bidding">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="listing_type" value="bidding" id="listing_type_3" class="custom-control-input" required>
                                            <label for="listing_type_3" class="custom-control-label"><?= trans('add_product_get_price_requests'); ?></label>
                                            <p class="form-element-exp"><?= trans('add_product_get_price_requests_exp'); ?></p>
                                        </div>
                                    </div>
                                <?php endif;
                                if ($generalSettings->digital_products_system == 1 && $generalSettings->selling_license_keys_system == 1): ?>
                                    <div class="col-12 col-sm-6 col-custom-field listing_license_keys">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="listing_type" value="license_key" id="listing_type_4" class="custom-control-input" required>
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
                        <select id="categories" name="category_id[]" class="form-control custom-select m-0" onchange="getSubCategoriesDashboard(this.value, 0, <?= selectedLangId(); ?>);" required>
                            <option value=""><?= trans('select_category'); ?></option>
                            <?php if (!empty($parentCategories)):
                                foreach ($parentCategories as $item): ?>
                                    <option value="<?= esc($item->id); ?>"><?= getCategoryName($item); ?></option>
                                <?php endforeach;
                            endif; ?>
                        </select>
                        <div id="category_select_container"></div>
                    </div>

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
                            foreach ($languages as $language):?>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" href="#collapse_<?= $language->id; ?>"><?= trans("details"); ?><?= $activeLanguages > 1 ? ':&nbsp;' . esc($language->name) : ''; ?>&nbsp;<?= selectedLangId() != $language->id ? '(' . trans("optional") . ')' : ''; ?><i class="fa fa-caret-down pull-right"></i></a>
                                        </h4>
                                    </div>
                                    <div id="collapse_<?= $language->id; ?>" class="panel-collapse collapse <?= selectedLangId() == $language->id ? 'in' : ''; ?>">
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label class="control-label"><?= trans("title"); ?></label>
                                                <input type="text" name="title_<?= $language->id; ?>" class="form-control form-input" placeholder="<?= trans("title"); ?>" <?= selectedLangId() == $language->id ? 'required' : ''; ?> maxlength="490">
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label"><?= trans("description"); ?></label>
                                                <div class="row">
                                                    <div class="col-sm-12 m-b-5">
                                                        <button type="button" id="btn_add_image_editor" class="btn btn-sm btn-info" data-editor-id="editor_<?= $language->id; ?>" data-toggle="modal" data-target="#fileManagerModal"><i class="icon-image"></i>&nbsp;&nbsp;<?= trans("add_image"); ?></button>
                                                    </div>
                                                </div>
                                                <textarea name="description_<?= $language->id; ?>" id="editor_<?= $language->id; ?>" class="tinyMCEsmall text-editor"></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label"><?= trans("seo"); ?></label>
                                                <input type="text" name="seo_title_<?= $language->id; ?>" class="form-control form-input m-b-5" placeholder="<?= trans("title"); ?>" maxlength="490">
                                                <input type="text" name="seo_description_<?= $language->id; ?>" class="form-control form-input m-b-5" placeholder="<?= trans("description"); ?>" maxlength="490">
                                                <input type="text" name="seo_keywords_<?= $language->id; ?>" class="form-control form-input m-b-5" placeholder="<?= trans("keywords"); ?>" maxlength="490">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach;
                        endif; ?>
                    </div>

                    <div class="col-sm-12 m-t-30">
                        <button type="submit" class="btn btn-lg btn-success pull-right"><?= trans("save_and_continue"); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<div class="modal fade" id="fileManagerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-file-manager" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= trans("images"); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="icon-close"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="file-manager">
                    <div class="file-manager-left">
                        <div class="dm-uploader-container">
                            <div id="drag-and-drop-zone-file-manager" class="dm-uploader text-center">
                                <p class="file-manager-file-types">
                                    <span>JPG</span>
                                    <span>JPEG</span>
                                    <span>PNG</span>
                                </p>
                                <p class="dm-upload-icon">
                                    <i class="icon-upload"></i>
                                </p>
                                <p class="dm-upload-text"><?= trans("drag_drop_images_here"); ?></p>
                                <p class="text-center">
                                    <button class="btn btn-default btn-browse-files"><?= trans('browse_files'); ?></button>
                                </p>
                                <a class='btn btn-md dm-btn-select-files'>
                                    <input type="file" name="file" size="40" multiple="multiple">
                                </a>
                                <ul class="dm-uploaded-files" id="files-file-manager"></ul>
                                <button type="button" id="btn_reset_upload_image" class="btn btn-reset-upload"><?= trans("reset"); ?></button>
                            </div>
                        </div>
                    </div>
                    <div class="file-manager-right">
                        <div class="file-manager-content">
                            <div id="ckimage_file_upload_response">
                                <?php if (!empty($fileManagerImages)):
                                    foreach ($fileManagerImages as $image): ?>
                                        <div class="col-file-manager" id="fm_img_col_id_<?= $image->id; ?>">
                                            <div class="file-box" data-file-id="<?= $image->id; ?>" data-file-path="<?= getFileManagerImageUrl($image); ?>">
                                                <div class="image-container">
                                                    <img src="<?= getFileManagerImageUrl($image); ?>" alt="" class="img-responsive">
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach;
                                endif; ?>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="selected_fm_img_file_id">
                    <input type="hidden" id="selected_fm_img_file_path">
                </div>
            </div>
            <div class="modal-footer">
                <div class="file-manager-footer">
                    <button type="button" id="btn_fm_img_delete" class="btn btn-sm btn-danger color-white pull-left btn-file-delete m-r-3"><i class="icon-trash"></i>&nbsp;&nbsp;<?= trans('delete'); ?></button>
                    <button type="button" id="btn_fm_img_select" class="btn btn-sm btn-info color-white btn-file-select"><i class="icon-check"></i>&nbsp;&nbsp;<?= trans('select_image'); ?></button>
                    <button type="button" class="btn btn-sm btn-secondary color-white" data-dismiss="modal"><?= trans('close'); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('dashboard/product/_product_part'); ?>
<form id="form_add_product_variation_option" novalidate>
    <input type="hidden" name="variation_id" value="<?= $variation->id; ?>">
    <div class="modal-header">
        <h5 class="modal-title"><?= trans("add_option"); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true"><i class="icon-close"></i></span>
        </button>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-sm-12">
                <?= view('dashboard/includes/_messages'); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 box-variation-options">
                <?php if (!empty($variation->parent_id == 0)): ?>
                    <div class="form-group m-b-5">
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="control-label"><?= trans('default_option'); ?>&nbsp;<small class="text-muted">(<?= trans('default_option_exp'); ?>)</small></label>
                            </div>
                            <div class="col-md-6 col-sm-12 col-custom-option">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="is_default" value="1" id="is_default_1" class="custom-control-input">
                                    <label for="is_default_1" class="custom-control-label"><?= trans("yes"); ?></label>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12 col-custom-option">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="is_default" value="0" id="is_default_2" class="custom-control-input" checked>
                                    <label for="is_default_2" class="custom-control-label"><?= trans("no"); ?></label>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="form-group m-b-5">
                    <label class="control-label"><?= trans("option_name"); ?></label>
                    <?php if (!empty($activeLanguages)):
                        if (countItems($activeLanguages) <= 1): ?>
                            <input type="text" id="input_variation_option_name" class="form-control form-input input-variation-option" name="option_name_<?= selectedLangId(); ?>" maxlength="255">
                        <?php else:
                            foreach ($activeLanguages as $language):
                                if ($language->id == selectedLangId()): ?>
                                    <input type="text" id="input_variation_option_name" class="form-control form-input input-variation-option" name="option_name_<?= $language->id; ?>" placeholder="<?= esc($language->name); ?>" maxlength="255">
                                <?php else: ?>
                                    <input type="text" class="form-control form-input input-variation-option" name="option_name_<?= $language->id; ?>" placeholder="<?= esc($language->name) . ' (' . trans("optional") . ')'; ?>" maxlength="255">
                                <?php endif;
                            endforeach;
                        endif;
                    endif; ?>
                </div>
                <?php if (!empty($variation->parent_id != 0)): ?>
                    <div class="form-group">
                        <label class="control-label"><?= trans('parent_option'); ?></label>
                        <select name="parent_id" class="form-control custom-select">
                            <?php if (!empty($parentVariationOptions)):
                                foreach ($parentVariationOptions as $parentOption): ?>
                                    <option value="<?= $parentOption->id; ?>"><?= esc(getVariationOptionName($parentOption->option_names, selectedLangId())); ?></option>
                                <?php endforeach;
                            endif; ?>
                        </select>
                    </div>
                <?php endif; ?>
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6 hide-if-default">
                            <label class="control-label"><?= trans('stock'); ?></label>
                            <input type="number" name="option_stock" class="form-control form-input" value="1" min="0">
                        </div>
                        <?php if ($variation->variation_type != 'dropdown' && $variation->option_display_type == 'color'): ?>
                            <div class="col-sm-6">
                                <label class="control-label"><?= trans('color'); ?>&nbsp;<small class="text-muted">(<?= trans("optional"); ?>)</small></label>
                                <div class="input-group colorpicker">
                                    <input type="text" class="form-control" name="option_color" maxlength="200" placeholder="<?= trans('color'); ?>">
                                    <div class="input-group-addon">
                                        <i></i>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($variation->use_different_price == 1): ?>
                    <div class="form-group hide-if-default">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="row align-items-center">
                                    <div class="col-sm-12">
                                        <label class="control-label"><?= trans("price"); ?></label>
                                        <div id="price_input_container_variation">
                                            <div class="input-group">
                                                <span class="input-group-addon"><?= $defaultCurrency->symbol; ?></span>
                                                <input type="text" name="option_price" id="product_price_input_variation" class="form-control form-input price-input validate-price-input m-0" placeholder="<?= $baseVars->inputInitialPrice; ?>" onpaste="return false;" maxlength="32" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 m-t-10">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="use_default_price" id="checkbox_price_variation" value="1">
                                            <label for="checkbox_price_variation" class="custom-control-label"><?= trans("use_default_price"); ?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="row align-items-center">
                                    <div class="col-sm-12">
                                        <label class="control-label"><?= trans("discount_rate"); ?></label>
                                        <div id="discount_input_container_variation">
                                            <div class="input-group">
                                                <span class="input-group-addon">%</span>
                                                <input type="number" name="option_discount_rate" id="input_discount_rate_variation" class="form-control form-input m-0" value="" min="0" max="99" placeholder="0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 m-t-10">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="no_discount" id="checkbox_discount_rate_variation" value="1">
                                            <label for="checkbox_discount_rate_variation" class="custom-control-label"><?= trans("no_discount"); ?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif;
                if ($variation->option_display_type == 'image' || $variation->show_images_on_slider == 1): ?>
                    <div class="form-group hide-if-default">
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="control-label"><?= trans("images"); ?>&nbsp;<small class="text-muted">(<?= trans("optional"); ?>)</small></label>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="dm-uploader-container">
                                            <div id="drag-and-drop-zone-variation-image-session" class="dm-uploader text-center">
                                                <p class="dm-upload-icon">
                                                    <i class="icon-upload"></i>
                                                </p>
                                                <p class="dm-upload-text"><?= trans("drag_drop_images_here"); ?>&nbsp;<span style="text-decoration: underline"><?= trans('browse_files'); ?></span></p>

                                                <a class='btn btn-md dm-btn-select-files'>
                                                    <input type="file" name="file" size="40" multiple="multiple">
                                                </a>
                                                <ul class="dm-uploaded-files" id="files-variation-image-session">
                                                    <?php $variationImagesSession = getSessVariationImagesArray();
                                                    if (!empty($variationImagesSession)):
                                                        foreach ($variationImagesSession as $imgSession):?>
                                                            <li class="media" id="uploaderFile<?= $imgSession->file_id; ?>">
                                                                <img src="<?= base_url('uploads/temp/' . $imgSession->img_default); ?>" alt="">
                                                                <a href="javascript:void(0)" class="btn-img-delete btn-delete-variation-image-session" data-file-id="<?= $imgSession->file_id; ?>"><i class="icon-close"></i></a>
                                                                <?php if ($imgSession->is_main == 1): ?>
                                                                    <a href="javascript:void(0)" class="btn btn-xs btn-success btn-is-image-main btn-set-variation-image-main-session"><?= trans("main"); ?></a>
                                                                <?php else: ?>
                                                                    <a href="javascript:void(0)" class="btn btn-xs btn-secondary btn-is-image-main btn-set-variation-image-main-session" data-file-id="<?= $imgSession->file_id; ?>"><?= trans("main"); ?></a>
                                                                <?php endif; ?>
                                                            </li>
                                                        <?php endforeach;
                                                    endif; ?>
                                                </ul>
                                            </div>
                                        </div>
                                        <script type="text/html" id="files-template-variation-image-session">
                                            <li class="media">
                                                <img class="preview-img" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="bg">
                                                <div class="media-body">
                                                    <div class="progress">
                                                        <div class="dm-progress-waiting"><?= trans("waiting"); ?></div>
                                                        <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                            </li>
                                        </script>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <div class="row-custom">
            <button type="button" id="btn_add_variation_option" class="btn btn-md btn-success color-white float-right"><?= trans("add_option"); ?></button>
        </div>
    </div>
</form>
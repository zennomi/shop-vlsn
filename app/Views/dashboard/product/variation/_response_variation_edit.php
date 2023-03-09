<input type="hidden" name="variation_id" value="<?= $variation->id; ?>">
<input type="hidden" name="product_id" value="<?= $variation->product_id; ?>">
<div class="modal-header">
    <h5 class="modal-title"><?= trans("edit_variation"); ?></h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true"><i class="icon-close"></i></span>
    </button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-12 tab-variation">
            <div class="form-group">
                <label class="control-label"><?= trans('label'); ?></label>
                <?php foreach ($activeLanguages as $language): ?>
                    <?php if ($language->id == selectedLangId()): ?>
                        <input type="text" id="input_variation_label_edit" class="form-control form-input input-variation-label" name="label_lang_<?= $language->id; ?>" value="<?= getVariationLabel($variation->label_names, $language->id); ?>" placeholder="<?= esc($language->name); ?>" maxlength="255" required>
                    <?php else: ?>
                        <input type="text" class="form-control form-input input-variation-label" name="label_lang_<?= $language->id; ?>" value="<?= getVariationLabel($variation->label_names, $language->id); ?>" placeholder="<?= esc($language->name) . ' (' . trans("optional") . ')'; ?>" maxlength="255">
                    <?php endif;
             endforeach; ?>
            </div>
            <div class="form-group">
                <label class="control-label"><?= trans('variation_type'); ?></label>
                <select name="variation_type" class="form-control custom-select" onchange="showHideFormOptionImages(this.value);" required>
                    <option value="radio_button" <?= $variation->variation_type == 'radio_button' ? 'selected' : ''; ?>><?= trans('radio_button'); ?></option>
                    <option value="dropdown" <?= $variation->variation_type == 'dropdown' ? 'selected' : ''; ?>><?= trans('dropdown'); ?></option>
                    <option value="checkbox" <?= $variation->variation_type == 'checkbox' ? 'selected' : ''; ?>><?= trans('checkbox'); ?></option>
                    <option value="text" <?= $variation->variation_type == 'text' ? 'selected' : ''; ?>><?= trans('text'); ?></option>
                    <option value="number" <?= $variation->variation_type == 'number' ? 'selected' : ''; ?>><?= trans('number'); ?></option>
                </select>
            </div>
            <div class="form-group m-0 form-group-display-type <?= $variation->variation_type != 'radio_button' && $variation->variation_type != 'checkbox' ? 'display-none' : ''; ?>">
                <div class="row">
                    <div class="col-sm-12">
                        <label class="control-label"><?= trans('option_display_type'); ?></label>
                    </div>
                    <div class="col-md-4 col-sm-12 col-custom-option">
                        <div class="custom-control custom-radio">
                            <input type="radio" name="option_display_type" value="text" id="option_display_type_edit_1" class="custom-control-input" <?= $variation->option_display_type == 'text' ? 'checked' : ''; ?>>
                            <label for="option_display_type_edit_1" class="custom-control-label"><?= trans('text'); ?></label>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 col-custom-option">
                        <div class="custom-control custom-radio">
                            <input type="radio" name="option_display_type" value="image" id="option_display_type_edit_2" class="custom-control-input" <?= $variation->option_display_type == 'image' ? 'checked' : ''; ?>>
                            <label for="option_display_type_edit_2" class="custom-control-label"><?= trans('image'); ?></label>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 col-custom-option">
                        <div class="custom-control custom-radio">
                            <input type="radio" name="option_display_type" value="color" id="option_display_type_edit_3" class="custom-control-input" <?= $variation->option_display_type == 'color' ? 'checked' : ''; ?>>
                            <label for="option_display_type_edit_3" class="custom-control-label"><?= trans('color'); ?></label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group <?= ($variation->variation_type != "dropdown") ? "display-none" : ""; ?>">
                <label class="control-label"><?= trans('parent_variation'); ?></label>
                <select name="parent_id" class="form-control custom-select">
                    <option value=""><?= trans("none"); ?></option>
                    <?php if (!empty($productVariations)):
                        foreach ($productVariations as $item):
                            if ($item->variation_type == 'dropdown'): ?>
                                <option value="<?= $item->id; ?>" <?= $variation->parent_id == $item->id ? 'selected' : ''; ?>><?= $item->id . ' - ' . esc(getVariationLabel($item->label_names, selectedLangId())) . ' - ' . trans($item->variation_type); ?></option>
                            <?php endif;
                        endforeach;
                    endif; ?>
                </select>
            </div>
            <div class="form-group m-0 form-group-show-option-images <?= $variation->variation_type != 'radio_button' && $variation->variation_type != 'dropdown' ? 'display-none' : ''; ?>">
                <div class="row">
                    <div class="col-sm-12">
                        <label class="control-label"><?= trans('show_option_images_on_slider'); ?></label>
                    </div>
                    <div class="col-md-4 col-sm-12 col-custom-option">
                        <div class="custom-control custom-radio">
                            <input type="radio" name="show_images_on_slider" value="1" id="show_images_on_slider_when_selected_edit_1" class="custom-control-input" <?= $variation->show_images_on_slider == 1 ? 'checked' : ''; ?>>
                            <label for="show_images_on_slider_when_selected_edit_1" class="custom-control-label"><?= trans('yes'); ?></label>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 col-custom-option">
                        <div class="custom-control custom-radio">
                            <input type="radio" name="show_images_on_slider" value="0" id="show_images_on_slider_when_selected_edit_2" class="custom-control-input" <?= $variation->show_images_on_slider != 1 ? 'checked' : ''; ?>>
                            <label for="show_images_on_slider_when_selected_edit_2" class="custom-control-label"><?= trans('no'); ?></label>
                        </div>
                    </div>
                </div>
            </div>
            <?php if (empty(isVariationsUseDifferentPrice($variation->product_id, $variation->id)) && $product->listing_type != 'bidding'): ?>
                <div class="form-group form-group-show-option-images">
                    <div class="row">
                        <div class="col-sm-12">
                            <label class="control-label"><?= trans('use_different_price_for_options'); ?></label>
                        </div>
                        <div class="col-md-4 col-sm-12 col-custom-option">
                            <div class="custom-control custom-radio">
                                <input type="radio" name="use_different_price" value="1" id="use_different_price_edit_1" class="custom-control-input" <?= $variation->use_different_price == 1 ? 'checked' : ''; ?>>
                                <label for="use_different_price_edit_1" class="custom-control-label"><?= trans('yes'); ?></label>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12 col-custom-option">
                            <div class="custom-control custom-radio">
                                <input type="radio" name="use_different_price" value="0" id="use_different_price_edit_2" class="custom-control-input" <?= $variation->use_different_price != 1 ? 'checked' : ''; ?>>
                                <label for="use_different_price_edit_2" class="custom-control-label"><?= trans('no'); ?></label>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="form-group m-0">
                <div class="row">
                    <div class="col-sm-12">
                        <label class="control-label"><?= trans('visible'); ?></label>
                    </div>
                    <div class="col-md-4 col-sm-12 col-custom-option">
                        <div class="custom-control custom-radio">
                            <input type="radio" name="is_visible" value="1" id="edit_visible_edit_1" class="custom-control-input" <?= $variation->is_visible == 1 ? 'checked' : ''; ?>>
                            <label for="edit_visible_edit_1" class="custom-control-label"><?= trans('yes'); ?></label>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 col-custom-option">
                        <div class="custom-control custom-radio">
                            <input type="radio" name="is_visible" value="0" id="edit_visible_edit_2" class="custom-control-input" <?= $variation->is_visible != 1 ? 'checked' : ''; ?>>
                            <label for="edit_visible_edit_2" class="custom-control-label"><?= trans('no'); ?></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="row-custom">
        <button type="button" class="btn btn-md btn-danger color-white float-left hidden btn-show-variation-form"><i class="icon-arrow-left"></i><?= trans("back") ?></button>
        <button type="submit" class="btn btn-md btn-secondary btn-variation float-right"><?= trans("save_changes"); ?></button>
    </div>
</div>
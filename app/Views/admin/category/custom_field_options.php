<div class="row">
    <div class="col-sm-12 form-header">
        <h1 class="form-title"><?= trans('custom_field_options'); ?></h1>
    </div>
</div>
<div class="callout" style="margin-top: 10px;background-color: #fff; border-color:#00c0ef;max-width: 600px;">
    <h4><?= trans("custom_field"); ?></h4>
    <p><?= trans('field_name'); ?>:&nbsp;<strong><?= parseSerializedNameArray($field->name_array, selectedLangId()); ?></strong></p>
    <p>
        <?= trans('type'); ?>:&nbsp;
        <strong><?= trans($field->field_type); ?></strong>
    </p>
</div>
<div class="row">
    <?php if ($field->field_type == 'checkbox' || $field->field_type == 'radio_button' || $field->field_type == 'dropdown'): ?>
        <div class="col-sm-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= trans("options"); ?></h3>
                </div>
                <div class="box-body">
                    <?php if (!empty($options)): ?>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="custom-field-options" style="max-height: 600px; overflow: auto">
                                        <?php $count = 1;
                                        foreach ($options as $option): ?>
                                            <div class="field-option-item">
                                                <form action="<?= base_url('CategoryController/editCustomFieldOptionPost'); ?>" method="post" onkeypress="return event.keyCode != 13;">
                                                    <?= csrf_field(); ?>
                                                    <input type="hidden" name="id" value="<?= $option->id; ?>">
                                                    <div class="option-title">
                                                        <strong><?= trans("option") . " " . $count; ?></strong>
                                                    </div>
                                                    <?php foreach ($activeLanguages as $language):
                                                        $nameKey = 'option_name_' . $language->id; ?>
                                                        <p><input type='text' class="form-control" name="option_lang_<?= $language->id; ?>" value="<?= $option->$nameKey; ?>" placeholder="<?= trans("option"); ?> (<?= $language->name; ?>)" style="width: 100%;padding: 0 5px; bottom: 0 !important;box-shadow: none !important;height: 26px;" required></p>
                                                    <?php endforeach; ?>
                                                    <div>
                                                        <button type="button" class="btn btn-xs btn-danger pull-right" onclick='deleteCustomFieldOption("<?= trans("confirm_delete", true); ?>","<?= $option->id; ?>");'><?= trans("delete"); ?></button>
                                                        <button type="submit" class="btn btn-xs btn-success pull-right m-r-5"><?= trans("save_changes"); ?></button>
                                                    </div>
                                                </form>
                                            </div>
                                            <?php $count++;
                                        endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <form action="<?= base_url('CategoryController/addCustomFieldOptionPost'); ?>" method="post" onkeypress="return event.keyCode != 13;">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="field_id" value="<?= $field->id; ?>">
                        <div class="form-group m-b-10">
                            <label><?= trans("add_option"); ?></label>
                            <?php foreach ($activeLanguages as $language): ?>
                                <input type="text" class="form-control option-input m-b-5" name="option_lang_<?= $language->id; ?>" placeholder="<?= trans("option"); ?> (<?= $language->name; ?>)" required>
                            <?php endforeach; ?>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary pull-right"><?= trans('add_option'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="col-sm-6">
        <div class="box box-primary" style="min-height: 252px;">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans("categories"); ?></h3>
                <small>(<?= trans("show_under_these_categories"); ?>)</small>
            </div>
            <form action="<?= base_url('CategoryController/addCategoryToCustomField'); ?>" method="post" onkeypress="return event.keyCode != 13;">
                <?= csrf_field(); ?>
                <input type="hidden" name="field_id" value="<?= $field->id; ?>">
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label"><?= trans("category"); ?></label>
                        <select id="categories" name="category_id[]" class="form-control" onchange="getSubCategories(this.value, 0);" required>
                            <option value=""><?= trans('select_category'); ?></option>
                            <?php if (!empty($parentCategories)):
                                foreach ($parentCategories as $item): ?>
                                    <option value="<?= esc($item->id); ?>"><?= getCategoryName($item); ?></option>
                                <?php endforeach;
                            endif; ?>
                        </select>
                        <div id="category_select_container"></div>
                    </div>
                    <div class="form-group text-right">
                        <button type="submit" class="btn btn-primary"><?= trans('select_category'); ?></button>
                    </div>
                    <div class="row m-t-15">
                        <div class="col-sm-12">
                            <table class="table table-bordered table-striped" role="grid">
                                <tbody>
                                <?php if (!empty($fieldCategories)):
                                    foreach ($fieldCategories as $item):
                                        if (!empty($item)):
                                            $category = getCategory($item->category_id);
                                            if (!empty($category)):
                                                $categoriesTree = getCategoryParentTree($category, false);
                                                if (!empty($categoriesTree)):?>
                                                    <tr>
                                                        <td>
                                                            <?php $count = 0;
                                                            foreach ($categoriesTree as $itemTree):
                                                                $itemCategory = getCategory($itemTree->id);
                                                                if (!empty($itemCategory)):
                                                                    if ($count == 0) {
                                                                        echo getCategoryName($itemCategory);
                                                                    } else {
                                                                        echo ' / ' . getCategoryName($itemCategory);
                                                                    }
                                                                endif;
                                                                $count++;
                                                            endforeach; ?>
                                                            <button type="button" class="btn btn-xs btn-danger pull-right" onclick="deleteCategoryFromField('<?= trans("confirm_delete", true); ?>',<?= $field->id; ?>,<?= $itemCategory->id; ?>);"><?= trans("delete"); ?></button>
                                                        </td>
                                                    </tr>
                                                <?php endif;
                                            endif;
                                        endif;
                                    endforeach;
                                endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <?php if ($field->field_type == "checkbox" || $field->field_type == "radio_button" || $field->field_type == "dropdown"): ?>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= trans('settings'); ?></h3>
                </div>
                <form action="<?= base_url('CategoryController/customFieldSettingsPost'); ?>" method="post">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="field_id" value="<?= $field->id; ?>">
                    <div class="box-body">
                        <div class="form-group m-b-30">
                            <div class="row">
                                <div class="col-sm-12 col-xs-12">
                                    <label><?= trans("sort_options"); ?></label>
                                </div>
                                <div class="col-md-4 col-sm-12 col-option">
                                    <input type="radio" name="sort_options" value="date" id="by_date_1" class="square-purple" <?= $field->sort_options == 'date' ? 'checked' : ''; ?>>
                                    <label for="by_date_1" class="option-label"><?= trans('by_date'); ?></label>
                                </div>
                                <div class="col-md-4 col-sm-12 col-option">
                                    <input type="radio" name="sort_options" value="date_desc" id="by_date_desc_1" class="square-purple" <?= $field->sort_options == 'date_desc' ? 'checked' : ''; ?>>
                                    <label for="by_date_desc_1" class="option-label"><?= trans('by_date'); ?>&nbsp;(DESC)</label>
                                </div>
                                <div class="col-md-4 col-sm-12 col-option">
                                    <input type="radio" name="sort_options" value="alphabetically" id="alphabetically_1" class="square-purple" <?= $field->sort_options == 'alphabetically' ? 'checked' : ''; ?>>
                                    <label for="alphabetically_1" class="option-label"><?= trans('alphabetically'); ?></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>
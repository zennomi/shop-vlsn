<div class="row">
    <div class="col-sm-7">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans('update_custom_field'); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('custom-fields'); ?>" class="btn btn-success btn-add-new">
                        <i class="fa fa-list-ul"></i>&nbsp;&nbsp;<?= trans('custom_fields'); ?>
                    </a>
                </div>
            </div>
            <form action="<?= base_url('CategoryController/editCustomFieldPost'); ?>" method="post" onkeypress="return event.keyCode != 13;">
                <?= csrf_field(); ?>
                <input type="hidden" name="id" value="<?= $field->id; ?>">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?php foreach ($activeLanguages as $language): ?>
                                <div class="form-group">
                                    <label><?= trans("field_name"); ?> (<?= $language->name; ?>)</label>
                                    <input type="text" class="form-control" name="name_lang_<?= $language->id; ?>" placeholder="<?= trans("field_name"); ?>" value="<?= parseSerializedNameArray($field->name_array, $language->id, false); ?>" maxlength="255" required>
                                </div>
                            <?php endforeach; ?>
                            <div class="form-group">
                                <label><?= trans("filter_key"); ?> <small>(<?= trans("filter_key_exp"); ?>)</small></label>
                                <input type="text" class="form-control" name="product_filter_key" placeholder="<?= trans("field_name"); ?>" value="<?= esc($field->product_filter_key); ?>" maxlength="255" required>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-6 col-xs-12">
                                        <label><?= trans('row_width'); ?></label>
                                    </div>
                                    <div class="col-sm-3 col-xs-12 col-option">
                                        <input type="radio" name="row_width" value="half" id="row_width_1" class="square-purple" <?= $field->row_width == 'half' ? 'checked' : ''; ?>>
                                        <label for="row_width_1" class="option-label"><?= trans('half_width'); ?></label>
                                    </div>
                                    <div class="col-sm-3 col-xs-12 col-option">
                                        <input type="radio" name="row_width" value="full" id="row_width_2" class="square-purple" <?= $field->row_width == 'full' ? 'checked' : ''; ?>>
                                        <label for="row_width_2" class="option-label"><?= trans('full_width'); ?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <label class="control-label"><?= trans('required'); ?></label>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <input type="checkbox" name="is_required" value="1" class="square-purple" <?= $field->is_required == 1 ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-6 col-xs-12">
                                        <label><?= trans('status'); ?></label>
                                    </div>
                                    <div class="col-sm-3 col-xs-12 col-option">
                                        <input type="radio" name="status" value="1" id="status_1" class="square-purple" <?= $field->status == 1 ? 'checked' : ''; ?>>
                                        <label for="status_1" class="option-label"><?= trans('active'); ?></label>
                                    </div>
                                    <div class="col-sm-3 col-xs-12 col-option">
                                        <input type="radio" name="status" value="0" id="status_2" class="square-purple" <?= ($field->status != 1) ? 'checked' : ''; ?>>
                                        <label for="status_2" class="option-label"><?= trans('inactive'); ?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label><?= trans('order'); ?></label>
                                <input type="number" class="form-control" name="field_order" placeholder="<?= trans('order'); ?>" value="<?= esc($field->field_order); ?>" min="1" max="99999" required>
                            </div>
                            <div class="form-group">
                                <label><?= trans('type'); ?></label>
                                <select class="form-control" name="field_type">
                                    <option value="text" <?= $field->field_type == 'text' ? 'selected' : ''; ?>><?= trans('text'); ?></option>
                                    <option value="textarea" <?= $field->field_type == 'textarea' ? 'selected' : ''; ?>><?= trans('textarea'); ?></option>
                                    <option value="number" <?= $field->field_type == 'number' ? 'selected' : ''; ?>><?= trans('number'); ?></option>
                                    <option value="checkbox" <?= $field->field_type == 'checkbox' ? 'selected' : ''; ?>><?= trans('checkbox'); ?></option>
                                    <option value="radio_button" <?= $field->field_type == 'radio_button' ? 'selected' : ''; ?>><?= trans('radio_button'); ?></option>
                                    <option value="dropdown" <?= $field->field_type == 'dropdown' ? 'selected' : ''; ?>><?= trans('dropdown'); ?></option>
                                    <option value="date" <?= $field->field_type == 'date' ? 'selected' : ''; ?>><?= trans('date'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                    <a href="<?= adminUrl('custom-field-options/' . $field->id); ?>" class="btn btn-warning pull-right m-r-5"><i class="fa fa-list"></i>&nbsp;&nbsp;<?= trans('edit_options'); ?></a>
                </div>
            </form>
        </div>
    </div>
</div>
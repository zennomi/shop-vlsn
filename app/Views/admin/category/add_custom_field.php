<div class="row">
    <div class="col-sm-7">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans('add_custom_field'); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('custom-fields'); ?>" class="btn btn-success btn-add-new">
                        <i class="fa fa-list-ul"></i>&nbsp;&nbsp;<?= trans('custom_fields'); ?>
                    </a>
                </div>
            </div>
            <form action="<?= base_url('CategoryController/addCustomFieldPost'); ?>" method="post" onkeypress="return event.keyCode != 13;">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?php foreach ($activeLanguages as $language): ?>
                                <div class="form-group">
                                    <label><?= trans("field_name"); ?> (<?= $language->name; ?>)</label>
                                    <input type="text" class="form-control" name="name_lang_<?= $language->id; ?>" placeholder="<?= trans("field_name"); ?>" maxlength="255" required>
                                </div>
                            <?php endforeach; ?>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label><?= trans('row_width'); ?></label>
                                    </div>
                                    <div class="col-sm-3 col-xs-12 col-option">
                                        <input type="radio" name="row_width" value="half" id="row_width_1" class="square-purple" checked>
                                        <label for="row_width_1" class="option-label"><?= trans('half_width'); ?></label>
                                    </div>
                                    <div class="col-sm-3 col-xs-12 col-option">
                                        <input type="radio" name="row_width" value="full" id="row_width_2" class="square-purple">
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
                                        <input type="checkbox" name="is_required" value="1" class="square-purple">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-6 col-xs-12">
                                        <label><?= trans('status'); ?></label>
                                    </div>
                                    <div class="col-sm-3 col-xs-12 col-option">
                                        <input type="radio" name="status" value="1" id="status_1" class="square-purple" checked>
                                        <label for="status_1" class="option-label"><?= trans('active'); ?></label>
                                    </div>
                                    <div class="col-sm-3 col-xs-12 col-option">
                                        <input type="radio" name="status" value="0" id="status_2" class="square-purple">
                                        <label for="status_2" class="option-label"><?= trans('inactive'); ?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label><?= trans('order'); ?></label>
                                <input type="number" class="form-control" name="field_order" placeholder="<?= trans('order'); ?>" min="1" max="99999" value="1" required>
                            </div>
                            <div class="form-group">
                                <label><?= trans('type'); ?></label>
                                <select class="form-control" name="field_type">
                                    <option value="text"><?= trans('text'); ?></option>
                                    <option value="textarea"><?= trans('textarea'); ?></option>
                                    <option value="number"><?= trans('number'); ?></option>
                                    <option value="checkbox"><?= trans('checkbox'); ?></option>
                                    <option value="radio_button"><?= trans('radio_button'); ?></option>
                                    <option value="dropdown"><?= trans('dropdown'); ?></option>
                                    <option value="date"><?= trans('date'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('save_and_continue'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
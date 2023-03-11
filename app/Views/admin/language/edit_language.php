<?php include FCPATH . 'assets/vendor/tinymce/languages.php'; ?>
<div class="row">
    <div class="col-lg-6 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans("update_language"); ?></h3>
            </div>
            <form action="<?= base_url('LanguageController/editLanguagePost'); ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <input type="hidden" name="id" value="<?= esc($language->id); ?>">
                <div class="box-body">
                    <div class="form-group">
                        <label><?= trans("language_name"); ?></label>
                        <input type="text" class="form-control" name="name" placeholder="<?= trans("language_name"); ?>" value="<?= esc($language->name); ?>" maxlength="200" required>
                        <small>(Ex: English)</small>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans("short_form"); ?> </label>
                        <input type="text" class="form-control" name="short_form" placeholder="<?= trans("short_form"); ?>" value="<?= esc($language->short_form); ?>" maxlength="200" required>
                        <small>(Ex: en)</small>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans("language_code"); ?> </label>
                        <input type="text" class="form-control" name="language_code" placeholder="<?= trans("language_code"); ?>" value="<?= esc($language->language_code); ?>" maxlength="200" required>
                        <small>(Ex: en_us)</small>
                    </div>
                    <div class="form-group">
                        <label><?= trans('order'); ?></label>
                        <input type="number" class="form-control" name="language_order" placeholder="<?= trans('order'); ?>" value="<?= esc($language->language_order); ?>" min="1" required>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                <label><?= trans('text_direction'); ?></label>
                            </div>
                            <div class="col-sm-6 col-xs-12 col-option">
                                <input type="radio" id="rb_type_1" name="text_direction" value="ltr" class="square-purple" <?= ($language->text_direction == "ltr") ? 'checked' : ''; ?>>
                                <label for="rb_type_1" class="cursor-pointer"><?= trans("left_to_right"); ?></label>
                            </div>
                            <div class="col-sm-6 col-xs-12 col-option">
                                <input type="radio" id="rb_type_2" name="text_direction" value="rtl" class="square-purple" <?= ($language->text_direction == "rtl") ? 'checked' : ''; ?>>
                                <label for="rb_type_2" class="cursor-pointer"><?= trans("right_to_left"); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><?= trans('text_editor_language'); ?></label>
                        <select name="text_editor_lang" class="form-control" required>
                            <option value=""><?= trans("select"); ?></option>
                            <?php if (!empty($edLangArray)):
                                foreach ($edLangArray as $edLang): ?>
                                    <option value="<?= $edLang['short']; ?>" <?= $edLang['short'] == $language->text_editor_lang ? 'selected' : ''; ?>><?= $edLang['name']; ?></option>
                                <?php endforeach;
                            endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans("flag"); ?></label>
                        <div class="display-block m-b-15">
                            <img src="<?= base_url($language->flag_path); ?>" alt=""/>
                        </div>
                        <div class="display-block">
                            <a class='btn btn-default btn-sm btn-file-upload'>
                                <i class="fa fa-image text-muted"></i>&nbsp;&nbsp;<?= trans("select_image"); ?>
                                <input type="file" name="file" size="40" accept=".png, .jpg, .jpeg, .gif" onchange="$('#upload-file-info-flag').html($(this).val().replace(/.*[\/\\]/, ''));">
                            </a>
                            <br>
                            <span class='label label-default label-file-upload' id="upload-file-info-flag"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-3 col-xs-12">
                                <label><?= trans('status'); ?></label>
                            </div>
                            <div class="col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="status" value="1" id="status1" class="square-purple" <?= $language->status == 1 ? 'checked' : ''; ?>>&nbsp;&nbsp;
                                <label for="status1" class="option-label"><?= trans('active'); ?></label>
                            </div>
                            <div class="col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="status" value="0" id="status2" class="square-purple" <?= $language->status != 1 ? 'checked' : ''; ?>>&nbsp;&nbsp;
                                <label for="status2" class="option-label"><?= trans('inactive'); ?></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
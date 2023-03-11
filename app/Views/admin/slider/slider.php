<?php $animationArray = ['none', 'bounce', 'flash', 'pulse', 'rubberBand', 'shake', 'swing', 'tada', 'wobble', 'jello', 'heartBeat', 'bounceIn', 'bounceInDown', 'bounceInLeft',
    'bounceInRight', 'bounceInUp', 'fadeIn', 'fadeInDown', 'fadeInDownBig', 'fadeInLeft', 'fadeInLeftBig', 'fadeInRight', 'fadeInRightBig', 'fadeInUp', 'fadeInUpBig', 'flip',
    'flipInX', 'flipInY', 'lightSpeedIn', 'rotateIn', 'rotateInDownLeft', 'rotateInDownRight', 'rotateInUpLeft', 'rotateInUpRight', 'slideInUp', 'slideInDown', 'slideInLeft',
    'slideInRight', 'zoomIn', 'zoomInDown', 'zoomInLeft', 'zoomInRight', 'zoomInUp', 'hinge', 'jackInTheBox', 'rollIn']; ?>

<div class="row">
    <div class="col-lg-5 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('add_slider_item'); ?></h3>
            </div>
            <form action="<?= base_url('AdminController/addSliderItemPost'); ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label><?= trans("language"); ?></label>
                        <select name="lang_id" class="form-control">
                            <?php foreach ($activeLanguages as $language): ?>
                                <option value="<?= $language->id; ?>" <?= selectedLangId() == $language->id ? 'selected' : ''; ?>><?= esc($language->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('title'); ?></label>
                        <input type="text" class="form-control" name="title" placeholder="<?= trans('title'); ?>" value="<?= old('title'); ?>">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('description'); ?></label>
                        <textarea name="description" class="form-control" placeholder="<?= trans('description'); ?>"><?= old('description'); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('link'); ?></label>
                        <input type="text" class="form-control" name="link" placeholder="<?= trans('link'); ?>" value="<?= old('link'); ?>">
                    </div>
                    <div class="row row-form">
                        <div class="col-sm-12 col-md-6 col-form">
                            <div class="form-group">
                                <label class="control-label"><?= trans('order'); ?></label>
                                <input type="number" class="form-control" name="item_order" placeholder="<?= trans('order'); ?>" value="<?= old('item_order'); ?>">
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-form">
                            <div class="form-group">
                                <label class="control-label"><?= trans('button_text'); ?></label>
                                <input type="text" class="form-control" name="button_text" placeholder="<?= trans('button_text'); ?>" value="<?= old('button_text'); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row row-form">
                        <div class="col-sm-12 col-md-4 col-form">
                            <div class="form-group">
                                <label class="control-label"><?= trans('text_color'); ?></label>
                                <input type="color" class="form-control" name="text_color" value="#ffffff">
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4 col-form">
                            <div class="form-group">
                                <label class="control-label"><?= trans('button_color'); ?></label>
                                <input type="color" class="form-control" name="button_color" value="#222222">
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4 col-form">
                            <div class="form-group">
                                <label class="control-label"><?= trans('button_text_color'); ?></label>
                                <input type="color" class="form-control" name="button_text_color" value="#ffffff">
                            </div>
                        </div>
                    </div>
                    <div class="row row-form">
                        <div class="col-sm-12" style="padding-left: 7.5px;">
                            <label><?= trans("animations"); ?></label>
                        </div>
                        <div class="col-sm-12 col-md-4 col-form">
                            <div class="form-group">
                                <label><?= trans("title"); ?></label>
                                <select name="animation_title" class="form-control">
                                    <?php foreach ($animationArray as $animation): ?>
                                        <option value="<?= $animation; ?>" <?= $animation == 'fadeInUp' ? 'selected' : ''; ?>><?= $animation; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4 col-form">
                            <div class="form-group">
                                <label><?= trans("description"); ?></label>
                                <select name="animation_description" class="form-control">
                                    <?php foreach ($animationArray as $animation): ?>
                                        <option value="<?= $animation; ?>" <?= $animation == 'fadeInUp' ? 'selected' : ''; ?>><?= $animation; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4 col-form">
                            <div class="form-group">
                                <label><?= trans("button"); ?></label>
                                <select name="animation_button" class="form-control">
                                    <?php foreach ($animationArray as $animation): ?>
                                        <option value="<?= $animation; ?>" <?= $animation == 'fadeInUp' ? 'selected' : ''; ?>><?= $animation; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('image'); ?> (1920x600)</label>
                        <div class="display-block">
                            <a class='btn btn-success btn-sm btn-file-upload'>
                                <?= trans('select_image'); ?>
                                <input type="file" name="file" size="40" accept=".png, .jpg, .jpeg, .gif" required onchange="showPreviewImage(this);">
                            </a>
                        </div>
                        <img src="<?= IMG_BASE64_1x1; ?>" id="img_preview_file" class="img-file-upload-preview">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('image'); ?>&nbsp;(for mobile) (768x500)</label>
                        <div class="display-block">
                            <a class='btn btn-success btn-sm btn-file-upload'>
                                <?= trans('select_image'); ?>
                                <input type="file" name="file_mobile" size="40" accept=".png, .jpg, .jpeg, .gif" required onchange="showPreviewImage(this);">
                            </a>
                        </div>
                        <img src="<?= IMG_BASE64_1x1; ?>" id="img_preview_file_mobile" class="img-file-upload-preview">
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('add_slider_item'); ?></button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-7 col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('slider_items'); ?></h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped cs_datatable_lang" role="grid" aria-describedby="example1_info">
                                <thead>
                                <tr role="row">
                                    <th width="20"><?= trans('id'); ?></th>
                                    <th><?= trans('image'); ?></th>
                                    <th><?= trans('language'); ?></th>
                                    <th><?= trans('order'); ?></th>
                                    <th class="th-options"><?= trans('options'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($sliderItems)):
                                    foreach ($sliderItems as $item): ?>
                                        <tr>
                                            <td><?= esc($item->id); ?></td>
                                            <td><img src="<?= base_url($item->image); ?>" alt="" style="width: 200px;"/></td>
                                            <td>
                                                <?php $language = getLanguage($item->lang_id);
                                                if (!empty($language)) {
                                                    echo $language->name;
                                                } ?>
                                            </td>
                                            <td><?= $item->item_order; ?></td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn bg-purple dropdown-toggle btn-select-option" type="button" data-toggle="dropdown"><?= trans('select_option'); ?><span class="caret"></span></button>
                                                    <ul class="dropdown-menu options-dropdown">
                                                        <li><a href="<?= adminUrl('edit-slider-item/' . $item->id); ?>"><i class="fa fa-edit option-icon"></i><?= trans('edit'); ?></a></li>
                                                        <li><a href="javascript:void(0)" onclick="deleteItem('AdminController/deleteSliderItemPost','<?= $item->id; ?>','<?= trans("confirm_slider_item", true); ?>');"><i class="fa fa-trash option-icon"></i><?= trans('delete'); ?></a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach;
                                endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-5 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('slider_settings'); ?></h3>
            </div>
            <form action="<?= base_url('AdminController/editSliderSettingsPost'); ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                <label><?= trans('status'); ?></label>
                            </div>
                            <div class="col-sm-6 col-xs-12 col-option">
                                <input type="radio" name="slider_status" value="1" id="slider_status_1" class="square-purple" <?= $generalSettings->slider_status == 1 ? 'checked' : ''; ?>>
                                <label for="slider_status_1" class="option-label"><?= trans('enable'); ?></label>
                            </div>
                            <div class="col-sm-6 col-xs-12 col-option">
                                <input type="radio" name="slider_status" value="0" id="slider_status_2" class="square-purple" <?= $generalSettings->slider_status != 1 ? 'checked' : ''; ?>>
                                <label for="slider_status_2" class="option-label"><?= trans('disable'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                <label><?= trans('type'); ?></label>
                            </div>
                            <div class="col-sm-6 col-xs-12 col-option">
                                <input type="radio" name="slider_type" value="full_width" id="slider_type_1" class="square-purple" <?= $generalSettings->slider_type == 'full_width' ? 'checked' : ''; ?>>
                                <label for="slider_type_1" class="option-label"><?= trans('full_width'); ?></label>
                            </div>
                            <div class="col-sm-6 col-xs-12 col-option">
                                <input type="radio" name="slider_type" value="boxed" id="slider_type_2" class="square-purple" <?= $generalSettings->slider_type != 'full_width' ? 'checked' : ''; ?>>
                                <label for="slider_type_2" class="option-label"><?= trans('boxed'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                <label><?= trans('effect'); ?></label>
                            </div>
                            <div class="col-sm-6 col-xs-12 col-option">
                                <input type="radio" name="slider_effect" value="fade" id="slider_effect_1" class="square-purple" <?= $generalSettings->slider_effect == 'fade' ? 'checked' : ''; ?>>
                                <label for="slider_effect_1" class="option-label"><?= trans("fade"); ?></label>
                            </div>
                            <div class="col-sm-6 col-xs-12 col-option">
                                <input type="radio" name="slider_effect" value="slide" id="slider_effect_2" class="square-purple" <?= $generalSettings->slider_effect != 'fade' ? 'checked' : ''; ?>>
                                <label for="slider_effect_2" class="option-label"><?= trans("slide"); ?></label>
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
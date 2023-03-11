<div class="row">
    <div class="col-lg-8 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans('add_category'); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('categories'); ?>" class="btn btn-success btn-add-new">
                        <i class="fa fa-list-ul"></i>&nbsp;&nbsp;<?= trans('categories'); ?>
                    </a>
                </div>
            </div>
            <form action="<?= base_url('CategoryController/addCategoryPost'); ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <input type="hidden" name="parent_id" value="0">
                <div class="box-body">
                    <?php foreach ($activeLanguages as $language): ?>
                        <div class="form-group">
                            <label><?= trans("category_name"); ?> (<?= $language->name; ?>)</label>
                            <input type="text" class="form-control" name="name_lang_<?= $language->id; ?>" placeholder="<?= trans("category_name"); ?>" maxlength="255" required>
                        </div>
                    <?php endforeach; ?>
                    <div class="form-group">
                        <label class="control-label"><?= trans("slug"); ?>
                            <small>(<?= trans("slug_exp"); ?>)</small>
                        </label>
                        <input type="text" class="form-control" name="slug_lang" placeholder="<?= trans("slug"); ?>">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('title'); ?> (<?= trans('meta_tag'); ?>)</label>
                        <input type="text" class="form-control" name="title_meta_tag" placeholder="<?= trans('title'); ?> (<?= trans('meta_tag'); ?>)" value="<?= old('title_meta_tag'); ?>">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('description'); ?> (<?= trans('meta_tag'); ?>)</label>
                        <input type="text" class="form-control" name="description" placeholder="<?= trans('description'); ?> (<?= trans('meta_tag'); ?>)" value="<?= old('description'); ?>">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('keywords'); ?> (<?= trans('meta_tag'); ?>)</label>
                        <input type="text" class="form-control" name="keywords" placeholder="<?= trans('keywords'); ?> (<?= trans('meta_tag'); ?>)" value="<?= old('keywords'); ?>">
                    </div>
                    <div class="form-group">
                        <label><?= trans('order'); ?></label>
                        <input type="number" class="form-control" name="category_order" placeholder="<?= trans('order'); ?>" value="<?= old('category_order'); ?>" min="1" max="99999" required>
                    </div>
                    <div class="form-group">
                        <label><?= trans('parent_category'); ?></label>
                        <select class="form-control" name="category_id[]" onchange="getSubCategories(this.value, 0);" required>
                            <option value="0"><?= trans('none'); ?></option>
                            <?php if (!empty($parentCategories)):
                                foreach ($parentCategories as $parentCategory): ?>
                                    <option value="<?= $parentCategory->id; ?>"><?= getCategoryName($parentCategory); ?></option>
                                <?php endforeach;
                            endif; ?>
                        </select>
                        <div id="category_select_container"></div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4 col-xs-12">
                                <label><?= trans('visibility'); ?></label>
                            </div>
                            <div class="col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="visibility" value="1" id="visibility_1" class="square-purple" checked>
                                <label for="visibility_1" class="option-label"><?= trans('show'); ?></label>
                            </div>
                            <div class="col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="visibility" value="0" id="visibility_2" class="square-purple">
                                <label for="visibility_2" class="option-label"><?= trans('hide'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4 col-xs-12">
                                <label><?= trans('show_on_main_menu'); ?></label>
                            </div>
                            <div class="col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="show_on_main_menu" value="1" id="show_on_main_menu_1" class="square-purple" checked>
                                <label for="show_on_main_menu_1" class="option-label"><?= trans('yes'); ?></label>
                            </div>
                            <div class="col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="show_on_main_menu" value="0" id="show_on_main_menu_2" class="square-purple">
                                <label for="show_on_main_menu_2" class="option-label"><?= trans('no'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4 col-xs-12">
                                <label><?= trans('show_image_on_main_menu'); ?></label>
                            </div>
                            <div class="col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="show_image_on_main_menu" value="1" id="show_image_on_main_menu_1" class="square-purple">
                                <label for="show_image_on_main_menu_1" class="option-label"><?= trans('yes'); ?></label>
                            </div>
                            <div class="col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="show_image_on_main_menu" value="0" id="show_image_on_main_menu_2" class="square-purple" checked>
                                <label for="show_image_on_main_menu_2" class="option-label"><?= trans('no'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('image'); ?></label>
                        <div class="display-block">
                            <a class='btn btn-success btn-sm btn-file-upload'>
                                <?= trans('select_image'); ?>
                                <input type="file" id="Multifileupload" name="file" size="40" accept=".png, .jpg, .jpeg, .gif">
                            </a>
                        </div>
                        <div id="MultidvPreview" class="image-preview"></div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('add_category'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
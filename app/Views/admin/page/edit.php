<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('update_page'); ?></h3>
            </div>
            <form action="<?= base_url('AdminController/editPagePost'); ?>" method="post">
                <?= csrf_field(); ?>
                <input type="hidden" name="id" value="<?= $page->id; ?>">
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label"><?= trans('title'); ?></label>
                        <input type="text" class="form-control" name="title" placeholder="<?= trans('title'); ?>" value="<?= esc($page->title); ?>" required>
                    </div>
                    <?php if (empty($page->page_default_name)): ?>
                        <div class="form-group">
                            <label class="control-label"><?= trans("slug"); ?>
                                <small>(<?= trans("slug_exp"); ?>)</small>
                            </label>
                            <input type="text" class="form-control" name="slug" placeholder="<?= trans("slug"); ?>" value="<?= esc($page->slug); ?>">
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="slug" value="<?= esc($page->slug); ?>">
                    <?php endif; ?>
                    <div class="form-group">
                        <label class="control-label"><?= trans("description"); ?> (<?= trans('meta_tag'); ?>)</label>
                        <input type="text" class="form-control" name="description" placeholder="<?= trans("description"); ?> (<?= trans('meta_tag'); ?>)" value="<?= esc($page->description); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('keywords'); ?> (<?= trans('meta_tag'); ?>)</label>
                        <input type="text" class="form-control" name="keywords" placeholder="<?= trans('keywords'); ?> (<?= trans('meta_tag'); ?>)" value="<?= esc($page->keywords); ?>">
                    </div>

                    <div class="form-group">
                        <label><?= trans("language"); ?></label>
                        <select name="lang_id" class="form-control" style="max-width: 600px;">
                            <?php foreach ($activeLanguages as $language): ?>
                                <option value="<?= $language->id; ?>" <?= $page->lang_id == $language->id ? 'selected' : ''; ?>><?= esc($language->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><?= trans('order'); ?></label>
                        <input type="number" class="form-control" name="page_order" placeholder="<?= trans('order'); ?>" value="<?= $page->page_order; ?>" min="1" style="max-width: 600px;">
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-3 col-xs-12">
                                <label><?= trans('location'); ?></label>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="location" value="top_menu" id="menu_top_menu" class="square-purple" <?= $page->location == 'top_menu' ? 'checked' : ''; ?>>
                                <label for="menu_top_menu" class="option-label"><?= trans('top_menu'); ?></label>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="location" value="quick_links" id="menu_quick_links" class="square-purple" <?= $page->location == 'quick_links' ? 'checked' : ''; ?>>
                                <label for="menu_quick_links" class="option-label"><?= trans('footer_quick_links'); ?></label>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="location" value="information" id="menu_information" class="square-purple" <?= $page->location == 'information' ? 'checked' : ''; ?>>
                                <label for="menu_information" class="option-label"><?= trans('footer_information'); ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-3 col-xs-12">
                                <label><?= trans('visibility'); ?></label>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="visibility" value="1" id="page_enabled" class="square-purple" <?= $page->visibility == 1 ? 'checked' : ''; ?>>
                                <label for="page_enabled" class="option-label"><?= trans('show'); ?></label>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="visibility" value="0" id="page_disabled" class="square-purple" <?= $page->visibility == 0 ? 'checked' : ''; ?>>
                                <label for="page_disabled" class="option-label"><?= trans('hide'); ?></label>
                            </div>
                        </div>
                    </div>

                    <?php if ($page->page_default_name != 'blog' && $page->page_default_name != 'contact' && $page->page_default_name != 'shops'): ?>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-3 col-xs-12">
                                    <label><?= trans('show_title'); ?></label>
                                </div>
                                <div class="col-md-2 col-sm-4 col-xs-12 col-option">
                                    <input type="radio" name="title_active" value="1" id="title_enabled" class="square-purple" <?= $page->title_active == 1 ? 'checked' : ''; ?>>
                                    <label for="title_enabled" class="option-label"><?= trans('yes'); ?></label>
                                </div>
                                <div class="col-md-2 col-sm-4 col-xs-12 col-option">
                                    <input type="radio" name="title_active" value="0" id="title_disabled" class="square-purple" <?= $page->title_active == 0 ? 'checked' : ''; ?>>
                                    <label for="title_disabled" class="option-label"><?= trans('no'); ?></label>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <input type="hidden" value="1" name="title_active">
                    <?php endif;
                    if ($page->page_default_name != 'blog' && $page->page_default_name != 'contact' && $page->page_default_name != 'shops'): ?>
                        <div class="form-group" style="margin-top: 30px;">
                            <label><?= trans('content'); ?></label>
                            <div class="row">
                                <div class="col-sm-12 m-b-5">
                                    <button type="button" class="btn btn-success btn-file-manager" data-image-type="editor" data-toggle="modal" data-target="#imageFileManagerModal"><i class="fa fa-image"></i>&nbsp;&nbsp;<?= trans("add_image"); ?></button>
                                </div>
                            </div>
                            <textarea class="form-control tinyMCE" name="page_content"><?= $page->page_content; ?></textarea>
                        </div>
                    <?php else: ?>
                        <input type="hidden" value="" name="page_content">
                    <?php endif; ?>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= view('admin/includes/_image_file_manager'); ?>
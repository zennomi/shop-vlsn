<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans("update_post"); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('blog-posts'); ?>" class="btn btn-success btn-add-new">
                        <i class="fa fa-list-ul"></i>&nbsp;&nbsp;<?= trans('posts'); ?>
                    </a>
                </div>
            </div>
            <form action="<?= base_url('BlogController/editPostPost'); ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <input type="hidden" name="id" value="<?= esc($post->id); ?>">
                    <div class="form-group">
                        <label class="control-label"><?= trans('title'); ?></label>
                        <input type="text" class="form-control" name="title" placeholder="<?= trans('title'); ?>" value="<?= esc($post->title); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('slug'); ?>
                            <small>(<?= trans('slug_exp'); ?>)</small>
                        </label>
                        <input type="text" class="form-control" name="slug" placeholder="<?= trans('slug'); ?>" value="<?= esc($post->slug); ?>">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('summary'); ?> & <?= trans("description"); ?> (<?= trans('meta_tag'); ?>)</label>
                        <textarea class="form-control text-area" name="summary" placeholder="<?= trans('summary'); ?> & <?= trans("description"); ?> (<?= trans('meta_tag'); ?>)"><?= esc($post->summary); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('keywords'); ?> (<?= trans('meta_tag'); ?>)</label>
                        <input type="text" class="form-control" name="keywords" placeholder="<?= trans('keywords'); ?> (<?= trans('meta_tag'); ?>)" value="<?= esc($post->keywords); ?>">
                    </div>
                    <div class="form-group">
                        <label><?= trans("language"); ?></label>
                        <select name="lang_id" class="form-control max-600" onchange="getBlogCategoriesByLang(this.value);">
                            <?php foreach ($activeLanguages as $language): ?>
                                <option value="<?= $language->id; ?>" <?= $post->lang_id == $language->id ? 'selected' : ''; ?>><?= esc($language->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('category'); ?></label>
                        <select id="categories" name="category_id" class="form-control max-600" required>
                            <option value=""><?= trans('select_category'); ?></option>
                            <?php if (!empty($categories)):
                                foreach ($categories as $item): ?>
                                    <option value="<?= esc($item->id); ?>" <?= $item->id == $post->category_id ? 'selected' : ''; ?>><?= esc($item->name); ?></option>
                                <?php endforeach;
                            endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="max-600">
                                    <label class="control-label"><?= trans('tags'); ?></label>
                                    <input id="tags_1" type="text" name="tags" class="form-control tags" value="<?= esc($tags); ?>"/>
                                    <small>(<?= trans('type_tag'); ?>)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('image'); ?></label>
                        <?php if (!empty($post->image_default)): ?>
                            <div id="blog_select_image_container" class="post-select-image-container">
                                <img src="<?= getBlogImageURL($post, 'image_small'); ?>" alt="">
                                <a id="btn_delete_blog_main_image_database" class="btn btn-danger btn-sm btn-delete-selected-file-image" data-post-id="<?= $post->id; ?>"><i class="fa fa-times"></i></a>
                            </div>
                        <?php else: ?>
                            <div id="blog_select_image_container" class="post-select-image-container">
                                <a class="btn-select-image" data-image-type="main" data-toggle="modal" data-target="#imageFileManagerModal">
                                    <div class="btn-select-image-inner"><i class="fa fa-image"></i>
                                        <button class="btn"><?= trans("select_image"); ?></button>
                                    </div>
                                </a>
                            </div>
                        <?php endif; ?>
                        <input type="hidden" name="blog_image_id" id="blog_image_id">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('content'); ?></label>
                        <div class="row">
                            <div class="col-sm-12 m-b-5">
                                <button type="button" class="btn btn-success btn-file-manager" data-image-type="editor" data-toggle="modal" data-target="#imageFileManagerModal"><i class="fa fa-image"></i>&nbsp;&nbsp;<?= trans("add_image"); ?></button>
                            </div>
                        </div>
                        <textarea class="form-control tinyMCE" name="content"><?= $post->content; ?></textarea>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= view('admin/includes/_image_file_manager'); ?>

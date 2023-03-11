<div class="box">
    <div class="box-header with-border">
        <div class="left">
            <h3 class="box-title"><?= trans('categories'); ?></h3>
        </div>
        <div class="right">
            <a href="<?= adminUrl('add-category'); ?>" class="btn btn-success btn-add-new">
                <i class="fa fa-plus"></i>&nbsp;&nbsp;<?= trans('add_category'); ?>
            </a>
        </div>
    </div>
    <div class="box-body">
        <div class="col-sm-12">
            <div class="row">
                <div class="category-filters">
                    <?php if (countItems($activeLanguages) > 1): ?>
                        <div class="item-filter">
                            <label><?= trans("language"); ?></label>
                            <select name="lang_id" class="form-control" onchange="window.location.href = '<?= adminUrl(); ?>/categories?lang='+this.value" style="max-width: 600px;">
                                <?php foreach ($activeLanguages as $language): ?>
                                    <option value="<?= $language->id; ?>" <?= $language->id == $lang ? 'selected' : ''; ?>><?= $language->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row">
                <div class="categories-panel-group nested-sortable">
                    <?php if (!empty($parentCategories)):
                        foreach ($parentCategories as $parentCategory): ?>
                            <div class="panel-group" draggable="false">
                                <div data-item-id="<?= $parentCategory->id; ?>" class="panel panel-default">
                                    <div id="panel_heading_parent_<?= $parentCategory->id; ?>" class="panel-heading <?= !empty($parentCategory->has_subcategory) ? 'panel-heading-parent' : ''; ?>" data-item-id="<?= $parentCategory->id; ?>" href="#collapse_<?= $parentCategory->id; ?>">
                                        <div class="left">
                                            <?php if (!empty($parentCategory->has_subcategory)): ?>
                                                <i class="fa fa-caret-right"></i>
                                            <?php else: ?>
                                                <i class="fa fa-circle" style="font-size: 8px;"></i>
                                            <?php endif; ?>
                                            <?= getCategoryName($parentCategory); ?> <span class="id">( <?= trans("id") . ': ' . $parentCategory->id; ?>)</span></div>
                                        <div class="right">
                                            <?php if ($parentCategory->is_featured == 1): ?>
                                                <label class="label bg-teal"><?= trans("featured"); ?></label>
                                            <?php endif; ?>
                                            <?php if ($parentCategory->visibility == 1): ?>
                                                <label class="label bg-olive"><?= trans("visible"); ?></label>
                                            <?php else: ?>
                                                <label class="label bg-danger"><?= trans("hidden"); ?></label>
                                            <?php endif; ?>
                                            <div class="btn-group">
                                                <a href="<?= adminUrl('edit-category/' . $parentCategory->id); ?>" target="_blank" class="btn btn-sm btn-default btn-edit"><?= trans("edit"); ?></a>
                                                <a href="javascript:void(0)" class="btn btn-sm btn-default btn-delete" data-item-id="<?= $parentCategory->id; ?>"><i class="fa fa-trash-o"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if (!empty($parentCategory->has_subcategory)): ?>
                                        <div id="collapse_<?= $parentCategory->id; ?>" class="panel-collapse collapse" aria-expanded="true" style="">
                                            <div class="panel-body" style="padding: 20px 0;">
                                                <div class="spinner">
                                                    <div class="bounce1"></div>
                                                    <div class="bounce2"></div>
                                                    <div class="bounce3"></div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach;
                    endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('settings'); ?></h3>
            </div>
            <form action="<?= base_url('CategoryController/categorySettingsPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group m-b-30">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                <label><?= trans("sort_categories"); ?></label>
                            </div>
                            <div class="col-md-3 col-sm-12 col-option">
                                <input type="radio" name="sort_categories" value="category_order" id="by_category_order_1" class="square-purple" <?= $generalSettings->sort_categories == 'category_order' ? 'checked' : ''; ?>>
                                <label for="by_category_order_1" class="option-label"><?= trans('by_category_order'); ?></label>
                            </div>
                            <div class="col-md-3 col-sm-12 col-option">
                                <input type="radio" name="sort_categories" value="date" id="by_date_1" class="square-purple" <?= $generalSettings->sort_categories == 'date' ? 'checked' : ''; ?>>
                                <label for="by_date_1" class="option-label"><?= trans('by_date'); ?></label>
                            </div>
                            <div class="col-md-3 col-sm-12 col-option">
                                <input type="radio" name="sort_categories" value="date_desc" id="by_date_desc_1" class="square-purple" <?= $generalSettings->sort_categories == 'date_desc' ? 'checked' : ''; ?>>
                                <label for="by_date_desc_1" class="option-label"><?= trans('by_date'); ?>&nbsp;(DESC)</label>
                            </div>
                            <div class="col-md-3 col-sm-12 col-option">
                                <input type="radio" name="sort_categories" value="alphabetically" id="alphabetically_1" class="square-purple" <?= $generalSettings->sort_categories == 'alphabetically' ? 'checked' : ''; ?>>
                                <label for="alphabetically_1" class="option-label"><?= trans('alphabetically'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('sort_parent_categories_by_category_order'); ?></label>&nbsp;&nbsp;&nbsp;
                        <input type="checkbox" name="sort_parent_categories_by_order" value="1" class="square-purple" <?= $generalSettings->sort_parent_categories_by_order == 1 ? 'checked' : ''; ?>>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>
        <div class="alert alert-info alert-large">
            <strong><?= trans("warning"); ?>!</strong>&nbsp;&nbsp;<?= trans("warning_category_sort"); ?>
        </div>
    </div>
</div>
<script>
    $(document).on("click", ".panel .panel-heading", function (e) {
        if ($(e.target).is('div') || $(e.target).is('span') || $(e.target).is('.fa-caret-right') || $(e.target).is('.fa-caret-down')) {
            var id = $(this).attr('data-item-id');
            $('#collapse_' + id).collapse("toggle");
            $('.left .fa', this).toggleClass('fa-caret-right').toggleClass('fa-caret-down');
        }
    });
    $(document).on("click", ".panel .panel-heading .btn-delete", function (e) {
        var id = $(this).attr('data-item-id');
        deleteItem("CategoryController/deleteCategoryPost", id, "<?= trans("confirm_delete", true);?>");
    });

    $(document).on('click', '.panel-heading-parent', function (e) {
        var id = $(this).attr('data-item-id');
        if ($(e.target).hasClass('btn')) {
            return true;
        }
        if ($('#panel_heading_parent_' + id).hasClass('parent-panel-open')) {
            return false;
        }
        $('#collapse_' + id + ' .spinner').css('visibility', 'visible');
        var data = {
            'id': id,
            'lang_id': <?= clrNum($lang); ?>
        };
        $.ajax({
            url: MdsConfig.baseURL + '/CategoryController/loadCategories',
            type: 'POST',
            data: setAjaxData(data),
            success: function (response) {
                var obj = JSON.parse(response);
                if (obj.result == 1) {
                    setTimeout(function () {
                        $('#panel_heading_parent_' + id).addClass('parent-panel-open');
                        document.getElementById('collapse_' + id).innerHTML = obj.htmlContent;
                    }, 300);
                }
            }
        });
    });
</script>

<style>
    .btn-group-option {
        display: inline-block !important;
    }

    .spinner {
        visibility: hidden;
    }

    .spinner > div {
        width: 16px;
        height: 16px;
        background-color: #999;
    }

    .cursor-default {
        cursor: default !important;
    }
</style>

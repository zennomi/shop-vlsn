<div class="box">
    <div class="box-header with-border">
        <div class="left">
            <h3 class="box-title"><?= trans("blog_posts"); ?></h3>
        </div>
        <div class="right">
            <a href="<?= adminUrl('blog-add-post'); ?>" class="btn btn-success btn-add-new">
                <i class="fa fa-plus"></i>&nbsp;&nbsp;<?= trans('add_post'); ?>
            </a>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" role="grid">
                        <div class="row table-filter-container">
                            <div class="col-sm-12">
                                <form action="<?= adminUrl('blog-posts'); ?>" method="get">
                                    <div class="item-table-filter" style="width: 80px; min-width: 80px;">
                                        <label><?= trans("show"); ?></label>
                                        <select name="show" class="form-control">
                                            <option value="15" <?= inputGet('show', true) == '15' ? 'selected' : ''; ?>>15</option>
                                            <option value="30" <?= inputGet('show', true) == '30' ? 'selected' : ''; ?>>30</option>
                                            <option value="60" <?= inputGet('show', true) == '60' ? 'selected' : ''; ?>>60</option>
                                            <option value="100" <?= inputGet('show', true) == '100' ? 'selected' : ''; ?>>100</option>
                                        </select>
                                    </div>
                                    <div class="item-table-filter">
                                        <label><?= trans("language"); ?></label>
                                        <select name="lang_id" class="form-control" onchange="getParentCategoriesByLang(this.value);">
                                            <option value=""><?= trans("all"); ?></option>
                                            <?php foreach ($activeLanguages as $language): ?>
                                                <option value="<?= $language->id; ?>" <?= inputGet('lang_id') == $language->id ? 'selected' : ''; ?>><?= esc($language->name); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="item-table-filter">
                                        <label><?= trans("search"); ?></label>
                                        <input name="q" class="form-control" placeholder="<?= trans("search") ?>" type="search" value="<?= esc(inputGet('q', true)); ?>">
                                    </div>
                                    <div class="item-table-filter md-top-10" style="width: 65px; min-width: 65px;">
                                        <label style="display: block">&nbsp;</label>
                                        <button type="submit" class="btn bg-purple"><?= trans("filter"); ?></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <thead>
                        <tr role="row">
                            <th width="20"><?= trans('id'); ?></th>
                            <th><?= trans('title'); ?></th>
                            <th><?= trans('language'); ?></th>
                            <th><?= trans('category'); ?></th>
                            <th><?= trans('date'); ?></th>
                            <th class="th-options"><?= trans('options'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($posts)):
                            $model = new \App\Models\BlogModel();
                            foreach ($posts as $item):
                                $category = $model->getCategory($item->category_id); ?>
                                <tr>
                                    <td><?= esc($item->id); ?></td>
                                    <td class="td-product td-blog">
                                        <?php if (!empty($category)): ?>
                                            <a href="<?= generateUrl('blog') . '/' . $category->slug . '/' . $item->slug; ?>" target="_blank" class="a-table">
                                                <div class="img-table">
                                                    <img src="<?= getBlogImageURL($item, 'image_small'); ?>" alt=""/>
                                                </div>
                                                <?= esc($item->title); ?>
                                            </a>
                                        <?php else: ?>
                                            <div class="img-table">
                                                <img src="<?= getBlogImageURL($item, 'image_small'); ?>" alt=""/>
                                            </div>
                                            <?= esc($item->title);
                                        endif; ?>
                                    </td>
                                    <td>
                                        <?php $language = getLanguage($item->lang_id);
                                        if (!empty($language)):
                                            echo $language->name;
                                        endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($category)):
                                            echo esc($category->name);
                                        endif; ?>
                                    </td>
                                    <td><?= formatDate($item->created_at); ?></td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn bg-purple dropdown-toggle btn-select-option" type="button" data-toggle="dropdown"><?= trans('select_option'); ?>
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu options-dropdown">
                                                <li><a href="<?= adminUrl('edit-blog-post/' . $item->id); ?>"><i class="fa fa-edit option-icon"></i><?= trans('edit'); ?></a></li>
                                                <li><a href="javascript:void(0)" onclick="deleteItem('BlogController/deletePostPost','<?= $item->id; ?>','<?= trans("confirm_delete", true); ?>');"><i class="fa fa-trash option-icon"></i><?= trans('delete'); ?></a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach;
                        endif; ?>
                        </tbody>
                    </table>
                    <?php if (empty($posts)): ?>
                        <p class="text-center"><?= trans("no_records_found"); ?></p>
                    <?php endif; ?>
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="pull-right">
                                <?= view('partials/_pagination'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
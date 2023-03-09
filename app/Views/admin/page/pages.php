<div class="box">
    <div class="box-header with-border">
        <div class="left">
            <h3 class="box-title"><?= trans('pages'); ?></h3>
        </div>
        <div class="right">
            <a href="<?= adminUrl('add-page'); ?>" class="btn btn-success btn-add-new">
                <i class="fa fa-plus"></i>&nbsp;&nbsp;<?= trans('add_page'); ?>
            </a>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped cs_datatable_lang" role="grid">
                        <thead>
                        <tr role="row">
                            <th width="20"><?= trans('id'); ?></th>
                            <th><?= trans('title'); ?></th>
                            <th><?= trans('language'); ?></th>
                            <th><?= trans('location'); ?></th>
                            <th><?= trans('visibility'); ?></th>
                            <th><?= trans('page_type'); ?></th>
                            <th><?= trans('date'); ?></th>
                            <th class="th-options"><?= trans('options'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($pages)):
                            foreach ($pages as $item): ?>
                                <tr>
                                    <td><?= esc($item->id); ?></td>
                                    <td><?= esc($item->title); ?></td>
                                    <td><?php
                                        $language = getLanguage($item->lang_id);
                                        if (!empty($language)) {
                                            echo $language->name;
                                        } ?>
                                    </td>
                                    <td>
                                        <?php if ($item->location == 'top_menu') {
                                            echo trans("top_menu");
                                        } else {
                                            echo trans("footer_" . $item->location);
                                        } ?>
                                    </td>
                                    <td>
                                        <?php if ($item->visibility == 1): ?>
                                            <label class="label label-success"><i class="fa fa-eye"></i></label>
                                        <?php else: ?>
                                            <label class="label label-danger"><i class="fa fa-eye"></i></label>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($item->is_custom == 1): ?>
                                            <label class="label bg-teal"><?= trans('custom'); ?></label>
                                        <?php else: ?>
                                            <label class="label label-default"><?= trans('default'); ?></label>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= formatDate($item->created_at); ?></td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn bg-purple dropdown-toggle btn-select-option" type="button" data-toggle="dropdown"><?= trans('select_option'); ?>
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu options-dropdown">
                                                <li><a href="<?= adminUrl('edit-page/' . $item->id); ?>"><i class="fa fa-edit option-icon"></i><?= trans('edit'); ?></a></li>
                                                <?php if ($item->is_custom == 1): ?>
                                                    <li><a href="javascript:void(0)" onclick="deleteItem('AdminController/deletePagePost','<?= $item->id; ?>','<?= trans("confirm_delete", true); ?>');"><i class="fa fa-trash option-icon"></i><?= trans('delete'); ?></a></li>
                                                <?php endif; ?>
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
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans('custom_fields'); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('add-custom-field'); ?>" class="btn btn-success btn-add-new">
                        <i class="fa fa-plus"></i>&nbsp;&nbsp;<?= trans('add_custom_field'); ?>
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped data_table" role="grid" aria-describedby="example1_info">
                                <thead>
                                <tr role="row">
                                    <th width="20"><?= trans('id'); ?></th>
                                    <th><?= trans('name'); ?></th>
                                    <th><?= trans('type'); ?></th>
                                    <th>&nbsp;</th>
                                    <th><?= trans('required'); ?></th>
                                    <th><?= trans('order'); ?></th>
                                    <th><?= trans('status'); ?></th>
                                    <th class="th-options"><?= trans('options'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($fields)):
                                    foreach ($fields as $item): ?>
                                        <tr>
                                            <td><?= esc($item->id); ?></td>
                                            <td><?= parseSerializedNameArray($item->name_array, selectedLangId()); ?></td>
                                            <td><?= trans($item->field_type); ?></td>
                                            <td>
                                                <form action="<?= base_url('CategoryController/addRemoveCustomFieldFiltersPost'); ?>" method="post">
                                                    <?= csrf_field(); ?>
                                                    <input type="hidden" name="id" value="<?= $item->id; ?>">
                                                    <?php if ($item->field_type == 'checkbox' || $item->field_type == 'radio_button' || $item->field_type == 'dropdown'):
                                                        if ($item->is_product_filter == 1):?>
                                                            <button class="btn btn-sm btn-danger"><i class="fa fa-times"></i>&nbsp;<?= trans('remove_from_product_filters'); ?></button>
                                                        <?php else: ?>
                                                            <button class="btn btn-sm btn-success"><i class="fa fa-plus"></i>&nbsp;<?= trans('add_to_product_filters'); ?></button>
                                                        <?php endif;
                                                    endif; ?>
                                                </form>
                                            </td>
                                            <td> <?= $item->is_required == 1 ? trans("yes") : trans("no"); ?></td>
                                            <td><?= esc($item->field_order); ?></td>
                                            <td>
                                                <?php if ($item->status == 1): ?>
                                                    <label class="label bg-olive label-table"><?= trans('active'); ?></label>
                                                <?php else: ?>
                                                    <label class="label bg-danger label-table"><?= trans('inactive'); ?></label>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn bg-purple dropdown-toggle btn-select-option" type="button" data-toggle="dropdown"><?= trans('select_option'); ?>
                                                        <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu options-dropdown">
                                                        <li><a href="<?= adminUrl('edit-custom-field/' . $item->id); ?>"><i class="fa fa-edit option-icon"></i><?= trans('edit'); ?></a></li>
                                                        <li><a href="javascript:void(0)" onclick="deleteItem('CategoryController/deleteCustomFieldPost','<?= $item->id; ?>','<?= trans("confirm_delete", true); ?>');"><i class="fa fa-trash option-icon"></i><?= trans('delete'); ?></a></li>
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
<div class="row">
    <div class="col-sm-12">
        <?= view('dashboard/includes/_messages'); ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans("shipping_zones"); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= generateDashUrl('add_shipping_zone'); ?>" class="btn btn-success btn-add-new">
                        <i class="fa fa-plus"></i>&nbsp;&nbsp;<?= trans("add_shipping_zone"); ?>
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped dataTableNoSort" role="grid">
                                <thead>
                                <tr role="row">
                                    <th scope="col"><?= trans("zone_name"); ?></th>
                                    <th scope="col"><?= trans("regions"); ?></th>
                                    <th scope="col"><?= trans("shipping_methods"); ?></th>
                                    <th scope="col"><?= trans("options"); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($shippingZones)): ?>
                                    <?php foreach ($shippingZones as $shippingZone): ?>
                                        <tr>
                                            <td><?= @parseSerializedNameArray($shippingZone->name_array, selectedLangId()); ?></td>
                                            <td>
                                                <?php $locations = getShippingLocationsByZone($shippingZone->id);
                                                if (!empty($locations)):
                                                    $i = 0;
                                                    foreach ($locations as $location):
                                                        if (!empty($location->country_name) && !empty($location->state_name)):?>
                                                            <label class="badge badge-light badge-shipping-loc pull-left"><?= esc(getContinentNameByKey($location->continent_code)) . '/' . esc($location->country_name) . '/' . esc($location->state_name); ?></label>
                                                        <?php
                                                        elseif (!empty($location->country_name) && empty($location->state_name)):?>
                                                            <label class="badge badge-light badge-shipping-loc pull-left"><?= getContinentNameByKey($location->continent_code) . '/' . esc($location->country_name); ?></label>
                                                        <?php else: ?>
                                                            <label class="badge badge-light badge-shipping-loc pull-left"><?= getContinentNameByKey($location->continent_code); ?></label>
                                                        <?php endif;
                                                        $i++;
                                                    endforeach;
                                                endif; ?>
                                            </td>
                                            <td>
                                                <?php $methods = getShippingPaymentMethodsByZone($shippingZone->id);
                                                $i = 0;
                                                if (!empty($methods)):
                                                    foreach ($methods as $method): ?>
                                                        <span class="pull-left"><?= $i != 0 ? ', ' : ''; ?><?= @parseSerializedNameArray($method->name_array, selectedLangId()); ?></span>
                                                        <?php $i++;
                                                    endforeach;
                                                endif; ?>
                                            </td>
                                            <td style="width: 120px;">
                                                <div class="btn-group btn-group-option">
                                                    <a href="<?= generateDashUrl('edit_shipping_zone') . '/' . $shippingZone->id; ?>" class="btn btn-sm btn-default btn-edit" data-toggle="tooltip" title="<?= trans('edit'); ?>"><i class="fa fa-edit"></i></a>
                                                    <a href="javascript:void(0)" class="btn btn-sm btn-default btn-delete" data-toggle="tooltip" title="<?= trans('delete'); ?>" onclick="deleteItem('DashboardController/deleteShippingZonePost','<?= $shippingZone->id; ?>','<?= trans("confirm_delete", true); ?>');"><i class="fa fa-trash-o"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
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
    <div class="col-md-6 col-sm-12">
        <div class="box box-sm">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans("shipping_classes"); ?></h3>
                </div>
                <div class="right">
                    <a href="javascript:void(0)" class="btn btn-success btn-add-new" data-toggle="modal" data-target="#modalAddShippingClass">
                        <i class="fa fa-plus"></i>&nbsp;&nbsp;<?= trans("add_shipping_class"); ?>
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive table-delivery-times">
                            <table class="table table-bordered table-striped dataTableNoSort" role="grid">
                                <thead>
                                <tr role="row">
                                    <th scope="col"><?= trans("option"); ?></th>
                                    <th scope="col"><?= trans("status"); ?></th>
                                    <th scope="col"><?= trans("options"); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($shippingClasses)): ?>
                                    <?php foreach ($shippingClasses as $shippingClass): ?>
                                        <tr>
                                            <td><?= @parseSerializedNameArray($shippingClass->name_array, selectedLangId()); ?></td>
                                            <td>
                                                <?php if ($shippingClass->status == 1): ?>
                                                    <span class="text-success"><?= trans('active'); ?></span>
                                                <?php else: ?>
                                                    <span class="text-danger"><?= trans('inactive'); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="width: 120px;">
                                                <div class="btn-group btn-group-option">
                                                    <a href="javascript:void(0)" class="btn btn-sm btn-default btn-edit" data-toggle="modal" data-target="#modalEditShippingClass<?= $shippingClass->id; ?>"><span data-toggle="tooltip" title="<?= trans('edit'); ?>"><i class="fa fa-edit"></i></span></a>
                                                    <a href="javascript:void(0)" class="btn btn-sm btn-default btn-delete" data-toggle="tooltip" title="<?= trans('delete'); ?>" onclick="deleteItem('DashboardController/deleteShippingClassPost','<?= $shippingClass->id; ?>','<?= trans("confirm_delete", true); ?>');"><i class="fa fa-trash-o"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                        <div id="modalEditShippingClass<?= $shippingClass->id; ?>" class="modal fade" role="dialog">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                                                        <h4 class="modal-title"><?= trans("edit_shipping_class"); ?></h4>
                                                    </div>
                                                    <form action="<?= base_url('edit-shipping-class-post'); ?>" method="post">
                                                        <?= csrf_field(); ?>
                                                        <input type="hidden" name="id" value="<?= $shippingClass->id; ?>">
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label class="control-label"><?= trans("name"); ?></label>
                                                                <?php foreach ($activeLanguages as $language): ?>
                                                                    <input type="text" name="name_lang_<?= $language->id; ?>" value="<?= @parseSerializedNameArray($shippingClass->name_array, $language->id); ?>" class="form-control form-input m-b-5" placeholder="<?= esc($language->name); ?>" maxlength="255" required>
                                                                <?php endforeach; ?>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="row">
                                                                    <div class="col-sm-12 col-xs-12">
                                                                        <label><?= trans("status"); ?></label>
                                                                    </div>
                                                                    <div class="col-md-6 col-sm-12 col-custom-option">
                                                                        <div class="custom-control custom-radio">
                                                                            <input type="radio" name="status" value="1" id="status_<?= $shippingClass->id; ?>_1" class="custom-control-input" <?= $shippingClass->status == 1 ? 'checked' : ''; ?>>
                                                                            <label for="status_<?= $shippingClass->id; ?>_1" class="custom-control-label"><?= trans("enable"); ?></label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6 col-sm-12 col-custom-option">
                                                                        <div class="custom-control custom-radio">
                                                                            <input type="radio" name="status" value="0" id="status_<?= $shippingClass->id; ?>_2" class="custom-control-input" <?= $shippingClass->status != 1 ? 'checked' : ''; ?>>
                                                                            <label for="status_<?= $shippingClass->id; ?>_2" class="custom-control-label"><?= trans("disable"); ?></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-success"><?= trans("submit"); ?></button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach;
                                endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="alert alert-info alert-large">
            <?= trans("shipping_classes_exp"); ?>
        </div>
    </div>
    <div class="col-md-6 col-sm-12">
        <div class="box box-sm">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans("shipping_delivery_times"); ?></h3>
                </div>
                <div class="right">
                    <a href="javascript:void(0)" class="btn btn-success btn-add-new" data-toggle="modal" data-target="#modalAddDeliveryTime">
                        <i class="fa fa-plus"></i>&nbsp;&nbsp;<?= trans("add_delivery_time"); ?>
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive table-delivery-times">
                            <table class="table table-bordered table-striped dataTableNoSort" role="grid">
                                <thead>
                                <tr role="row">
                                    <th scope="col"><?= trans("option"); ?></th>
                                    <th scope="col"><?= trans("options"); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($shippingDeliveryTimes)): ?>
                                    <?php foreach ($shippingDeliveryTimes as $deliveryTime): ?>
                                        <tr>
                                            <td><?= @parseSerializedOptionArray($deliveryTime->option_array, selectedLangId()); ?></td>
                                            <td style="width: 120px;">
                                                <div class="btn-group btn-group-option">
                                                    <a href="javascript:void(0)" class="btn btn-sm btn-default btn-edit" data-toggle="modal" data-target="#modalEditDeliveryTime<?= $deliveryTime->id; ?>"><span data-toggle="tooltip" title="<?= trans('edit'); ?>"><i class="fa fa-edit"></i></span></a>
                                                    <a href="javascript:void(0)" class="btn btn-sm btn-default btn-delete" data-toggle="tooltip" title="<?= trans('delete'); ?>" onclick="deleteItem('DashboardController/deleteShippingDeliveryTimePost','<?= $deliveryTime->id; ?>','<?= trans("confirm_delete", true); ?>');"><i class="fa fa-trash-o"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                        <div id="modalEditDeliveryTime<?= $deliveryTime->id; ?>" class="modal fade" role="dialog">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                                                        <h4 class="modal-title"><?= trans("edit_delivery_time"); ?></h4>
                                                    </div>
                                                    <form action="<?= base_url('edit-shipping-delivery-time-post'); ?>" method="post">
                                                        <?= csrf_field(); ?>
                                                        <input type="hidden" name="id" value="<?= $deliveryTime->id; ?>">
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label class="control-label"><?= trans("option"); ?></label>
                                                                <?php foreach ($activeLanguages as $language): ?>
                                                                    <input type="text" name="option_lang_<?= $language->id; ?>" value="<?= @parseSerializedOptionArray($deliveryTime->option_array, $language->id); ?>" class="form-control form-input m-b-5" placeholder="<?= esc($language->name); ?>" maxlength="255" required>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-success"><?= trans("submit"); ?></button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach;
                                endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="alert alert-info alert-large">
            <?= trans("shipping_delivery_times_exp"); ?>
        </div>
    </div>
</div>

<div id="modalAddShippingClass" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                <h4 class="modal-title"><?= trans("add_shipping_class"); ?></h4>
            </div>
            <form action="<?= base_url('add-shipping-class-post'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label"><?= trans("name"); ?></label>
                        <?php foreach ($activeLanguages as $language): ?>
                            <input type="text" name="name_lang_<?= $language->id; ?>" class="form-control form-input m-b-5" placeholder="<?= esc($language->name); ?>" maxlength="255" required>
                        <?php endforeach; ?>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                <label><?= trans("status"); ?></label>
                            </div>
                            <div class="col-md-6 col-sm-12 col-custom-option">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="status" value="1" id="status_1" class="custom-control-input" checked>
                                    <label for="status_1" class="custom-control-label"><?= trans("enable"); ?></label>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12 col-custom-option">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="status" value="0" id="status_2" class="custom-control-input">
                                    <label for="status_2" class="custom-control-label"><?= trans("disable"); ?></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success"><?= trans("submit"); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="modalAddDeliveryTime" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                <h4 class="modal-title"><?= trans("add_delivery_time"); ?></h4>
            </div>
            <form action="<?= base_url('add-shipping-delivery-time-post'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label"><?= trans("option"); ?></label>
                        <?php foreach ($activeLanguages as $language): ?>
                            <input type="text" name="option_lang_<?= $language->id; ?>" class="form-control form-input m-b-5" placeholder="<?= esc($language->name); ?>" maxlength="255" required>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success"><?= trans("submit"); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .table-delivery-times .dataTables_length, .table-delivery-times .dataTables_filter {
        display: none;
    }
</style>



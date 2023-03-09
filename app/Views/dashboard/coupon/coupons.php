<div class="row">
    <div class="col-sm-12">
        <?= view('dashboard/includes/_messages'); ?>
    </div>
</div>
<div class="box">
    <div class="box-header with-border">
        <div class="left">
            <h3 class="box-title"><?= esc($title); ?></h3>
        </div>
        <div class="right">
            <a href="<?= generateDashUrl('add_coupon'); ?>" class="btn btn-success btn-add-new">
                <i class="fa fa-plus"></i>&nbsp;&nbsp;<?= trans("add_coupon"); ?>
            </a>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" role="grid">
                        <thead>
                        <tr role="row">
                            <th><?= trans("coupon_code"); ?></th>
                            <th><?= trans("discount_rate"); ?></th>
                            <th><?= trans("number_of_coupons"); ?></th>
                            <th><?= trans("expiry_date"); ?></th>
                            <th><?= trans("status"); ?></th>
                            <th><?= trans("date"); ?></th>
                            <th class="max-width-120"><?= trans('options'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($coupons)):
                            foreach ($coupons as $item): ?>
                                <tr>
                                    <td><?= esc($item->coupon_code); ?></td>
                                    <td><?= esc($item->discount_rate); ?>%</td>
                                    <td><?= esc($item->coupon_count); ?>&nbsp;<small class="text-danger">(<?= trans("used"); ?>:&nbsp;<b><?= getUsedCouponsCount($item->coupon_code); ?></b>)</small></td>
                                    <td><?= formatDate($item->expiry_date); ?>&nbsp;<span class="text-danger"></td>
                                    <td>
                                        <?php if (date('Y-m-d H:i:s') > $item->expiry_date): ?>
                                            <label class="label label-danger"><?= trans("expired"); ?></label>
                                        <?php else: ?>
                                            <label class="label label-success"><?= trans("active"); ?></label>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= formatDate($item->created_at); ?></td>
                                    <td style="width: 120px;">
                                        <div class="btn-group btn-group-option">
                                            <a href="<?= generateDashUrl('edit_coupon') . '/' . $item->id; ?>" class="btn btn-sm btn-default btn-edit" data-toggle="tooltip" title="<?= trans('edit'); ?>"><i class="fa fa-edit"></i></a>
                                            <a href="javascript:void(0)" class="btn btn-sm btn-default btn-delete" data-toggle="tooltip" title="<?= trans('delete'); ?>" onclick='deleteItem("DashboardController/deleteCouponPost","<?= $item->id; ?>","<?= trans("confirm_delete", true); ?>");'><i class="fa fa-trash-o"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach;
                        endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if (empty($coupons)): ?>
                    <p class="text-center">
                        <?= trans("no_records_found"); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?php if (!empty($coupons)): ?>
                    <div class="number-of-entries">
                        <span><?= trans("number_of_entries"); ?>:</span>&nbsp;&nbsp;<strong><?= $numRows; ?></strong>
                    </div>
                <?php endif; ?>
                <div class="table-pagination">
                    <?= view('partials/_pagination'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

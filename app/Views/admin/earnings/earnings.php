<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?= $title; ?></h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" role="grid">
                        <div class="row table-filter-container">
                            <div class="col-sm-12">
                                <form action="<?= adminUrl('earnings'); ?>" method="get">
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
                                        <label><?= trans("search"); ?></label>
                                        <input name="q" class="form-control" placeholder="<?= trans("order_number"); ?>" type="search" value="<?= esc(inputGet('q')); ?>">
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
                            <th><?= trans('id'); ?></th>
                            <th><?= trans('order'); ?></th>
                            <th><?= trans('user'); ?></th>
                            <th><?= trans('total'); ?></th>
                            <th><?= trans("vat"); ?></th>
                            <th><?= trans("commission"); ?></th>
                            <th><?= trans("discount_coupon"); ?></th>
                            <th><?= trans('shipping_cost'); ?></th>
                            <th><?= trans('earned_amount'); ?></th>
                            <th><?= trans('date'); ?></th>
                            <th class="max-width-120"><?= trans('options'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($earnings)):
                            foreach ($earnings as $item): ?>
                                <tr>
                                    <td><?= $item->id; ?></td>
                                    <td>#<?= $item->order_number; ?></td>
                                    <td>
                                        <?php $user = getUser($item->user_id);
                                        if (!empty($user)):?>
                                            <div class="table-orders-user">
                                                <a href="<?= generateProfileUrl($user->slug); ?>" target="_blank" class="table-link"><?= esc(getUsername($user)); ?></a>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= priceFormatted($item->sale_amount, $item->currency); ?></td>
                                    <td><?= priceFormatted($item->vat_amount, $item->currency); ?>&nbsp;<?= !empty($item->vat_rate) ? '(' . $item->vat_rate . '%)' : ''; ?></td>
                                    <td><?= priceFormatted($item->commission, $item->currency); ?>&nbsp;<?= !empty($item->commission_rate) ? '(' . $item->commission_rate . '%)' : ''; ?></td>
                                    <td>
                                        <?php if (!empty($item->coupon_discount)): ?>
                                            <span class="text-danger">-
                                            <?= priceFormatted($item->coupon_discount, $item->currency);
                                            if (!empty($order) && !empty($order->coupon_code)):
                                                echo ' (' . $order->coupon_code . ')';
                                            endif; ?>
                                        </span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= priceFormatted($item->shipping_cost, $item->currency); ?></td>
                                    <td>
                                        <?= priceFormatted($item->earned_amount, $item->currency);
                                        $order = getOrderByOrderNumber($item->order_number);
                                        if (!empty($order) && $order->payment_method == 'Cash On Delivery'):?>
                                            <span class="text-danger">(-<?= priceFormatted($item->earned_amount, $item->currency); ?>)</span><br><small class="text-danger"><?= trans("cash_on_delivery"); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= formatDate($item->created_at); ?></td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn bg-purple dropdown-toggle btn-select-option" type="button" data-toggle="dropdown"><?= trans('select_option'); ?>
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu options-dropdown">
                                                <li>
                                                    <a href="javascript:void(0)" onclick="deleteItem('EarningsController/deleteEarningPost','<?= $item->id; ?>','<?= trans("confirm_delete", true); ?>');"><i class="fa fa-trash option-icon"></i><?= trans('delete'); ?></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach;
                        endif; ?>
                        </tbody>
                    </table>
                    <?php if (empty($earnings)): ?>
                        <p class="text-center">
                            <?= trans("no_records_found"); ?>
                        </p>
                    <?php endif; ?>
                    <div class="col-sm-12 table-ft">
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
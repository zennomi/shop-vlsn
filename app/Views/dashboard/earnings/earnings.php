<div class="row">
    <div class="col-sm-12">
        <?= view('dashboard/includes/_messages'); ?>
    </div>
</div>
<div class="row m-b-30">
    <div class="col-sm-12">
        <div class="small-boxes-dashboard-earnings">
            <div class="small-boxes-dashboard">
                <div class="col-sm-12 col-xs-12 p-0">
                    <div class="small-box-dashboard">
                        <h3 class="total"><?= priceFormatted(user()->balance, $paymentSettings->default_currency); ?></h3>
                        <span class="text-muted"><?= trans("balance"); ?></span>
                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-cash-stack" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14 3H1a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1h-1z"/>
                            <path fill-rule="evenodd" d="M15 5H1v8h14V5zM1 4a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1H1z"/>
                            <path d="M13 5a2 2 0 0 0 2 2V5h-2zM3 5a2 2 0 0 1-2 2V5h2zm10 8a2 2 0 0 1 2-2v2h-2zM3 13a2 2 0 0 0-2-2v2h2zm7-4a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="box">
    <div class="box-header with-border">
        <div class="left">
            <h3 class="box-title"><?= esc($title); ?></h3>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <div class="row table-filter-container">
                        <div class="col-sm-12">
                            <form action="<?= generateDashUrl('earnings'); ?>" method="get">
                                <div class="item-table-filter">
                                    <label><?= trans("search"); ?></label>
                                    <input name="q" class="form-control" placeholder="<?= trans("order_id"); ?>" type="search" value="<?= esc(inputGet('q')); ?>">
                                </div>
                                <div class="item-table-filter md-top-10" style="width: 65px; min-width: 65px;">
                                    <label style="display: block">&nbsp;</label>
                                    <button type="submit" class="btn bg-purple btn-filter"><?= trans("filter"); ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <table class="table table-striped" role="grid">
                        <thead>
                        <tr role="row">
                            <th scope="col"><?= trans("order"); ?></th>
                            <th scope="col"><?= trans("total"); ?></th>
                            <th scope="col"><?= trans("vat"); ?></th>
                            <th scope="col"><?= trans("commission"); ?></th>
                            <th scope="col"><?= trans("discount_coupon"); ?></th>
                            <th scope="col"><?= trans("shipping_cost"); ?></th>
                            <th scope="col"><?= trans("earned_amount"); ?></th>
                            <th scope="col"><?= trans("date"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($earnings)): ?>
                            <?php foreach ($earnings as $earning):
                                $order = getOrderByOrderNumber($earning->order_number); ?>
                                <tr>
                                    <td>#<?= $earning->order_number; ?></td>
                                    <td><?= priceFormatted($earning->sale_amount, $earning->currency); ?></td>
                                    <td><?= priceFormatted($earning->vat_amount, $earning->currency); ?>&nbsp;<?= !empty($earning->vat_rate) ? '(' . $earning->vat_rate . '%)' : ''; ?></td>
                                    <td><?= priceFormatted($earning->commission, $earning->currency); ?>&nbsp;<?= !empty($earning->commission_rate) ? '(' . $earning->commission_rate . '%)' : ''; ?></td>
                                    <td>
                                        <?php if (!empty($earning->coupon_discount)): ?>
                                            <span class="text-danger">-
                                            <?= priceFormatted($earning->coupon_discount, $earning->currency);
                                            if (!empty($order) && !empty($order->coupon_code)):
                                                echo ' (' . $order->coupon_code . ')';
                                            endif; ?>
                                        </span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= priceFormatted($earning->shipping_cost, $earning->currency); ?></td>
                                    <td> <span class="text-success">
                                        <?= priceFormatted($earning->earned_amount, $earning->currency); ?>
                                        </span>
                                        <?php if (!empty($order) && $order->payment_method == 'Cash On Delivery'): ?>
                                            <span class="text-danger">(-<?= priceFormatted($earning->earned_amount, $earning->currency); ?>)</span><br><small class="text-danger"><?= trans("cash_on_delivery"); ?></small>
                                        <?php endif;
                                        if ($paymentSettings->currency_converter == 1 && $earning->exchange_rate > 0 && $earning->exchange_rate != 1):
                                            $totalEarned = getPrice($earning->earned_amount, 'decimal');
                                            $totalEarned = $totalEarned / $earning->exchange_rate;
                                            $totalEarned = number_format($totalEarned, 2, '.', ''); ?>
                                            <span>(<?= $defaultCurrency->code . ' ' . $totalEarned; ?>)</span>
                                        <?php endif;
                                        if ($earning->is_refunded == 1): ?>
                                            <br><span class="text-danger">(<?= trans("refund"); ?>)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= formatDate($earning->created_at); ?></td>
                                </tr>
                            <?php endforeach;
                        endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if (empty($earnings)): ?>
                    <p class="text-center">
                        <?= trans("no_records_found"); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?php if (!empty($earnings)): ?>
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
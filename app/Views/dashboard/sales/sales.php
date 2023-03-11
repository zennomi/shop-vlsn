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
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <div class="row table-filter-container">
                        <div class="col-sm-12">
                            <form action="<?= generateDashUrl('sales'); ?>" method="get">
                                <?php if (!empty(inputGet('st'))): ?>
                                    <input type="hidden" name="st" value="<?= esc(inputGet('st')); ?>">
                                <?php endif;
                                if ($page == 'sales'): ?>
                                    <div class="item-table-filter">
                                        <label><?= trans("payment_status"); ?></label>
                                        <select name="payment_status" class="form-control custom-select">
                                            <option value="" selected><?= trans("all"); ?></option>
                                            <option value="payment_received" <?= inputGet('payment_status') == 'payment_received' ? 'selected' : ''; ?>><?= trans("payment_received"); ?></option>
                                            <option value="awaiting_payment" <?= inputGet('payment_status') == 'awaiting_payment' ? 'selected' : ''; ?>><?= trans("awaiting_payment"); ?></option>
                                        </select>
                                    </div>
                                <?php endif; ?>
                                <div class="item-table-filter">
                                    <label><?= trans("search"); ?></label>
                                    <input name="q" class="form-control" placeholder="<?= trans("sale_id"); ?>" type="search" value="<?= strSlug(esc(inputGet('q'))); ?>">
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
                            <th scope="col"><?= trans("sale"); ?></th>
                            <th scope="col"><?= trans("total"); ?></th>
                            <th scope="col"><?= trans("payment"); ?></th>
                            <th scope="col"><?= trans("status"); ?></th>
                            <th scope="col"><?= trans("date"); ?></th>
                            <th scope="col"><?= trans("options"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($sales)): ?>
                            <?php foreach ($sales as $sale):
                                $finalPrice = getSellerFinalPrice($sale->id);
                                if (!empty($sale)):?>
                                    <tr>
                                        <td>#<?= $sale->order_number; ?></td>
                                        <td><?= priceFormatted($finalPrice, $sale->price_currency); ?></td>
                                        <td>
                                            <?php if ($sale->status == 2):
                                                echo trans("cancelled");
                                            else:
                                                if ($sale->payment_status == 'payment_received'):
                                                    echo trans("payment_received");
                                                else:
                                                    echo trans("awaiting_payment");
                                                endif;
                                            endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($sale->status == 2): ?>
                                                <label class="label label-danger"><?= trans("cancelled"); ?></label>
                                            <?php else:
                                                if ($page == 'sales'): ?>
                                                    <label class="label label-success"><?= trans("order_processing"); ?></label>
                                                <?php else: ?>
                                                    <label class="label label-default"><?= trans("completed"); ?></label>
                                                <?php endif;
                                            endif; ?>
                                        </td>
                                        <td><?= formatDate($sale->created_at); ?></td>
                                        <td>
                                            <a href="<?= generateDashUrl('sale'); ?>/<?= esc($sale->order_number); ?>" class="btn btn-sm btn-default btn-details"><i class="fa fa-info-circle" aria-hidden="true"></i><?= trans("details"); ?></a>
                                        </td>
                                    </tr>
                                <?php endif;
                            endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if (empty($sales)): ?>
                    <p class="text-center">
                        <?= trans("no_records_found"); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?php if (!empty($sales)): ?>
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
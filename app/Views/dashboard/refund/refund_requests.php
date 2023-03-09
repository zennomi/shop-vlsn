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
                    <table class="table table-striped" role="grid">
                        <thead>
                        <tr role="row">
                            <th scope="col"><?= trans("product"); ?></th>
                            <th scope="col"><?= trans("total"); ?></th>
                            <th scope="col"><?= trans("buyer"); ?></th>
                            <th scope="col"><?= trans("status"); ?></th>
                            <th scope="col"><?= trans("updated"); ?></th>
                            <th scope="col"><?= trans("date"); ?></th>
                            <th scope="col"><?= trans("options"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($refundRequests)):
                            foreach ($refundRequests as $request):
                                $product = getOrderProduct($request->order_product_id);
                                if (!empty($product)):?>
                                    <tr>
                                        <td>
                                            <a href="<?= generateDashUrl('sale') . '/' . $request->order_number; ?>" target="_blank">
                                                #<?= $request->order_number; ?>&nbsp;-&nbsp;<?= esc($product->product_title); ?>
                                            </a>
                                        </td>
                                        <td><?= priceFormatted($product->product_total_price, $product->product_currency); ?></td>
                                        <td>
                                            <?php $buyer = getUser($product->buyer_id);
                                            if (!empty($buyer)): ?>
                                                <a href="<?= generateProfileUrl($buyer->slug); ?>" target="_blank" class="font-600"><?= esc(getUsername($buyer)); ?></a>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($request->status == 1): ?>
                                                <label class="label label-success"><?= trans("approved"); ?></label>
                                            <?php elseif ($request->status == 2): ?>
                                                <label class="label label-danger"><?= trans("declined"); ?></label>
                                            <?php else: ?>
                                                <label class="label label-default"><?= trans("order_processing"); ?></label>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= timeAgo($request->updated_at); ?></td>
                                        <td><?= formatDate($request->created_at); ?></td>
                                        <td>
                                            <a href="<?= generateDashUrl('refund_requests'); ?>/<?= $request->id; ?>" class="btn btn-sm btn-default btn-details"><i class="fa fa-info-circle" aria-hidden="true"></i><?= trans("details"); ?></a>
                                        </td>
                                    </tr>
                                <?php endif;
                            endforeach;
                        endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if (empty($refundRequests)): ?>
                    <p class="text-center">
                        <?= trans("no_records_found"); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?php if (!empty($refundRequests)): ?>
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
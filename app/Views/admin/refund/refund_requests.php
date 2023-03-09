<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?= $title; ?></h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" role="grid">
                        <thead>
                        <tr role="row">
                            <th scope="col"><?= trans("product"); ?></th>
                            <th scope="col"><?= trans("total"); ?></th>
                            <th scope="col"><?= trans("commission_rate"); ?></th>
                            <th scope="col"><?= trans("earned_amount"); ?></th>
                            <th scope="col"><?= trans("buyer"); ?></th>
                            <th scope="col"><?= trans("seller"); ?></th>
                            <th scope="col"><?= trans("status"); ?></th>
                            <th scope="col"><?= trans("updated"); ?></th>
                            <th scope="col"><?= trans("date"); ?></th>
                            <th scope="col"><?= trans("options"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($refundRequests)): ?>
                            <?php foreach ($refundRequests as $request):
                                $product = getOrderProduct($request->order_product_id);
                                if (!empty($product)):?>
                                    <tr>
                                        <td>
                                            <a href="<?= adminUrl('order-details/' . $request->order_id); ?>" target="_blank">
                                                #<?= esc($request->order_number); ?>&nbsp;-&nbsp;<?= esc($product->product_title); ?>
                                            </a>
                                        </td>
                                        <td><?= priceFormatted($product->product_total_price, $product->product_currency); ?></td>
                                        <td><?= esc($product->commission_rate); ?>%</td>
                                        <td>
                                            <?php $earning = getEarningByOrderProductId($request->order_product_id, $request->order_number);
                                            $order = getOrderByOrderNumber($request->order_number);
                                            if (!empty($earning) && !empty($order) && $order->payment_method != 'Cash On Delivery') {
                                                echo priceFormatted($earning->earned_amount, $earning->currency);
                                            } else {
                                                echo trans("not_added_vendor_balance");
                                            } ?>
                                        </td>
                                        <td>
                                            <?php $buyer = getUser($product->buyer_id);
                                            if (!empty($buyer)): ?>
                                                <a href="<?= generateProfileUrl($buyer->slug); ?>" target="_blank" class="font-600"><?= esc(getUsername($buyer)); ?></a>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php $seller = getUser($product->seller_id);
                                            if (!empty($seller)): ?>
                                                <a href="<?= generateProfileUrl($seller->slug); ?>" target="_blank" class="font-600"><?= esc(getUsername($seller)); ?></a>
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
                                            <form action="<?= base_url('OrderAdminController/approveRefundPost'); ?>" method="post">
                                                <?= csrf_field(); ?>
                                                <input type="hidden" name="order_product_id" value="<?= $request->order_product_id; ?>">
                                                <?php if ($request->is_completed == 1): ?>
                                                    <a href="<?= adminUrl('refund-requests/' . $request->id); ?>" class="btn btn-sm btn-default btn-edit"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<?= trans("details"); ?></a>
                                                    <label class="label label-success"><i class="fa fa-check" aria-hidden="true"></i>&nbsp;<?= trans("refund_approved"); ?></label>
                                                <?php else: ?>
                                                    <div class="btn-group btn-group-option">
                                                        <a href="<?= adminUrl('refund-requests/' . $request->id); ?>" class="btn btn-sm btn-default btn-edit"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<?= trans("details"); ?></a>
                                                        <button type="submit" class="btn btn-sm btn-default btn-edit"><i class="fa fa-check" aria-hidden="true"></i>&nbsp;<?= trans("approve_refund"); ?></button>
                                                    </div>
                                                <?php endif; ?>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endif;
                            endforeach;
                        endif; ?>
                        </tbody>
                    </table>
                    <?php if (empty($refundRequests)): ?>
                        <p class="text-center">
                            <?= trans("no_records_found"); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
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

<div class="row">
    <div class="col-sm-9">
        <div class="alert alert-info alert-large m-t-10">
            <strong><?= trans("warning"); ?>!</strong>&nbsp;&nbsp;<?= trans("refund_admin_complete_exp"); ?>
        </div>
    </div>
</div>
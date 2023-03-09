<div id="wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="nav-breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= langBaseUrl(); ?>"><?= trans("home"); ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= trans("refund"); ?></li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 m-t-15 m-b-30">
                <div class="row justify-content-center">
                    <div class="col-12 col-sm-6">
                        <h1 class="page-title m-b-5"><?= trans("refund_requests"); ?></h1>
                    </div>
                    <div class="col-12 col-sm-6">
                        <button type="button" class="btn btn-info color-white float-right m-b-5" data-toggle="modal" data-target="#modalRefundRequest">
                            <svg width="18" height="18" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg" fill="#fff" class="mds-svg-icon">
                                <path d="M1600 736v192q0 40-28 68t-68 28h-416v416q0 40-28 68t-68 28h-192q-40 0-68-28t-28-68v-416h-416q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h416v-416q0-40 28-68t68-28h192q40 0 68 28t28 68v416h416q40 0 68 28t28 68z"/>
                            </svg>
                            <?= trans("submit_refund_request"); ?>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="row-custom">
                    <div class="profile-tab-content">
                        <?= view('partials/_messages'); ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th scope="col"><?= trans("product"); ?></th>
                                    <th scope="col"><?= trans("total"); ?></th>
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
                                                    <a href="<?= generateUrl("order_details") . '/' . esc($request->order_number); ?>" target="_blank" class="a-hover-underline">
                                                        #<?= esc($request->order_number); ?>&nbsp;-&nbsp;<?= esc($product->product_title); ?>
                                                    </a>
                                                </td>
                                                <td><?= priceFormatted($product->product_total_price, $product->product_currency); ?></td>
                                                <td>
                                                    <?php $seller = getUser($product->seller_id);
                                                    if (!empty($seller)): ?>
                                                        <a href="<?= generateProfileUrl($seller->slug); ?>" target="_blank" class="font-600"><?= esc(getUsername($seller)); ?></a>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($request->status == 1): ?>
                                                        <label class="badge badge-lg badge-success"><?= trans("approved"); ?></label>
                                                    <?php elseif ($request->status == 2): ?>
                                                        <label class="badge badge-lg badge-danger"><?= trans("declined"); ?></label>
                                                    <?php else: ?>
                                                        <label class="badge badge-lg badge-secondary"><?= trans("order_processing"); ?></label>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= timeAgo($request->updated_at); ?></td>
                                                <td><?= formatDate($request->created_at); ?></td>
                                                <td>
                                                    <a href="<?= generateUrl("refund_requests") . '/' . $request->id; ?>" class="btn btn-sm btn-table-info"><?= trans("details"); ?></a>
                                                </td>
                                            </tr>
                                        <?php endif;
                                    endforeach;
                                endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if (empty($refundRequests)): ?>
                            <p class="text-center text-muted">
                                <?= trans("no_records_found"); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row-custom m-t-15">
                    <div class="float-right">
                        <?= view('partials/_pagination'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalRefundRequest" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content modal-custom modal-refund">
            <form action="<?= base_url('submit-refund-request'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title"><?= trans("submit_refund_request"); ?></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true"><i class="icon-close"></i> </span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label"><?= trans("product"); ?></label>
                        <select class="form-control custom-select" name="order_product_id" required>
                            <option value=""><?= trans("select"); ?></option>
                            <?php if (!empty($userOrders)):
                                foreach ($userOrders as $order):
                                    $hideProducts = false;
                                    if ($order->payment_method == 'Bank Transfer' && $order->payment_status == 'awaiting_payment') {
                                        $hideProducts = true;
                                    }
                                    if ($order->status != 2 && $hideProducts == false):
                                        $products = getOrderProducts($order->id);
                                        if (!empty($products)):?>
                                            <option disabled><?= formatDate($order->created_at); ?></option>
                                            <?php foreach ($products as $product):
                                                if (!in_array($product->id, $activeRefundRequestIds)):?>
                                                    <option value="<?= $product->id; ?>">#<?= esc($order->order_number); ?>&nbsp;-&nbsp;<?= esc($product->product_title); ?></option>
                                                <?php endif;
                                            endforeach;
                                        endif;
                                    endif;
                                endforeach;
                            endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans("refund_reason_explain"); ?></label>
                        <textarea name="message" class="form-control" aria-hidden="true" required><?= old('message'); ?></textarea>
                    </div>
                    <div class="form-group text-right m-0">
                        <button type="submit" class="btn btn-md btn-custom"><?= trans("submit"); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
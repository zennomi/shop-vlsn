<div class="row">
    <div class="col-sm-12">
        <?= view('dashboard/includes/_messages'); ?>
    </div>
</div>
<div class="box">
    <div class="box-header with-border">
        <div class="left">
            <h3 class="box-title"><?= trans("sale"); ?>:&nbsp;#<?= esc($order->order_number); ?></h3>
        </div>
        <div class="right">
            <a href="<?= langBaseUrl('invoice/' . esc($order->order_number) . '?type=seller'); ?>" target="_blank" class="btn btn-sm btn-info btn-sale-options btn-view-invoice"><i class="fa fa-file-text-o"></i>&nbsp;&nbsp;<?= trans('view_invoice'); ?></a>
        </div>
    </div>
    <div class="box-body">
        <div class="row m-b-30">
            <div class="col-lg-6 col-md-12">
                <div class="line-detail">
                    <span><?= trans("status"); ?></span>
                    <?php $orderStatus = 1;
                    foreach ($orderProducts as $item):
                        if ($item->order_status != 'completed' && $item->order_status != 'refund_approved') {
                            $orderStatus = 0;
                        }
                    endforeach;
                    if ($order->status == 2): ?>
                        <label class="label label-danger"><?= trans("cancelled"); ?></label>
                    <?php else:
                        if ($orderStatus == 1): ?>
                            <label class="label label-default"><?= trans("completed"); ?></label>
                        <?php else: ?>
                            <label class="label label-success"><?= trans("order_processing"); ?></label>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <?php if ($order->status != 2): ?>
                    <div class="line-detail">
                        <span><?= trans("payment_status"); ?></span>
                        <strong class="font-600"><?= trans($order->payment_status); ?></strong>
                    </div>
                    <div class="line-detail">
                        <span><?= trans("payment_method"); ?></span>
                        <?= getPaymentMethod($order->payment_method); ?>
                    </div>
                <?php endif; ?>
                <div class="line-detail">
                    <span><?= trans("date"); ?></span>
                    <?= formatDate($order->created_at); ?>
                </div>
                <div class="line-detail">
                    <span><?= trans("updated"); ?></span>
                    <?= timeAgo($order->updated_at); ?>
                </div>
            </div>
            <div class="col-lg-6 col-md-12">
                <?php if (!empty($order->buyer_id)):
                    $buyer = getUser($order->buyer_id);
                    if (!empty($buyer)):?>
                        <div class="tbl-table" style="max-width: 400px;">
                            <div class="left" style="width: 135px !important;">
                                <a href="<?= generateProfileUrl($buyer->slug); ?>" target="_blank">
                                    <img src="<?= getUserAvatar($buyer); ?>" alt="" class="img-responsive" style="width: 120px !important; max-width: 120px !important; height: 120px;">
                                </a>
                            </div>
                            <div class="right">
                                <p><strong><a href="<?= generateProfileUrl($buyer->slug); ?>" target="_blank"><?= esc(getUsername($buyer)); ?></a></strong></p>
                                <?php if ($generalSettings->show_customer_phone_seller == 1): ?>
                                    <p><strong><?= esc($buyer->phone_number); ?></strong></p>
                                <?php endif;
                                if ($generalSettings->show_customer_email_seller == 1): ?>
                                    <p><strong><?= esc($buyer->email); ?></strong></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif;
                else: ?>
                    <div class="tbl-table" style="max-width: 400px;">
                        <div class="left" style="width: 135px !important;">
                            <img src="<?= getUserAvatar(null); ?>" alt="" class="img-responsive" style="width: 120px !important; max-width: 120px !important; height: 120px;">
                        </div>
                        <div class="right">
                            <p><strong><?= trans("guest"); ?></strong></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php $shipping = unserializeData($order->shipping);
        if (!empty($shipping)):?>
            <div class="row m-b-30">
                <div class="col-sm-12 col-md-6">
                    <h3 class="block-title"><?= trans("shipping_address"); ?></h3>
                    <div class="line-detail line-detail-sm">
                        <span><?= trans("first_name"); ?></span>
                        <?= !empty($shipping->sFirstName) ? esc($shipping->sFirstName) : ''; ?>
                    </div>
                    <div class="line-detail line-detail-sm">
                        <span><?= trans("last_name"); ?></span>
                        <?= !empty($shipping->sLastName) ? esc($shipping->sLastName) : ''; ?>
                    </div>
                    <?php if ($generalSettings->show_customer_email_seller == 1): ?>
                        <div class="line-detail line-detail-sm">
                            <span><?= trans("email"); ?></span>
                            <?= !empty($shipping->sEmail) ? esc($shipping->sEmail) : ''; ?>
                        </div>
                    <?php endif;
                    if ($generalSettings->show_customer_phone_seller == 1): ?>
                        <div class="line-detail line-detail-sm">
                            <span><?= trans("phone_number"); ?></span>
                            <?= !empty($shipping->sPhoneNumber) ? esc($shipping->sPhoneNumber) : ''; ?>
                        </div>
                    <?php endif; ?>
                    <div class="line-detail line-detail-sm">
                        <span><?= trans("address"); ?></span>
                        <?= !empty($shipping->sAddress) ? esc($shipping->sAddress) : ''; ?>
                    </div>
                    <div class="line-detail line-detail-sm">
                        <span><?= trans("country"); ?></span>
                        <?= !empty($shipping->sCountry) ? esc($shipping->sCountry) : ''; ?>
                    </div>
                    <div class="line-detail line-detail-sm">
                        <span><?= trans("state"); ?></span>
                        <?= !empty($shipping->sState) ? esc($shipping->sState) : ''; ?>
                    </div>
                    <div class="line-detail line-detail-sm">
                        <span><?= trans("city"); ?></span>
                        <?= !empty($shipping->sCity) ? esc($shipping->sCity) : ''; ?>
                    </div>
                    <div class="line-detail line-detail-sm">
                        <span><?= trans("zip_code"); ?></span>
                        <?= !empty($shipping->sZipCode) ? esc($shipping->sZipCode) : ''; ?>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <h3 class="block-title"><?= trans("billing_address"); ?></h3>
                    <div class="line-detail line-detail-sm">
                        <span><?= trans("first_name"); ?></span>
                        <?= !empty($shipping->bFirstName) ? esc($shipping->bFirstName) : ''; ?>
                    </div>
                    <div class="line-detail line-detail-sm">
                        <span><?= trans("last_name"); ?></span>
                        <?= !empty($shipping->bLastName) ? esc($shipping->bLastName) : ''; ?>
                    </div>
                    <?php if ($generalSettings->show_customer_email_seller == 1): ?>
                        <div class="line-detail line-detail-sm">
                            <span><?= trans("email"); ?></span>
                            <?= !empty($shipping->bEmail) ? esc($shipping->bEmail) : ''; ?>
                        </div>
                    <?php endif;
                    if ($generalSettings->show_customer_phone_seller == 1): ?>
                        <div class="line-detail line-detail-sm">
                            <span><?= trans("phone_number"); ?></span>
                            <?= !empty($shipping->bPhoneNumber) ? esc($shipping->bPhoneNumber) : ''; ?>
                        </div>
                    <?php endif; ?>
                    <div class="line-detail line-detail-sm">
                        <span><?= trans("address"); ?></span>
                        <?= !empty($shipping->bAddress) ? esc($shipping->bAddress) : ''; ?>
                    </div>
                    <div class="line-detail line-detail-sm">
                        <span><?= trans("country"); ?></span>
                        <?= !empty($shipping->bCountry) ? esc($shipping->bCountry) : ''; ?>
                    </div>
                    <div class="line-detail line-detail-sm">
                        <span><?= trans("state"); ?></span>
                        <?= !empty($shipping->bState) ? esc($shipping->bState) : ''; ?>
                    </div>
                    <div class="line-detail line-detail-sm">
                        <span><?= trans("city"); ?></span>
                        <?= !empty($shipping->bCity) ? esc($shipping->bCity) : ''; ?>
                    </div>
                    <div class="line-detail line-detail-sm">
                        <span><?= trans("zip_code"); ?></span>
                        <?= !empty($shipping->bZipCode) ? esc($shipping->bZipCode) : ''; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-sm-12">
                <h3 class="block-title"><?= trans("products"); ?></h3>
                <div class="table-responsive">
                    <table class="table table-orders">
                        <thead>
                        <tr>
                            <th scope="col"><?= trans("product"); ?></th>
                            <th scope="col"><?= trans("status"); ?></th>
                            <th scope="col"><?= trans("updated"); ?></th>
                            <th scope="col"><?= trans("options"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $saleSubtotal = 0;
                        $saleVat = 0;
                        $saleShipping = 0;
                        $saleTotal = 0;
                        if (!empty($orderProducts)):
                            foreach ($orderProducts as $item):
                                if ($item->seller_id == user()->id):
                                    $saleSubtotal += $item->product_unit_price * $item->product_quantity;
                                    $saleVat += $item->product_vat;
                                    $saleShipping = $item->seller_shipping_cost;
                                    $saleTotal += $item->product_total_price; ?>
                                    <tr>
                                        <td style="width: 50%">
                                            <div class="table-item-product">
                                                <div class="left">
                                                    <div class="img-table">
                                                        <a href="<?= generateProductUrlBySlug($item->product_slug); ?>" target="_blank">
                                                            <img src="<?= getProductMainImage($item->product_id, 'image_small'); ?>" data-src="" alt="" class="lazyload img-responsive post-image"/>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="right">
                                                    <a href="<?= generateProductUrlBySlug($item->product_slug); ?>" target="_blank" class="table-product-title"><?= esc($item->product_title); ?></a>
                                                    <p class="m-b-15">
                                                        <span><?= trans("seller"); ?>:</span>
                                                        <?php $seller = getUser($item->seller_id); ?>
                                                        <?php if (!empty($seller)): ?>
                                                            <a href="<?= generateProfileUrl($seller->slug); ?>" target="_blank" class="table-product-title">
                                                                <strong class="font-600"><?= esc(getUsername($seller)); ?></strong>
                                                            </a>
                                                        <?php endif; ?>
                                                    </p>
                                                    <p><span class="span-product-dtl-table"><?= trans("unit_price"); ?>:</span><?= priceFormatted($item->product_unit_price, $item->product_currency); ?></p>
                                                    <p><span class="span-product-dtl-table"><?= trans("quantity"); ?>:</span><?= $item->product_quantity; ?></p>
                                                    <?php if (!empty($item->product_vat)): ?>
                                                        <p><span class="span-product-dtl-table"><?= trans("vat"); ?>&nbsp;(<?= $item->product_vat_rate; ?>%):</span><?= priceFormatted($item->product_vat, $item->product_currency); ?></p>
                                                        <p><span class="span-product-dtl-table"><?= trans("total"); ?>:</span><?= priceFormatted($item->product_total_price, $item->product_currency); ?></p>
                                                    <?php else: ?>
                                                        <p><span class="span-product-dtl-table"><?= trans("total"); ?>:</span><?= priceFormatted($item->product_total_price, $item->product_currency); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="width: 10%; white-space: nowrap">
                                            <strong><?= trans($item->order_status) ?></strong>
                                        </td>
                                        <td style="width: 15%">
                                            <?php if ($item->product_type == 'physical') {
                                                echo timeAgo($item->updated_at);
                                            } ?>
                                        </td>
                                        <td style="width: 25%">
                                            <?php if ($order->status != 2 && $item->order_status != 'refund_approved'):
                                                if ($item->product_type != 'digital'):
                                                    if ($item->order_status == "completed"): ?>
                                                        <strong class="font-600"><i class="icon-check"></i>&nbsp;<?= trans("approved"); ?></strong>
                                                    <?php else:
                                                        if ($order->payment_method == 'Cash On Delivery' || $order->payment_status == 'payment_received'):?>
                                                            <p class="m-b-5">
                                                                <button type="button" class="btn btn-md btn-block btn-success" data-toggle="modal" data-target="#updateStatusModal_<?= $item->id; ?>"><?= trans('update_order_status'); ?></button>
                                                            </p>
                                                        <?php endif;
                                                    endif;
                                                endif;
                                            endif; ?>
                                        </td>
                                    </tr>
                                    <?php if ($item->product_type != "digital"): ?>
                                    <tr class="tr-shipping">
                                        <td colspan="4">
                                            <div class="order-shipping-tracking-number">
                                                <p><strong><?= trans("shipping") ?></strong></p>
                                                <p class="font-600 m-t-5"><?= trans("shipping_method") ?>:&nbsp;<?= esc($item->shipping_method); ?></p>
                                                <?php if ($item->order_status == 'shipped' || $item->order_status == 'completed'): ?>
                                                    <p class="font-600 m-t-15"><?= trans("order_has_been_shipped"); ?></p>
                                                    <p><?= trans("tracking_code") ?>:&nbsp;<?= esc($item->shipping_tracking_number); ?></p>
                                                    <p class="m-0"><?= trans("tracking_url") ?>: <a href="<?= esc($item->shipping_tracking_url); ?>" target="_blank" class="link-underlined"><?= esc($item->shipping_tracking_url); ?></a></p>
                                                <?php else: ?>
                                                    <p><?= trans("order_not_yet_shipped") . trans("warning_add_order_tracking_code"); ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="tr-shipping-seperator">
                                        <td colspan="4"></td>
                                    </tr>
                                <?php endif;
                                endif;
                            endforeach;
                        endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="order-total">
                    <div class="row">
                        <div class="col-sm-6 col-xs-6 col-left">
                            <?= trans("subtotal"); ?>
                        </div>
                        <div class="col-sm-6 col-xs-6 col-right">
                            <strong><?= priceFormatted($saleSubtotal, $order->price_currency); ?></strong>
                        </div>
                    </div>
                    <?php if (!empty($saleVat)): ?>
                        <div class="row">
                            <div class="col-sm-6 col-xs-6 col-left">
                                <?= trans("vat"); ?>
                            </div>
                            <div class="col-sm-6 col-xs-6 col-right">
                                <strong><?= priceFormatted($saleVat, $order->price_currency); ?></strong>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-sm-6 col-xs-6 col-left">
                            <?= trans("shipping"); ?>
                        </div>
                        <div class="col-sm-6 col-xs-6 col-right">
                            <strong><?= priceFormatted($saleShipping, $order->price_currency); ?></strong>
                        </div>
                    </div>
                    <?php $coupon_discount = 0;
                    if (user()->id == $order->coupon_seller_id && !empty($order->coupon_discount)):
                        $saleTotal = $saleTotal - $order->coupon_discount; ?>
                        <div class="row">
                            <div class="col-sm-6 col-xs-6 col-left">
                                <?= trans("coupon"); ?>&nbsp;&nbsp;[<?= esc($order->coupon_code); ?>]
                            </div>
                            <div class="col-sm-6 col-xs-6 col-right">
                                <strong class="font-600">-&nbsp;<?= priceFormatted($order->coupon_discount, $order->price_currency); ?></strong>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-12 m-b-15">
                            <div class="row-seperator"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 col-xs-6 col-left">
                            <?= trans("total"); ?>
                        </div>
                        <div class="col-sm-6 col-xs-6 col-right">
                            <strong><?= priceFormatted($saleTotal + $saleShipping, $order->price_currency); ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php if (!empty($orderProducts)):
    foreach ($orderProducts as $item):
        if ($item->seller_id == user()->id):?>
            <div class="modal fade" id="updateStatusModal_<?= $item->id; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content modal-custom">
                        <form action="<?= base_url('update-order-product-status-post'); ?>" method="post">
                            <?= csrf_field(); ?>
                            <input type="hidden" name="id" value="<?= $item->id; ?>">
                            <div class="modal-header">
                                <h5 class="modal-title"><?= trans("update_order_status"); ?></h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span aria-hidden="true"><i class="icon-close"></i> </span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="control-label"><?= trans('status'); ?></label>
                                            <select id="select_order_status" name="order_status" class="form-control custom-select" data-order-product-id="<?= $item->id; ?>">
                                                <?php if ($item->product_type == 'physical'): ?>
                                                    <option value="order_processing" <?= $item->order_status == 'order_processing' ? 'selected' : ''; ?>><?= trans("order_processing"); ?></option>
                                                    <option value="shipped" <?= $item->order_status == 'shipped' ? 'selected' : ''; ?>><?= trans("shipped"); ?></option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="row tracking-number-container <?= $item->order_status != 'shipped' ? 'display-none' : ''; ?>">
                                            <hr>
                                            <div class="col-12 text-center">
                                                <strong><?= trans("shipping"); ?></strong>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label><?= trans("tracking_code"); ?></label>
                                                    <input type="text" name="shipping_tracking_number" class="form-control form-input" value="<?= esc($item->shipping_tracking_number); ?>" placeholder="<?= trans("tracking_code"); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label><?= trans("tracking_url"); ?></label>
                                                    <input type="text" name="shipping_tracking_url" class="form-control form-input" value="<?= esc($item->shipping_tracking_url); ?>" placeholder="<?= trans("tracking_url"); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-md btn-default" data-dismiss="modal"><?= trans("close"); ?></button>
                                <button type="submit" class="btn btn-md btn-success"><?= trans("submit"); ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif;
    endforeach;
endif; ?>

<script>
    $(document).on("change", "#select_order_status", function () {
        var val = $(this).val();
        if (val == "shipped") {
            $(".tracking-number-container").show();
        } else {
            $(".tracking-number-container").hide();
        }
    });
</script>
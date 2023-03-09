<div id="wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="nav-breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= langBaseUrl(); ?>"><?= trans("home"); ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= $title; ?></li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-12">
                        <?= view('partials/_messages'); ?>
                    </div>
                </div>
                <div class="order-details-container">
                    <div class="order-head">
                        <div class="row justify-content-center row-title">
                            <div class="col-12 col-sm-6">
                                <h1 class="page-title m-b-5"><?= trans("order"); ?>:&nbsp;#<?= esc($order->order_number); ?></h1>
                            </div>
                            <div class="col-12 col-sm-6">
                                <a href="<?= generateUrl('orders'); ?>" class="btn btn-custom color-white float-right m-b-5">
                                    <svg width="18" height="18" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg" fill="#fff" class="mds-svg-icon">
                                        <path d="M384 1408q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm0-512q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm1408 416v192q0 13-9.5 22.5t-22.5 9.5h-1216q-13 0-22.5-9.5t-9.5-22.5v-192q0-13 9.5-22.5t22.5-9.5h1216q13 0 22.5 9.5t9.5 22.5zm-1408-928q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm1408 416v192q0 13-9.5 22.5t-22.5 9.5h-1216q-13 0-22.5-9.5t-9.5-22.5v-192q0-13 9.5-22.5t22.5-9.5h1216q13 0 22.5 9.5t9.5 22.5zm0-512v192q0 13-9.5 22.5t-22.5 9.5h-1216q-13 0-22.5-9.5t-9.5-22.5v-192q0-13 9.5-22.5t22.5-9.5h1216q13 0 22.5 9.5t9.5 22.5z"/>
                                    </svg>
                                    <?= trans("orders"); ?>
                                </a>
                                <?php if ($order->status != 2):
                                    if ($order->payment_status == 'payment_received'): ?>
                                        <a href="<?= langBaseUrl(); ?>/invoice/<?= esc($order->order_number); ?>?type=buyer" target="_blank" class="btn btn-info color-white float-right m-b-5 m-r-5"><i class="icon-text-o"></i>&nbsp;<?= trans('view_invoice'); ?></a>
                                    <?php else: ?>
                                        <?php if ($order->payment_method != "Cash On Delivery" || ($order->payment_method == 'Cash On Delivery' && dateDifferenceInHours(date('Y-m-d H:i:s'), $order->created_at) <= 24)): ?>
                                            <button type="button" class="btn btn-gray float-right m-b-5 m-r-5" onclick='cancelOrder(<?= $order->id; ?>,"<?= trans("confirm_action", true); ?>");'><i class="icon-times"></i>&nbsp;<?= trans("cancel_order"); ?></button>
                                        <?php endif;
                                    endif;
                                endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="order-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="row order-row-item">
                                    <div class="col-3">
                                        <b class="font-600"><?= trans("status"); ?></b>
                                    </div>
                                    <div class="col-9">
                                        <?php if ($order->status == 1): ?>
                                            <strong><?= trans("completed"); ?></strong>
                                        <?php elseif ($order->status == 2): ?>
                                            <strong><?= trans("cancelled"); ?></strong>
                                        <?php else: ?>
                                            <strong><?= trans("order_processing"); ?></strong>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if ($order->status != 2): ?>
                                    <div class="row order-row-item">
                                        <div class="col-3">
                                            <b class="font-600"><?= trans("payment_status"); ?></b>
                                        </div>
                                        <div class="col-9">
                                            <?= trans($order->payment_status); ?>
                                            <?php if ($order->payment_method == 'Bank Transfer' && $order->payment_status == 'awaiting_payment'):
                                                if (isset($lastBankTransfer)):
                                                    if ($lastBankTransfer->status == 'pending'): ?>
                                                        <span class="text-info">(<?= trans("pending"); ?>)</span>
                                                    <?php elseif ($lastBankTransfer->status == 'declined'): ?>
                                                        <span class="text-danger">(<?= trans("bank_transfer_declined"); ?>)</span>
                                                        <button type="button" class="btn btn-sm btn-secondary color-white m-l-15" data-toggle="modal" data-target="#reportPaymentModal"><?= trans("report_bank_transfer"); ?></button>
                                                    <?php endif;
                                                else: ?>
                                                    <button type="button" class="btn btn-sm btn-secondary color-white m-l-15" data-toggle="modal" data-target="#reportPaymentModal">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16" class="mds-svg-icon">
                                                            <path d="M1.92.506a.5.5 0 0 1 .434.14L3 1.293l.646-.647a.5.5 0 0 1 .708 0L5 1.293l.646-.647a.5.5 0 0 1 .708 0L7 1.293l.646-.647a.5.5 0 0 1 .708 0L9 1.293l.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .801.13l.5 1A.5.5 0 0 1 15 2v12a.5.5 0 0 1-.053.224l-.5 1a.5.5 0 0 1-.8.13L13 14.707l-.646.647a.5.5 0 0 1-.708 0L11 14.707l-.646.647a.5.5 0 0 1-.708 0L9 14.707l-.646.647a.5.5 0 0 1-.708 0L7 14.707l-.646.647a.5.5 0 0 1-.708 0L5 14.707l-.646.647a.5.5 0 0 1-.708 0L3 14.707l-.646.647a.5.5 0 0 1-.801-.13l-.5-1A.5.5 0 0 1 1 14V2a.5.5 0 0 1 .053-.224l.5-1a.5.5 0 0 1 .367-.27zm.217 1.338L2 2.118v11.764l.137.274.51-.51a.5.5 0 0 1 .707 0l.646.647.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.509.509.137-.274V2.118l-.137-.274-.51.51a.5.5 0 0 1-.707 0L12 1.707l-.646.647a.5.5 0 0 1-.708 0L10 1.707l-.646.647a.5.5 0 0 1-.708 0L8 1.707l-.646.647a.5.5 0 0 1-.708 0L6 1.707l-.646.647a.5.5 0 0 1-.708 0L4 1.707l-.646.647a.5.5 0 0 1-.708 0l-.509-.51z"/>
                                                            <path d="M3 4.5a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5zm8-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5z"/>
                                                        </svg>
                                                        <?= trans("report_bank_transfer"); ?>
                                                    </button>
                                                <?php endif; ?>
                                                <button type="button" class="btn btn-sm btn-success color-white" data-toggle="modal" data-target="#bankAccountsModal">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16" class="mds-svg-icon">
                                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                                        <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                                    </svg>
                                                    <?= trans("bank_accounts"); ?>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="row order-row-item">
                                        <div class="col-3">
                                            <b class="font-600"><?= trans("payment_method"); ?></b>
                                        </div>
                                        <div class="col-9">
                                            <?= getPaymentMethod($order->payment_method); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <div class="row order-row-item">
                                    <div class="col-3">
                                        <b class="font-600"><?= trans("date"); ?></b>
                                    </div>
                                    <div class="col-9">
                                        <?= formatDate($order->created_at); ?>
                                    </div>
                                </div>
                                <div class="row order-row-item">
                                    <div class="col-3">
                                        <b class="font-600"><?= trans("updated"); ?></b>
                                    </div>
                                    <div class="col-9">
                                        <?= timeAgo($order->updated_at); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php $shipping = unserializeData($order->shipping);
                        if (!empty($shipping)):?>
                            <div class="row shipping-container">
                                <div class="col-md-12 col-lg-6 m-b-sm-15">
                                    <div class="order-address-box">
                                        <h3 class="block-title"><?= trans("shipping_address"); ?></h3>
                                        <div class="row shipping-row-item">
                                            <div class="col-5"><?= trans("first_name"); ?></div>
                                            <div class="col-7"><?= !empty($shipping->sFirstName) ? esc($shipping->sFirstName) : ''; ?></div>
                                        </div>
                                        <div class="row shipping-row-item">
                                            <div class="col-5"><?= trans("last_name"); ?></div>
                                            <div class="col-7"><?= !empty($shipping->sLastName) ? esc($shipping->sLastName) : ''; ?></div>
                                        </div>
                                        <div class="row shipping-row-item">
                                            <div class="col-5"><?= trans("email"); ?></div>
                                            <div class="col-7"><?= !empty($shipping->sEmail) ? esc($shipping->sEmail) : ''; ?></div>
                                        </div>
                                        <div class="row shipping-row-item">
                                            <div class="col-5"><?= trans("phone_number"); ?></div>
                                            <div class="col-7"><?= !empty($shipping->sPhoneNumber) ? esc($shipping->sPhoneNumber) : ''; ?></div>
                                        </div>
                                        <div class="row shipping-row-item">
                                            <div class="col-5"><?= trans("address"); ?></div>
                                            <div class="col-7"><?= !empty($shipping->sAddress) ? esc($shipping->sAddress) : ''; ?></div>
                                        </div>
                                        <div class="row shipping-row-item">
                                            <div class="col-5"><?= trans("country"); ?></div>
                                            <div class="col-7"><?= !empty($shipping->sCountry) ? esc($shipping->sCountry) : ''; ?></div>
                                        </div>
                                        <div class="row shipping-row-item">
                                            <div class="col-5"><?= trans("state"); ?></div>
                                            <div class="col-7"><?= !empty($shipping->sState) ? esc($shipping->sState) : ''; ?></div>
                                        </div>
                                        <div class="row shipping-row-item">
                                            <div class="col-5"><?= trans("city"); ?></div>
                                            <div class="col-7"><?= !empty($shipping->sCity) ? esc($shipping->sCity) : ''; ?></div>
                                        </div>
                                        <div class="row shipping-row-item mb-0">
                                            <div class="col-5"><?= trans("zip_code"); ?></div>
                                            <div class="col-7"><?= !empty($shipping->sZipCode) ? esc($shipping->sZipCode) : ''; ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-6">
                                    <div class="order-address-box">
                                        <h3 class="block-title"><?= trans("billing_address"); ?></h3>
                                        <div class="row shipping-row-item">
                                            <div class="col-5"><?= trans("first_name"); ?></div>
                                            <div class="col-7"><?= !empty($shipping->bFirstName) ? esc($shipping->bFirstName) : ''; ?></div>
                                        </div>
                                        <div class="row shipping-row-item">
                                            <div class="col-5"><?= trans("last_name"); ?></div>
                                            <div class="col-7"><?= !empty($shipping->bLastName) ? esc($shipping->bLastName) : ''; ?></div>
                                        </div>
                                        <div class="row shipping-row-item">
                                            <div class="col-5"><?= trans("email"); ?></div>
                                            <div class="col-7"><?= !empty($shipping->bEmail) ? esc($shipping->bEmail) : ''; ?></div>
                                        </div>
                                        <div class="row shipping-row-item">
                                            <div class="col-5"><?= trans("phone_number"); ?></div>
                                            <div class="col-7"><?= !empty($shipping->bPhoneNumber) ? esc($shipping->bPhoneNumber) : ''; ?></div>
                                        </div>
                                        <div class="row shipping-row-item">
                                            <div class="col-5"><?= trans("address"); ?></div>
                                            <div class="col-7"><?= !empty($shipping->bAddress) ? esc($shipping->bAddress) : ''; ?></div>
                                        </div>
                                        <div class="row shipping-row-item">
                                            <div class="col-5"><?= trans("country"); ?></div>
                                            <div class="col-7"><?= !empty($shipping->bCountry) ? esc($shipping->bCountry) : ''; ?></div>
                                        </div>
                                        <div class="row shipping-row-item">
                                            <div class="col-5"><?= trans("state"); ?></div>
                                            <div class="col-7"><?= !empty($shipping->bState) ? esc($shipping->bState) : ''; ?></div>
                                        </div>
                                        <div class="row shipping-row-item">
                                            <div class="col-5"><?= trans("city"); ?></div>
                                            <div class="col-7"><?= !empty($shipping->bCity) ? esc($shipping->bCity) : ''; ?></div>
                                        </div>
                                        <div class="row shipping-row-item mb-0">
                                            <div class="col-5"><?= trans("zip_code"); ?></div>
                                            <div class="col-7"><?= !empty($shipping->bZipCode) ? esc($shipping->bZipCode) : ''; ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif;
                        $isOrderHasPhysicalProduct = false; ?>
                        <div class="row table-orders-container">
                            <div class="col-6 col-table-orders">
                                <h3 class="block-title"><?= trans("products"); ?></h3>
                            </div>
                            <div class="col-12">
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
                                        <?php if (!empty($orderProducts)):
                                            foreach ($orderProducts as $item):
                                                if ($item->product_type == 'physical') {
                                                    $isOrderHasPhysicalProduct = true;
                                                } ?>
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
                                                                <div class="m-b-5">
                                                                    <a href="<?= generateProductUrlBySlug($item->product_slug); ?>" target="_blank" class="table-product-title font-600"><?= esc($item->product_title); ?></a>
                                                                </div>
                                                                <div class="m-b-5">
                                                                    <span><?= trans("seller"); ?>:</span>
                                                                    <?php $seller = getUser($item->seller_id); ?>
                                                                    <?php if (!empty($seller)): ?>
                                                                        <a href="<?= generateProfileUrl($seller->slug); ?>" target="_blank" class="table-product-title">
                                                                            <strong class="font-600"><?= esc(getUsername($seller)); ?></strong>
                                                                        </a>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div class="m-b-5"><span class="span-product-dtl-table"><?= trans("unit_price"); ?>:</span><?= priceFormatted($item->product_unit_price, $item->product_currency); ?></div>
                                                                <div class="m-b-5"><span class="span-product-dtl-table"><?= trans("quantity"); ?>:</span><?= $item->product_quantity; ?></div>
                                                                <?php if (!empty($item->product_vat)): ?>
                                                                    <div class="m-b-5"><span class="span-product-dtl-table"><?= trans("vat"); ?>&nbsp;(<?= $item->product_vat_rate; ?>%):</span><?= priceFormatted($item->product_vat, $item->product_currency); ?></div>
                                                                    <div class="m-b-5"><span class="span-product-dtl-table"><?= trans("total"); ?>:</span><?= priceFormatted($item->product_total_price, $item->product_currency); ?></div>
                                                                <?php else: ?>
                                                                    <div class="m-b-5"><span class="span-product-dtl-table"><?= trans("total"); ?>:</span><?= priceFormatted($item->product_total_price, $item->product_currency); ?></div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td style="width: 10%">
                                                        <strong class="no-wrap"><?= trans($item->order_status) ?></strong>
                                                    </td>
                                                    <td style="width: 15%;">
                                                        <?php if ($item->product_type == 'physical') {
                                                            echo timeAgo($item->updated_at);
                                                        } ?>
                                                    </td>
                                                    <td style="width: 25%;">
                                                        <?php if ($item->order_status == 'shipped'): ?>
                                                            <button type="submit" class="btn btn-sm btn-custom" onclick=" approveOrderProduct('<?= $item->id; ?>','<?= trans("confirm_approve_order", true); ?>');"><i class="icon-check"></i><?= trans("confirm_order_received"); ?></button>
                                                            <small class="text-confirm-order-table"><?= trans("confirm_order_received_exp"); ?></small>
                                                        <?php elseif ($item->order_status == 'completed'):
                                                            if ($item->product_type == 'digital'):
                                                                $digitalSale = getDigitalSaleByOrderId($item->buyer_id, $item->product_id, $item->order_id);
                                                                if (!empty($digitalSale)):
                                                                    if ($item->listing_type == 'license_key'):?>
                                                                        <div class="row-custom">
                                                                            <form action="<?= base_url('download-purchased-digital-file-post'); ?>" method="post">
                                                                                <?= csrf_field(); ?>
                                                                                <input type="hidden" name="sale_id" value="<?= $digitalSale->id; ?>">
                                                                                <button name="submit" value="license_certificate" class="btn btn-md btn-custom no-wrap"><i class="icon-download-solid"></i><?= trans("download_license_key"); ?></button>
                                                                            </form>
                                                                        </div>
                                                                    <?php else: ?>
                                                                        <div class="row-custom">
                                                                            <form action="<?= base_url('download-purchased-digital-file-post'); ?>" method="post">
                                                                                <?= csrf_field(); ?>
                                                                                <input type="hidden" name="sale_id" value="<?= $digitalSale->id; ?>">
                                                                                <div class="btn-group btn-group-download m-b-15">
                                                                                    <button type="button" class="btn btn-md btn-custom dropdown-toggle" data-toggle="dropdown">
                                                                                        <i class="icon-download-solid"></i><?= trans("download"); ?>&nbsp;&nbsp;<i class="icon-arrow-down m-0"></i>
                                                                                    </button>
                                                                                    <div class="dropdown-menu">
                                                                                        <button name="submit" value="main_files" class="dropdown-item"><?= trans("main_files"); ?></button>
                                                                                        <button name="submit" value="license_certificate" class="dropdown-item"><?= trans("license_certificate"); ?></button>
                                                                                    </div>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    <?php endif;
                                                                endif;
                                                            endif;
                                                            if ($generalSettings->reviews == 1 && $item->seller_id != $item->buyer_id): ?>
                                                                <div class="row-custom">
                                                                    <div class="rate-product">
                                                                        <p class="p-rate-product"><?= trans("rate_this_product"); ?></p>
                                                                        <div class="rating-stars">
                                                                            <?php $review = getReview($item->product_id, user()->id); ?>
                                                                            <label class="label-star label-star-open-modal" data-star="5" data-product-id="<?= $item->product_id; ?>" data-toggle="modal" data-target="#rateProductModal"><i class="<?= !empty($review) && $review->rating >= 5 ? 'icon-star' : 'icon-star-o'; ?>"></i></label>
                                                                            <label class="label-star label-star-open-modal" data-star="4" data-product-id="<?= $item->product_id; ?>" data-toggle="modal" data-target="#rateProductModal"><i class="<?= !empty($review) && $review->rating >= 4 ? 'icon-star' : 'icon-star-o'; ?>"></i></label>
                                                                            <label class="label-star label-star-open-modal" data-star="3" data-product-id="<?= $item->product_id; ?>" data-toggle="modal" data-target="#rateProductModal"><i class="<?= !empty($review) && $review->rating >= 3 ? 'icon-star' : 'icon-star-o'; ?>"></i></label>
                                                                            <label class="label-star label-star-open-modal" data-star="2" data-product-id="<?= $item->product_id; ?>" data-toggle="modal" data-target="#rateProductModal"><i class="<?= !empty($review) && $review->rating >= 2 ? 'icon-star' : 'icon-star-o'; ?>"></i></label>
                                                                            <label class="label-star label-star-open-modal" data-star="1" data-product-id="<?= $item->product_id; ?>" data-toggle="modal" data-target="#rateProductModal"><i class="<?= !empty($review) && $review->rating >= 1 ? 'icon-star' : 'icon-star-o'; ?>"></i></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php endif;
                                                        endif; ?>
                                                    </td>
                                                </tr>
                                                <?php if ($item->product_type == 'physical'): ?>
                                                <tr class="tr-shipping">
                                                    <td colspan="4">
                                                        <div class="order-shipping-tracking-number">
                                                            <p><strong><?= trans("shipping") ?></strong></p>
                                                            <p class="font-600 m-t-5"><?= trans("shipping_method") ?>:&nbsp;<?= esc($item->shipping_method); ?></p>
                                                            <?php if ($item->order_status == 'shipped'): ?>
                                                                <p class="font-600 m-t-15"><?= trans("order_has_been_shipped"); ?></p>
                                                                <p><?= trans("tracking_code") ?>:&nbsp;<?= esc($item->shipping_tracking_number); ?></p>
                                                                <p class="m-0"><?= trans("tracking_url") ?>: <a href="<?= esc($item->shipping_tracking_url); ?>" target="_blank" class="link-underlined"><?= esc($item->shipping_tracking_url); ?></a></p>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr class="tr-shipping-seperator">
                                                    <td colspan="4"></td>
                                                </tr>
                                            <?php endif;
                                            endforeach;
                                        endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="order-total">
                                    <div class="row">
                                        <div class="col-6 col-left">
                                            <?= trans("subtotal"); ?>
                                        </div>
                                        <div class="col-6 col-right">
                                            <strong class="font-600"><?= priceFormatted($order->price_subtotal, $order->price_currency); ?></strong>
                                        </div>
                                    </div>
                                    <?php if (!empty($order->price_vat)): ?>
                                        <div class="row">
                                            <div class="col-6 col-left">
                                                <?= trans("vat"); ?>
                                            </div>
                                            <div class="col-6 col-right">
                                                <strong class="font-600"><?= priceFormatted($order->price_vat, $order->price_currency); ?></strong>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($isOrderHasPhysicalProduct): ?>
                                        <div class="row">
                                            <div class="col-6 col-left">
                                                <?= trans("shipping"); ?>
                                            </div>
                                            <div class="col-6 col-right">
                                                <strong class="font-600"><?= priceFormatted($order->price_shipping, $order->price_currency); ?></strong>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($order->coupon_discount > 0): ?>
                                        <div class="row row-details">
                                            <div class="col-xs-12 col-sm-6 col-left">
                                                <strong><?= trans("coupon"); ?>&nbsp;&nbsp;[<?= esc($order->coupon_code); ?>]</strong>
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-right text-right">
                                                <strong class="font-right">-&nbsp;<?= priceFormatted($order->coupon_discount, $order->price_currency); ?></strong>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="row-seperator"></div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 col-left">
                                            <?= trans("total"); ?>
                                        </div>
                                        <div class="col-6 col-right">
                                            <?php $priceSecondCurrency = '';
                                            $transaction = getTransactionByOrderId($order->id);
                                            if (!empty($transaction) && $transaction->currency != $order->price_currency):
                                                $priceSecondCurrency = priceCurrencyFormat($transaction->payment_amount, $transaction->currency);
                                            endif; ?>
                                            <strong class="font-600">
                                                <?= priceFormatted($order->price_total, $order->price_currency);
                                                if (!empty($priceSecondCurrency)):?>
                                                    <br><span style="font-weight: 400;white-space: nowrap;">(<?= trans("paid"); ?>:&nbsp;<?= $priceSecondCurrency; ?>&nbsp;<?= $transaction->currency; ?>)</span>
                                                <?php endif; ?>
                                            </strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if ($order->payment_method != 'Cash On Delivery' || $order->payment_status == 'payment_received'):
                    if (!empty($shipping)): ?>
                        <p class="text-confirm-order">*<?= trans("confirm_order_received_warning"); ?></p>
                    <?php endif;
                endif;
                if ($order->payment_method == 'Cash On Delivery' && dateDifferenceInHours(date('Y-m-d H:i:s'), $order->created_at) <= 24):
                    if ($order->status != 2):?>
                        <p class="text-confirm-order text-danger">*<?= trans("cod_cancel_exp"); ?></p>
                    <?php endif;
                endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reportPaymentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content modal-custom">
            <form action="<?= base_url('bank-transfer-payment-report-post'); ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <input type="hidden" name="back_url" value="<?= getCurrentUrl(); ?>">
                <div class="modal-header">
                    <h5 class="modal-title"><?= trans("report_bank_transfer"); ?></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true"><i class="icon-close"></i> </span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="order_number" class="form-control form-input" value="<?= esc($order->order_number); ?>">
                    <div class="form-group">
                        <label><?= trans("payment_note"); ?></label>
                        <textarea name="payment_note" class="form-control form-textarea" maxlength="499"></textarea>
                    </div>
                    <div class="form-group">
                        <label><?= trans("receipt"); ?>
                            <small>(.png, .jpg, .jpeg)</small>
                        </label>
                        <div>
                            <a class='btn btn-md btn-secondary btn-file-upload'>
                                <?= trans('select_image'); ?>
                                <input type="file" name="file" size="40" accept=".png, .jpg, .jpeg" onchange="$('#upload-file-info').html($(this).val().replace(/.*[\/\\]/, ''));">
                            </a>
                            <br>
                            <span class='badge badge-info' id="upload-file-info"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-md btn-custom float-right"><?= trans("submit"); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="bankAccountsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content modal-custom">
            <div class="modal-header">
                <h5 class="modal-title"><?= trans("bank_accounts"); ?></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true"><i class="icon-close"></i> </span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted"><?= trans("bank_accounts_exp"); ?></p>
                <?= $paymentSettings->bank_transfer_accounts; ?>
            </div>
        </div>
    </div>
</div>
<?= view('partials/_modal_rate_product'); ?>
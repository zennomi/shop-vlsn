<!DOCTYPE html>
<html lang="<?= $activeLang->short_form; ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <title><?= esc($title); ?> - <?= esc($baseSettings->site_title); ?></title>
    <meta name="description" content="<?= esc($description); ?>"/>
    <meta name="keywords" content="<?= esc($keywords); ?>"/>
    <meta name="author" content="<?= esc($generalSettings->application_name); ?>"/>
    <link rel="shortcut icon" type="image/png" href="<?= getFavicon(); ?>"/>
    <meta property="og:locale" content="en-US"/>
    <meta property="og:site_name" content="<?= esc($generalSettings->application_name); ?>"/>
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css'); ?>"/>
</head>
<body>
<div class="container" style="width: 898px; max-width: 898px;min-width: 898px;">
    <div class="row">
        <div class="col-12">
            <div class="container-invoice">
                <div id="content" class="card">
                    <div class="card-body invoice p-0">
                        <div class="row">
                            <div class="col-12">
                                <h1 style="text-align: center; font-size: 36px;font-weight: 400;margin-top: 20px;"><?= trans("invoice"); ?></h1>
                            </div>
                        </div>
                        <div class="row" style="padding: 45px 30px;">
                            <div class="col-6">
                                <div class="logo">
                                    <img src="<?= getLogo(); ?>" alt="logo">
                                </div>
                                <div>
                                    <p style="margin-bottom: 5px;"><?= esc($baseSettings->contact_address); ?></p>
                                    <p style="margin-bottom: 5px;"><?= esc($baseSettings->contact_email); ?></p>
                                    <p style="margin-bottom: 5px;"><?= esc($baseSettings->contact_phone); ?></p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="float-right">
                                    <p class="font-weight-bold mb-1"><span style="display: inline-block;width: 100px;"><?= trans("invoice"); ?>:</span>#<?= 'VN' . $order->order_number; ?></p>
                                    <p class="font-weight-bold"><span style="display: inline-block;width: 100px;"><?= trans("date"); ?>:</span><?= formatDate($order->created_at); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="padding: 45px 30px;">
                            <div class="col-6">
                                <p class="font-weight-bold mb-3"><?= trans("client_information"); ?></p>
                                <p class="mb-1"><?= esc($invoice->client_first_name); ?>&nbsp;<?= esc($invoice->client_last_name); ?>&nbsp;(<?= $invoice->client_username; ?>)</p>
                                <?php if (!empty($invoice->client_address)): ?>
                                    <p class="mb-1"><?= esc($invoice->client_address); ?></p>
                                <?php endif;
                                if (!empty($invoice->client_state)): ?>
                                    <p class="mb-1"><?= !empty($invoice->client_city) ? $invoice->client_city . ', ' : '' ?><?= esc($invoice->client_state); ?></p>
                                <?php endif;
                                if (!empty($invoice->client_country)): ?>
                                    <p class="mb-1"><?= esc($invoice->client_country); ?></p>
                                <?php endif;
                                if ($generalSettings->show_customer_phone_seller == 1):
                                    if (!empty($invoice->client_phone_number)): ?>
                                        <p class="mb-1"><?= esc($invoice->client_phone_number); ?></p>
                                    <?php endif;
                                endif; ?>
                            </div>
                            <div class="col-6">
                                <div class="float-right">
                                    <p class="font-weight-bold mb-3"><?= trans("payment_details"); ?></p>
                                    <p class="mb-1"><span style="display: inline-block;min-width: 158px;"><?= trans("payment_status"); ?>:</span><?= getPaymentStatus($order->payment_status); ?></p>
                                    <p class="mb-1"><span style="display: inline-block;min-width: 158px;"><?= trans("payment_method"); ?>:</span><?= getPaymentMethod($order->payment_method); ?></p>
                                    <p class="mb-1"><span style="display: inline-block;min-width: 158px;"><?= trans("currency"); ?>:</span><?= $order->price_currency; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="row p-4">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th class="border-0 font-weight-bold"><?= trans("seller"); ?></th>
                                            <th class="border-0 font-weight-bold"><?= trans("product_id"); ?></th>
                                            <th class="border-0 font-weight-bold"><?= trans("description"); ?></th>
                                            <th class="border-0 font-weight-bold"><?= trans("quantity"); ?></th>
                                            <th class="border-0 font-weight-bold"><?= trans("unit_price"); ?></th>
                                            <?php if ($generalSettings->vat_status): ?>
                                                <th class="border-0 font-weight-bold"><?= trans("vat"); ?></th>
                                            <?php endif; ?>
                                            <th class="border-0 font-weight-bold"><?= trans("total"); ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $saleSubtotal = 0;
                                        $saleVat = 0;
                                        $saleTotal = 0;
                                        $saleShipping = 0;
                                        $shipping = false;
                                        if (!empty($invoiceItems) && is_array($invoiceItems)):
                                            foreach ($invoiceItems as $item):
                                                if (!empty($item['id'])):
                                                    $orderProduct = getOrderProduct($item['id']);
                                                    if (!empty($orderProduct)):
                                                        if ($orderProduct->product_type == 'physical') {
                                                            $shipping = true;
                                                        }
                                                        $showItem = false;
                                                        if ($orderProduct->seller_id == user()->id) {
                                                            $showItem = true;
                                                        }
                                                        if ($showItem == true):
                                                            $saleSubtotal += $orderProduct->product_unit_price * $orderProduct->product_quantity;
                                                            $saleVat += $orderProduct->product_vat;
                                                            $saleShipping = $orderProduct->seller_shipping_cost;
                                                            $saleTotal += $orderProduct->product_total_price; ?>
                                                            <tr style="font-size: 15px;">
                                                                <td><?= !empty($item['seller']) ? esc($item['seller']) : ''; ?></td>
                                                                <td><?= $orderProduct->product_id; ?></td>
                                                                <td><?= $orderProduct->product_title; ?></td>
                                                                <td><?= $orderProduct->product_quantity; ?></td>
                                                                <td style="white-space: nowrap"><?= priceFormatted($orderProduct->product_unit_price, $orderProduct->product_currency); ?></td>
                                                                <?php if ($generalSettings->vat_status): ?>
                                                                    <td style="white-space: nowrap">
                                                                        <?php if (!empty($orderProduct->product_vat)): ?>
                                                                            <?= priceFormatted($orderProduct->product_vat, $orderProduct->product_currency); ?>&nbsp;(<?= $orderProduct->product_vat_rate; ?>%)
                                                                        <?php endif; ?>
                                                                    </td>
                                                                <?php endif; ?>
                                                                <td style="white-space: nowrap"><?= priceFormatted($orderProduct->product_total_price, $orderProduct->product_currency); ?></td>
                                                            </tr>
                                                        <?php endif;
                                                    endif;
                                                endif;
                                            endforeach;
                                        endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="order-total float-right">
                                    <div class="row mb-2">
                                        <div class="col-6 col-left">
                                            <?= trans("subtotal"); ?>
                                        </div>
                                        <div class="col-6 col-right">
                                            <strong class="font-600"><?= priceFormatted($saleSubtotal, $order->price_currency); ?></strong>
                                        </div>
                                    </div>
                                    <?php if (!empty($saleVat)): ?>
                                        <div class="row mb-2">
                                            <div class="col-6 col-left">
                                                <?= trans("vat"); ?>
                                            </div>
                                            <div class="col-6 col-right">
                                                <strong class="font-600"><?= priceFormatted($saleVat, $order->price_currency); ?></strong>
                                            </div>
                                        </div>
                                    <?php endif;
                                    if ($shipping): ?>
                                        <div class="row mb-2">
                                            <div class="col-6 col-left">
                                                <?= trans("shipping"); ?>
                                            </div>
                                            <div class="col-6 col-right">
                                                <strong class="font-600"><?= priceFormatted($saleShipping, $order->price_currency); ?></strong>
                                            </div>
                                        </div>
                                    <?php endif;
                                    if ($order->coupon_discount > 0):
                                        if ($order->coupon_seller_id == user()->id) {
                                            $saleTotal = $saleTotal - $order->coupon_discount;
                                        }
                                        $showDiscount = false;
                                        if ($order->coupon_seller_id == user()->id) {
                                            $showDiscount = true;
                                        }
                                        if ($showDiscount):?>
                                            <div class="row mb-2">
                                                <div class="col-6 col-left">
                                                    <?= trans("discount"); ?>
                                                </div>
                                                <div class="col-6 col-right">
                                                    <strong class="font-600">-<?= priceFormatted($order->coupon_discount, $order->price_currency); ?></strong>
                                                </div>
                                            </div>
                                        <?php endif;
                                    endif; ?>
                                    <div class="row mb-2">
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
                                                <?php $saleTotal = $saleTotal + $saleShipping; ?>
                                                <?= priceFormatted($saleTotal, $order->price_currency);
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
                    <style>
                        body {
                            font-size: 16px !important;
                        }

                        .logo img {
                            width: 160px;
                            height: auto;
                        }

                        .container-invoice {
                            max-width: 900px;
                            margin: 0 auto;
                        }

                        table {
                            border-bottom: 1px solid #dee2e6;
                        }

                        table th {
                            font-size: 14px;
                            white-space: nowrap;
                        }

                        .order-total {
                            width: 400px;
                            max-width: 100%;
                            float: right;
                            padding: 20px;
                        }

                        .order-total .col-left {
                            font-weight: 600;
                        }

                        .order-total .col-right {
                            text-align: right;
                        }

                        #btn_print {
                            min-width: 180px;
                        }

                        @media print {
                            .hidden-print {
                                display: none !important;
                            }
                        }
                    </style>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container" style="margin-bottom: 100px;">
    <div class="row">
        <div class="col-12 text-center mt-3">
            <button id="btn_print" class="btn btn-secondary btn-md hidden-print">
                <svg id="i-print" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="16" height="16" fill="none" stroke="currentcolor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" style="margin-top: -4px;">
                    <path d="M7 25 L2 25 2 9 30 9 30 25 25 25 M7 19 L7 30 25 30 25 19 Z M25 9 L25 2 7 2 7 9 M22 14 L25 14"/>
                </svg>
                &nbsp;&nbsp;<?= trans("print"); ?></button>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/js/jquery-3.5.1.min.js'); ?>"></script>
<script>
    $(document).on('click', '#btn_print', function () {
        window.print();
    });
</script>
</body>
</html>
<?= view('email/_header', ['title' => trans("email_text_thank_for_order")]); ?>
<?php $emailData = unserializeData($emailRow->email_data); ?>
    <table role="presentation" class="main">
        <?php if (!empty($emailData['orderId'])):
            $order = getOrder($emailData['orderId']);
            if (!empty($order)):
                $orderProducts = getOrderProducts($order->id); ?>
                <tr>
                    <td class="wrapper">
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>
                                    <h1 style="text-decoration: none; font-size: 24px;line-height: 28px;font-weight: bold"><?= trans("email_text_thank_for_order"); ?></h1>
                                    <div class="mailcontent" style="line-height: 26px;font-size: 14px;">
                                        <p style='text-align: left;color: #555;'><?= trans("email_text_new_order"); ?></p><br>
                                        <h2 style="margin-bottom: 10px; font-size: 16px;font-weight: 600;"><?= trans("order_information"); ?></h2>
                                        <p style="color: #555;">
                                            <?= trans("order"); ?>:&nbsp;#<?= $order->order_number; ?><br>
                                            <?= trans("payment_status"); ?>:&nbsp;<?= trans($order->payment_status); ?><br>
                                            <?= trans("payment_method"); ?>:&nbsp;<?= getPaymentMethod($order->payment_method); ?>
                                            <br>
                                            <?= trans("date"); ?>:&nbsp;<?= formatDate($order->created_at); ?><br>
                                        </p>
                                    </div>
                                    <?php $shipping = unserializeData($order->shipping);
                                    if (!empty($shipping)):?>
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="margin-top: 30px;">
                                            <tr>
                                                <td>
                                                    <h3 style="margin-bottom: 10px; font-size: 16px;font-weight: 600;"><?= trans("shipping_address"); ?></h3>
                                                    <p style="color: #555; padding-right: 10px;">
                                                        <?= trans("first_name"); ?>:&nbsp;<?= !empty($shipping->sFirstName) ? esc($shipping->sFirstName) : ''; ?><br>
                                                        <?= trans("last_name"); ?>:&nbsp;<?= !empty($shipping->sLastName) ? esc($shipping->sLastName) : ''; ?><br>
                                                        <?= trans("email"); ?>:&nbsp;<?= !empty($shipping->sEmail) ? esc($shipping->sEmail) : ''; ?><br>
                                                        <?= trans("phone_number"); ?>:&nbsp;<?= !empty($shipping->sPhoneNumber) ? esc($shipping->sPhoneNumber) : ''; ?><br>
                                                        <?= trans("address"); ?>:&nbsp;<?= !empty($shipping->sAddress) ? esc($shipping->sAddress) : ''; ?><br>
                                                        <?= trans("country"); ?>:&nbsp;<?= !empty($shipping->sCountry) ? esc($shipping->sCountry) : ''; ?><br>
                                                        <?= trans("state"); ?>:&nbsp;<?= !empty($shipping->sState) ? esc($shipping->sState) : ''; ?><br>
                                                        <?= trans("city"); ?>:&nbsp;<?= !empty($shipping->sCity) ? esc($shipping->sCity) : ''; ?><br>
                                                        <?= trans("zip_code"); ?>:&nbsp;<?= !empty($shipping->sZipCode) ? esc($shipping->sZipCode) : ''; ?><br>
                                                    </p>
                                                </td>
                                                <td>
                                                    <h3 style="margin-bottom: 10px; font-size: 16px;font-weight: 600;"><?= trans("billing_address"); ?></h3>
                                                    <p style="color: #555; padding-right: 10px;">
                                                        <?= trans("first_name"); ?>:&nbsp;<?= !empty($shipping->bFirstName) ? esc($shipping->bFirstName) : ''; ?><br>
                                                        <?= trans("last_name"); ?>:&nbsp;<?= !empty($shipping->bLastName) ? esc($shipping->bLastName) : ''; ?><br>
                                                        <?= trans("email"); ?>:&nbsp;<?= !empty($shipping->bEmail) ? esc($shipping->bEmail) : ''; ?><br>
                                                        <?= trans("phone_number"); ?>:&nbsp;<?= !empty($shipping->bPhoneNumber) ? esc($shipping->bPhoneNumber) : ''; ?><br>
                                                        <?= trans("address"); ?>:&nbsp;<?= !empty($shipping->bAddress) ? esc($shipping->bAddress) : ''; ?><br>
                                                        <?= trans("country"); ?>:&nbsp;<?= !empty($shipping->bCountry) ? esc($shipping->bCountry) : ''; ?><br>
                                                        <?= trans("state"); ?>:&nbsp;<?= !empty($shipping->bState) ? esc($shipping->bState) : ''; ?><br>
                                                        <?= trans("city"); ?>:&nbsp;<?= !empty($shipping->bCity) ? esc($shipping->bCity) : ''; ?><br>
                                                        <?= trans("zip_code"); ?>:&nbsp;<?= !empty($shipping->bZipCode) ? esc($shipping->bZipCode) : ''; ?><br>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    <?php endif; ?>
                                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="text-align: left" class="table-products">
                                        <tr>
                                            <th style="padding: 10px 0; border-bottom: 2px solid #ddd;"><?= trans("product"); ?></th>
                                            <th style="padding: 10px 0; border-bottom: 2px solid #ddd;"><?= trans("unit_price"); ?></th>
                                            <th style="padding: 10px 0; border-bottom: 2px solid #ddd;"><?= trans("quantity"); ?></th>
                                            <th style="padding: 10px 0; border-bottom: 2px solid #ddd;"><?= trans("vat"); ?></th>
                                            <th style="padding: 10px 0; border-bottom: 2px solid #ddd;"><?= trans("total"); ?></th>
                                        </tr>
                                        <?php if (!empty($orderProducts)):
                                            foreach ($orderProducts as $item): ?>
                                                <tr>
                                                    <td style="width: 40%; padding: 15px 0; border-bottom: 1px solid #ddd;"><?= esc($item->product_title); ?></td>
                                                    <td style="padding: 12px 2px; border-bottom: 1px solid #ddd;"><?= priceFormatted($item->product_unit_price, $item->product_currency); ?></td>
                                                    <td style="padding: 12px 2px; border-bottom: 1px solid #ddd;"><?= $item->product_quantity; ?></td>
                                                    <?php if (!empty($order->price_vat)): ?>
                                                        <td style="padding: 12px 2px; border-bottom: 1px solid #ddd;">
                                                            <?php if (!empty($item->product_vat)): ?>
                                                                <?= priceFormatted($item->product_vat, $item->product_currency); ?>&nbsp;(<?= $item->product_vat_rate; ?>%)
                                                            <?php endif; ?>
                                                        </td>
                                                    <?php else: ?>
                                                        <td style="padding: 12px 2px; border-bottom: 1px solid #ddd;">-</td>
                                                    <?php endif; ?>
                                                    <td style="padding: 12px 2px; border-bottom: 1px solid #ddd;"><?= priceFormatted($item->product_total_price, $item->product_currency); ?></td>
                                                </tr>
                                            <?php endforeach;
                                        endif; ?>
                                    </table>
                                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="text-align: right;margin-top: 40px;">
                                        <tr>
                                            <td style="width: 70%"><?= trans("subtotal"); ?></td>
                                            <td style="width: 30%;padding-right: 15px;font-weight: 600;"><?= priceFormatted($order->price_subtotal, $order->price_currency); ?></td>
                                        </tr>
                                        <?php if (!empty($order->price_vat)): ?>
                                            <tr>
                                                <td style="width: 70%"><?= trans("vat"); ?></td>
                                                <td style="width: 30%;padding-right: 15px;font-weight: 600;"><?= priceFormatted($order->price_vat, $order->price_currency); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <tr>
                                            <td style="width: 70%"><?= trans("shipping"); ?></td>
                                            <td style="width: 30%;padding-right: 15px;font-weight: 600;"><?= priceFormatted($order->price_shipping, $order->price_currency); ?></td>
                                        </tr>
                                        <?php if ($order->coupon_discount > 0): ?>
                                            <tr>
                                                <td style="width: 70%"><?= trans("coupon"); ?>&nbsp;&nbsp;[<?= esc($order->coupon_code); ?>]</td>
                                                <td style="width: 30%;padding-right: 15px;font-weight: 600;">-<?= priceFormatted($order->coupon_discount, $order->price_currency); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <tr>
                                            <?php $priceSecondCurrency = '';
                                            $transaction = getTransactionByOrderId($order->id);
                                            if (!empty($transaction) && $transaction->currency != $order->price_currency):
                                                $priceSecondCurrency = priceCurrencyFormat($transaction->payment_amount, $transaction->currency);
                                            endif; ?>
                                            <td style="width: 70%;font-weight: bold"><?= trans("total"); ?></td>
                                            <td style="width: 30%;padding-right: 15px;font-weight: 600;">
                                                <?= priceFormatted($order->price_total, $order->price_currency);
                                                if (!empty($priceSecondCurrency)):?>
                                                    <br><span style="font-weight: 400;white-space: nowrap;">(<?= trans("paid"); ?>:&nbsp;<?= $priceSecondCurrency; ?>&nbsp;<?= $transaction->currency; ?>)</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </table>
                                    <?php if ($order->buyer_type != 'guest'): ?>
                                        <p style='text-align: center;margin-top: 40px;'>
                                            <a href="<?= generateUrl('order_details') . '/' . $order->order_number; ?>" style='font-size: 14px;text-decoration: none;padding: 14px 40px;background-color: <?= $generalSettings->site_color; ?>;color: #ffffff !important; border-radius: 3px;'>
                                                <?= trans("see_order_details"); ?>
                                            </a>
                                        </p>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            <?php endif;
        endif; ?>
    </table>
<?= view('email/_footer'); ?>
<?= view('email/_header', ['title' => trans("you_have_new_order")]); ?>
<?php $emailData = unserializeData($emailRow->email_data); ?>
    <table role="presentation" class="main">
        <?php if (!empty($emailData['orderId'])):
            $order = getOrder($emailData['orderId']);
            $seller = null;
            if (!empty($emailData['sellerId'])) {
                $seller = getUser($emailData['sellerId']);
            }
            if (!empty($order) && !empty($seller)):
                $orderProducts = getOrderProducts($order->id); ?>
                <tr>
                    <td class="wrapper">
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>
                                    <h1 style="text-decoration: none; font-size: 24px;line-height: 28px;font-weight: bold"><?= trans("you_have_new_order"); ?></h1>
                                    <div class="mailcontent" style="line-height: 26px;font-size: 14px;">
                                        <h2 style="margin-bottom: 10px; font-size: 16px;font-weight: 600;"><?= trans("order_information"); ?></h2>
                                        <p style="color: #555;">
                                            <?= trans("order"); ?>:&nbsp;#<?= esc($order->order_number); ?><br>
                                            <?= trans("payment_status"); ?>:&nbsp;<?= trans($order->payment_status); ?><br>
                                            <?= trans("payment_method"); ?>:&nbsp;<?= getPaymentMethod($order->payment_method); ?><br>
                                            <?= trans("date"); ?>:&nbsp;<?= formatDate($order->created_at); ?><br>
                                        </p>
                                    </div>
                                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="text-align: left" class="table-products">
                                        <tr>
                                            <th style="padding: 10px 0; border-bottom: 2px solid #ddd;"><?= trans("product"); ?></th>
                                            <th style="padding: 10px 0; border-bottom: 2px solid #ddd;"><?= trans("unit_price"); ?></th>
                                            <th style="padding: 10px 0; border-bottom: 2px solid #ddd;"><?= trans("quantity"); ?></th>
                                            <th style="padding: 10px 0; border-bottom: 2px solid #ddd;"><?= trans("vat"); ?></th>
                                            <th style="padding: 10px 0; border-bottom: 2px solid #ddd;"><?= trans("total"); ?></th>
                                        </tr>
                                        <?php $orderModel = new \App\Models\OrderModel();
                                        $sellerOrderProducts = $orderModel->getSellerOrderProducts($order->id, $seller->id);
                                        if (!empty($sellerOrderProducts)):
                                            foreach ($sellerOrderProducts as $item): ?>
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
                                    <p style='text-align: center;margin-top: 40px;'>
                                        <a href="<?= generateDashUrl('sale') . '/' . $order->order_number; ?>" style='font-size: 14px;text-decoration: none;padding: 14px 40px;background-color: <?= $generalSettings->site_color; ?>;color: #ffffff !important; border-radius: 3px;'>
                                            <?= trans("see_order_details"); ?>
                                        </a>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            <?php endif;
        endif; ?>
    </table>
<?= view('email/_footer'); ?>
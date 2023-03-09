<div class="row support-admin">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans('refund'); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl("refund-requests"); ?>" class="btn btn-success btn-add-new">
                        <i class="fa fa-list-ul"></i>&nbsp;&nbsp;<?= trans('refund_requests'); ?>
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="col-12">
                    <div class="ticket-container">
                        <div class="new-ticket-content new-ticket-content-reply">
                            <div class="ticket-header">
                                <p>
                                    <strong><?= trans("product"); ?>:&nbsp;
                                        <a href="<?= adminUrl('order-details/' . $refundRequest->order_id); ?>" target="_blank">
                                            #<?= $refundRequest->order_number; ?>&nbsp;-&nbsp;<?= esc($product->product_title); ?>
                                        </a>
                                    </strong>
                                </p>
                                <div class="row row-ticket-details">
                                    <div class="col-xs-4 col-md-2 m-b-5">
                                        <strong><?= trans("status"); ?></strong>
                                        <?php if ($refundRequest->status == 1): ?>
                                            <label class="label label-success"><?= trans("approved"); ?></label>
                                        <?php elseif ($refundRequest->status == 2): ?>
                                            <label class="label label-danger"><?= trans("declined"); ?></label>
                                        <?php else: ?>
                                            <label class="label label-default"><?= trans("order_processing"); ?></label>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-xs-4 col-md-2 m-b-5">
                                        <strong><?= trans("total"); ?></strong>
                                        <span><?= priceFormatted($product->product_total_price, $product->product_currency); ?></span>
                                    </div>
                                    <div class="col-xs-4 col-md-2 m-b-5">
                                        <strong><?= trans("buyer"); ?></strong>
                                        <?php $buyer = getUser($product->buyer_id);
                                        if (!empty($buyer)): ?>
                                            <a href="<?= generateProfileUrl($buyer->slug); ?>" target="_blank" class="font-600"><?= esc(getUsername($buyer)); ?></a>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-xs-4 col-md-2 m-b-5">
                                        <strong><?= trans("seller"); ?></strong>
                                        <?php $seller = getUser($product->seller_id);
                                        if (!empty($seller)): ?>
                                            <a href="<?= generateProfileUrl($seller->slug); ?>" target="_blank" class="font-600"><?= esc(getUsername($seller)); ?></a>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-xs-4 col-md-2 m-b-5">
                                        <strong><?= trans("last_update"); ?></strong>
                                        <span><?= timeAgo($refundRequest->updated_at); ?></span>
                                    </div>
                                    <div class="col-xs-4 col-md-2 m-b-5">
                                        <strong><?= trans("date"); ?></strong>
                                        <span><?= formatDate($refundRequest->created_at); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ticket-content">
                            <div class="row">
                                <div class="col-sm-12">
                                    <ul class="list-unstyled">
                                        <?php if (!empty($messages)):
                                            foreach ($messages as $message):
                                                $user = getUser($message->user_id);
                                                if (!empty($user)):?>
                                                    <li class="media">
                                                        <div class="left">
                                                            <img class="img-profile" src="<?= getUserAvatar($user) ?>" alt="">
                                                        </div>
                                                        <div class="right">
                                                            <div class="media-body">
                                                                <h5 class="title m-t-0 mb-3">
                                                                    <a href="<?= generateProfileUrl($user->slug) ?>" class="font-color" target="_blank"><?= esc(getUsername($user)); ?></a>
                                                                </h5>
                                                                <span class="date text-right"><?= timeAgo($message->created_at); ?></span>
                                                                <div class="message">
                                                                    <?= $message->message; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                <?php endif;
                                            endforeach;
                                        endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .support-admin .ticket-content .media .media-body .message {
        font-size: 14px !important;
    }
</style>
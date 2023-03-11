<div id="wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="support">
                    <nav class="nav-breadcrumb" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= langBaseUrl(); ?>"><?= trans("home"); ?></a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?= trans("refund"); ?></li>
                        </ol>
                    </nav>
                    <div class="row justify-content-center">
                        <div class="col-12 m-t-15 m-b-30">
                            <h1 class="page-title page-title-ticket"><?= trans("refund"); ?></h1>
                            <a href="<?= generateUrl('refund_requests'); ?>" class="btn btn-info color-white float-right">
                                <svg width="18" height="18" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg" fill="#fff" class="mds-svg-icon">
                                    <path d="M384 1408q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm0-512q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm1408 416v192q0 13-9.5 22.5t-22.5 9.5h-1216q-13 0-22.5-9.5t-9.5-22.5v-192q0-13 9.5-22.5t22.5-9.5h1216q13 0 22.5 9.5t9.5 22.5zm-1408-928q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm1408 416v192q0 13-9.5 22.5t-22.5 9.5h-1216q-13 0-22.5-9.5t-9.5-22.5v-192q0-13 9.5-22.5t22.5-9.5h1216q13 0 22.5 9.5t9.5 22.5zm0-512v192q0 13-9.5 22.5t-22.5 9.5h-1216q-13 0-22.5-9.5t-9.5-22.5v-192q0-13 9.5-22.5t22.5-9.5h1216q13 0 22.5 9.5t9.5 22.5z"/>
                                </svg>
                                <?= trans("refund_requests") ?>
                            </a>
                        </div>
                        <div class="col-12">
                            <div class="ticket-container shadow-sm">
                                <div class="new-ticket-content new-ticket-content-reply">
                                    <div class="ticket-header">
                                        <p><strong><?= trans("product"); ?>:&nbsp;
                                                <a href="<?= generateUrl('order-details/') . esc($refundRequest->order_number); ?>" target="_blank">
                                                    #<?= esc($refundRequest->order_number); ?>&nbsp;-&nbsp;<?= esc($product->product_title); ?>
                                                </a>
                                            </strong></p>
                                        <div class="row row-ticket-details">
                                            <div class="col-12 col-md-2">
                                                <strong><?= trans("status"); ?></strong>
                                                <?php if ($refundRequest->status == 1): ?>
                                                    <label class="badge badge-lg badge-success"><?= trans("approved"); ?></label>
                                                <?php elseif ($refundRequest->status == 2): ?>
                                                    <label class="badge badge-lg badge-danger"><?= trans("declined"); ?></label>
                                                <?php else: ?>
                                                    <label class="badge badge-lg badge-secondary"><?= trans("order_processing"); ?></label>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-12 col-md-2">
                                                <strong><?= trans("total"); ?></strong>
                                                <span><?= priceFormatted($product->product_total_price, $product->product_currency); ?></span>
                                            </div>
                                            <div class="col-12 col-md-2">
                                                <strong><?= trans("seller"); ?></strong>
                                                <span>
                                                <?php $seller = getUser($product->seller_id);
                                                if (!empty($seller)): ?>
                                                    <a href="<?= generateProfileUrl($seller->slug); ?>" target="_blank" class="font-600"><?= esc(getUsername($seller)); ?></a>
                                                <?php endif; ?>
                                                </span>
                                            </div>
                                            <div class="col-12 col-md-2">
                                                <strong><?= trans("last_update"); ?></strong>
                                                <span><?= timeAgo($refundRequest->updated_at); ?></span>
                                            </div>
                                            <div class="col-12 col-md-2">
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
                                                        $user = getUser($message->user_id); ?>
                                                        <li class="media<?= $message->is_buyer != 1 ? ' media-client' : ''; ?>">
                                                            <img class="img-profile" src="<?= getUserAvatar($user) ?>" alt="">
                                                            <div class="media-body">
                                                                <h5 class="title mt-0 mb-3">
                                                                    <a href="<?= generateProfileUrl($user->slug) ?>" class="font-color" target="_blank"><?= esc(getUsername($user)); ?></a>
                                                                </h5>
                                                                <span class="date text-right"><?= timeAgo($message->created_at); ?></span>
                                                                <div class="message">
                                                                    <?= $message->message; ?>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    <?php endforeach;
                                                endif; ?>
                                            </ul>
                                        </div>
                                        <?php if ($refundRequest->status == 0): ?>
                                            <div class="col-sm-12">
                                                <form action="<?= base_url('add-refund-message'); ?>" method="post">
                                                    <?= csrf_field(); ?>
                                                    <li class="media">
                                                        <input type="hidden" name="id" value="<?= $refundRequest->id; ?>">
                                                        <input type="hidden" name="back_url" value="<?= getCurrentUrl(); ?>">
                                                        <img class="img-profile" src="<?= IMG_BASE64_1x1; ?>">
                                                        <div class="media-body refund-media-body text-right">
                                                            <div class="form-group">
                                                                <textarea name="message" class="form-control form-textarea" placeholder="<?= trans("write_a_message", true); ?>" required></textarea>
                                                            </div>
                                                            <div class="form-group">
                                                                <button type="submit" class="btn btn-md btn-custom"><?= trans("submit"); ?></button>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                        <div class="col-sm-12 text-center m-t-30">
                                            <?php if ($refundRequest->status == 1): ?>
                                                <div class="alert alert-success" role="alert">
                                                    <?= trans("refund_approved_exp"); ?>
                                                </div>
                                            <?php endif;
                                            if ($refundRequest->status == 2): ?>
                                                <div class="alert alert-danger" role="alert">
                                                    <?= trans("refund_declined_exp"); ?>
                                                </div>
                                                <a href="<?= generateUrl('help_center', 'submit_request'); ?>" class="btn btn-lg btn-custom">
                                                    <svg width="18" height="18" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg" fill="#fff" class="mds-svg-icon">
                                                        <path d="M896 0q182 0 348 71t286 191 191 286 71 348-71 348-191 286-286 191-348 71-348-71-286-191-191-286-71-348 71-348 191-286 286-191 348-71zm0 128q-190 0-361 90l194 194q82-28 167-28t167 28l194-194q-171-90-361-90zm-678 1129l194-194q-28-82-28-167t28-167l-194-194q-90 171-90 361t90 361zm678 407q190 0 361-90l-194-194q-82 28-167 28t-167-28l-194 194q171 90 361 90zm0-384q159 0 271.5-112.5t112.5-271.5-112.5-271.5-271.5-112.5-271.5 112.5-112.5 271.5 112.5 271.5 271.5 112.5zm484-217l194 194q90-171 90-361t-90-361l-194 194q28 82 28 167t-28 167z"/>
                                                    </svg>
                                                    <?= trans("contact_support"); ?>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
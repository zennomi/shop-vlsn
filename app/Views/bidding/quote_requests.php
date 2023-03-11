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
                <h1 class="page-title"><?= $title; ?></h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="row-custom">
                    <div class="profile-tab-content">
                        <?= view('partials/_messages'); ?>
                        <div class="table-responsive">
                            <table class="table table-quote_requests table-striped">
                                <thead>
                                <tr>
                                    <th scope="col"><?= trans("quote"); ?></th>
                                    <th scope="col"><?= trans("product"); ?></th>
                                    <th scope="col"><?= trans("seller"); ?></th>
                                    <th scope="col"><?= trans("status"); ?></th>
                                    <th scope="col"><?= trans("sellers_bid"); ?></th>
                                    <th scope="col"><?= trans("updated"); ?></th>
                                    <th scope="col"><?= trans("options"); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($quoteRequests)): ?>
                                    <?php foreach ($quoteRequests as $quoteRequest): ?>
                                        <tr>
                                            <td>#<?= $quoteRequest->id; ?></td>
                                            <td>
                                                <?php $product = getProduct($quoteRequest->product_id);
                                                if (!empty($product)): ?>
                                                    <div class="table-item-product">
                                                        <div class="left">
                                                            <div class="img-table">
                                                                <a href="<?= generateProductUrl($product); ?>" target="_blank">
                                                                    <img src="<?= getProductMainImage($product->id, 'image_small'); ?>" data-src="" alt="" class="lazyload img-responsive post-image"/>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="right">
                                                            <a href="<?= generateProductUrl($product); ?>" target="_blank">
                                                                <h3 class="table-product-title"><?= esc($quoteRequest->product_title); ?></h3>
                                                            </a>
                                                            <?= trans("quantity") . ': ' . $quoteRequest->product_quantity; ?>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <h3 class="table-product-title"><?= esc($quoteRequest->product_title); ?></h3>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php $seller = getUser($quoteRequest->seller_id);
                                                if (!empty($seller)): ?>
                                                    <a href="<?= generateProfileUrl($seller->slug); ?>" target="_blank" class="font-600">
                                                        <?= esc(getUsername($seller)); ?>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= trans($quoteRequest->status); ?></td>
                                            <td>
                                                <?php if ($quoteRequest->status != 'new_quote_request' && $quoteRequest->price_offered != 0): ?>
                                                    <div class="table-seller-bid">
                                                        <p><strong><?= priceFormatted(@convertCurrencyByExchangeRate($quoteRequest->price_offered, $selectedCurrency->exchange_rate), $selectedCurrency->code); ?></strong></p>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= timeAgo($quoteRequest->updated_at); ?></td>
                                            <td>
                                                <?php if ($quoteRequest->status == 'pending_quote'): ?>
                                                    <form action="<?= base_url('accept-quote-post'); ?>" method="post">
                                                        <?= csrf_field(); ?>
                                                        <input type="hidden" name="id" class="form-control" value="<?= $quoteRequest->id; ?>">
                                                        <input type="hidden" name="back_url" class="form-control" value="<?=getCurrentUrl(); ?>">
                                                        <button type="submit" class="btn btn-sm btn-info btn-table-option"><?= trans("accept_quote"); ?></button>
                                                    </form>
                                                    <form action="<?= base_url('reject-quote-post'); ?>" method="post">
                                                        <?= csrf_field(); ?>
                                                        <input type="hidden" name="id" class="form-control" value="<?= $quoteRequest->id; ?>">
                                                        <input type="hidden" name="back_url" class="form-control" value="<?=getCurrentUrl(); ?>">
                                                        <button type="submit" class="btn btn-sm btn-secondary btn-table-option"><?= trans("reject_quote"); ?></button>
                                                    </form>
                                                <?php elseif ($quoteRequest->status == 'pending_payment'): ?>
                                                    <form action="<?= base_url('add-to-cart-quote'); ?>" method="post">
                                                        <?= csrf_field(); ?>
                                                        <input type="hidden" name="id" class="form-control" value="<?= $quoteRequest->id; ?>">
                                                        <button type="submit" class="btn btn-sm btn-info btn-table-option"><i class="icon-cart"></i>&nbsp;<?= trans("add_to_cart"); ?></button>
                                                    </form>
                                                <?php endif; ?>
                                                <button type="button" class="btn btn-sm btn-danger btn-table-option btn-delete-quote" onclick="deleteQuoteRequest(<?= $quoteRequest->id; ?>,'<?= trans("confirm_quote_request", true); ?>');"><?= trans("delete_quote"); ?></button>
                                            </td>
                                        </tr>
                                    <?php endforeach;
                                endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if (empty($quoteRequests)): ?>
                            <p class="text-center">
                                <?= trans("no_records_found"); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?php if (!empty($quoteRequests)): ?>
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
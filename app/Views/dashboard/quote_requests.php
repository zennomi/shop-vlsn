<div class="row">
    <div class="col-sm-12">
        <?= view('dashboard/includes/_messages'); ?>
    </div>
</div>
<div class="box">
    <div class="box-header with-border">
        <div class="left">
            <h3 class="box-title"><?= esc($title); ?></h3>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <div class="row table-filter-container">
                        <div class="col-sm-12">
                            <form action="<?= generateDashUrl('quote_requests'); ?>" method="get">
                                <div class="item-table-filter">
                                    <label><?= trans("status"); ?></label>
                                    <select name="status" class="form-control custom-select">
                                        <option value="" selected><?= trans("all"); ?></option>
                                        <option value="new_quote_request" <?= inputGet('status') == 'new_quote_request' ? 'selected' : ''; ?>><?= trans("new_quote_request"); ?></option>
                                        <option value="pending_quote" <?= inputGet('status') == 'pending_quote' ? 'selected' : ''; ?>><?= trans("pending_quote"); ?></option>
                                        <option value="pending_payment" <?= inputGet('status') == 'pending_payment' ? 'selected' : ''; ?>><?= trans("pending_payment"); ?></option>
                                        <option value="rejected_quote" <?= inputGet('status') == 'rejected_quote' ? 'selected' : ''; ?>><?= trans("rejected_quote"); ?></option>
                                        <option value="closed" <?= inputGet('status') == 'closed' ? 'selected' : ''; ?>><?= trans("closed"); ?></option>
                                        <option value="completed" <?= inputGet('status') == 'completed' ? 'selected' : ''; ?>><?= trans("completed"); ?></option>
                                    </select>
                                </div>
                                <div class="item-table-filter">
                                    <label><?= trans("search"); ?></label>
                                    <input name="q" class="form-control" placeholder="<?= trans("search"); ?>" type="search" value="<?= esc(inputGet('q')); ?>">
                                </div>
                                <div class="item-table-filter md-top-10" style="width: 65px; min-width: 65px;">
                                    <label style="display: block">&nbsp;</label>
                                    <button type="submit" class="btn bg-purple btn-filter"><?= trans("filter"); ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <table class="table table-bordered table-striped" role="grid">
                        <thead>
                        <tr role="row">
                            <th><?= trans('quote'); ?></th>
                            <th><?= trans('product'); ?></th>
                            <th><?= trans('buyer'); ?></th>
                            <th><?= trans('status'); ?></th>
                            <th><?= trans('sellers_bid'); ?></th>
                            <th><?= trans('updated'); ?></th>
                            <th><?= trans('date'); ?></th>
                            <th class="max-width-120"><?= trans('options'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($quoteRequests)):
                            foreach ($quoteRequests as $item): ?>
                                <tr>
                                    <td>#<?= $item->id; ?></td>
                                    <td>
                                        <?php $product = getProduct($item->product_id);
                                        if (!empty($product)):?>
                                            <div class="img-table">
                                                <a href="<?= generateProductUrl($product); ?>" target="_blank">
                                                    <img src="<?= getProductMainImage($product->id, 'image_small'); ?>" data-src="" alt="" class="lazyload img-responsive post-image"/>
                                                </a>
                                            </div>
                                            <a href="<?= generateProductUrl($product); ?>" target="_blank" class="table-product-title"><?= esc($item->product_title); ?></a><br>
                                            <?= trans("quantity") . ': ' . $item->product_quantity; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php $user = getUser($item->buyer_id);
                                        if (!empty($user)):?>
                                            <div class="table-orders-user">
                                                <a href="<?= generateProfileUrl($user->slug); ?>" target="_blank"><?= esc(getUsername($user)); ?></a>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($item->status == "new_quote_request"): ?>
                                            <label class="label label-success"><?= trans($item->status); ?></label>
                                        <?php elseif ($item->status == "pending_quote"): ?>
                                            <label class="label label-warning"><?= trans($item->status); ?></label>
                                        <?php elseif ($item->status == "pending_payment"): ?>
                                            <label class="label label-info"><?= trans($item->status); ?></label>
                                        <?php elseif ($item->status == "rejected_quote"): ?>
                                            <label class="label label-danger"><?= trans($item->status); ?></label>
                                        <?php elseif ($item->status == "closed"): ?>
                                            <label class="label label-default"><?= trans($item->status); ?></label>
                                        <?php elseif ($item->status == "completed"): ?>
                                            <label class="label label-primary"><?= trans($item->status); ?></label>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($item->status != 'new_quote_request' && $item->price_offered != 0): ?>
                                            <div class="table-seller-bid">
                                                <label class="badge bg-success"><?= priceFormatted($item->price_offered, $item->price_currency); ?></label>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= timeAgo($item->updated_at); ?></td>
                                    <td><?= formatDate($item->created_at); ?></td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn bg-purple dropdown-toggle btn-select-option" type="button" data-toggle="dropdown"><?= trans('select_option'); ?>
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu options-dropdown">
                                                <?php if ($item->status == 'new_quote_request'): ?>
                                                    <li>
                                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#modalSubmitQuote<?= $item->id; ?>"><i class="fa fa-plus option-icon"></i><?= trans("submit_a_quote"); ?></a>
                                                    </li>
                                                <?php elseif ($item->status == 'pending_quote'): ?>
                                                    <li>
                                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#modalSubmitQuote<?= $item->id; ?>"><i class="fa fa-edit option-icon"></i><?= trans("update_quote"); ?></a>
                                                    </li>
                                                <?php elseif ($item->status == 'rejected_quote'): ?>
                                                    <li>
                                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#modalSubmitQuote<?= $item->id; ?>"><i class="fa fa-refresh option-icon"></i><?= trans("submit_a_new_quote"); ?></a>
                                                    </li>
                                                <?php endif; ?>
                                                <li>
                                                    <a href="javascript:void(0)" onclick="deleteItem('OrderController/deleteQuoteRequest','<?= $item->id; ?>','<?= trans("confirm_delete", true); ?>');"><i class="fa fa-trash option-icon"></i><?= trans('delete'); ?></a>
                                                </li>
                                            </ul>
                                        </div>
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

<?php if (!empty($quoteRequests)):
    foreach ($quoteRequests as $quoteRequest):
        $quoteProduct = getProduct($quoteRequest->product_id); ?>
        <div class="modal fade" id="modalSubmitQuote<?= $quoteRequest->id; ?>" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content modal-custom">
                    <form action="<?= base_url('submit-quote-post'); ?>" method="post">
                        <?= csrf_field(); ?>
                        <div class="modal-header">
                            <h5 class="modal-title"><?= trans("submit_a_quote"); ?></h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span aria-hidden="true"><i class="icon-close"></i> </span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" class="form-control" value="<?= $quoteRequest->id; ?>">
                            <div class="form-group">
                                <label class="control-label"><?= trans('price'); ?></label>
                                <div class="input-group">
                                    <span class="input-group-addon"><?= $defaultCurrency->symbol; ?></span>
                                    <input type="hidden" name="currency" value="<?= $paymentSettings->default_currency; ?>">
                                    <input type="text" name="price" aria-describedby="basic-addon1" class="form-control form-input price-input validate-price-input" data-item-id="<?= $quoteRequest->id; ?>" data-product-quantity="<?= $quoteRequest->product_quantity; ?>" placeholder="<?= $baseVars->inputInitialPrice; ?>" onpaste="return false;" maxlength="32" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <p class="calculated-price">
                                    <strong><?= trans("unit_price"); ?> (<?= $defaultCurrency->symbol; ?>):&nbsp;&nbsp;
                                        <span id="unit_price_<?= $quoteRequest->id; ?>" class="earned-price">
                                        <?= number_format(0, 2, '.', ''); ?>
                                    </span>
                                    </strong><br>
                                    <strong><?= trans("you_will_earn"); ?> (<?= $defaultCurrency->symbol; ?>):&nbsp;&nbsp;
                                        <span id="earned_price_<?= $quoteRequest->id; ?>" class="earned-price">
                                        <?php $earnedPrice = $quoteProduct->price - (($quoteProduct->price * $generalSettings->commission_rate) / 100);
                                        if (!empty($earnedPrice)) {
                                            $earnedPrice = number_format($earnedPrice, 2, '.', '');
                                        }
                                        echo getPrice($earnedPrice, 'input'); ?>
                                    </span>
                                    </strong>&nbsp;&nbsp;&nbsp;
                                    <small> (<?= trans("commission_rate"); ?>:&nbsp;&nbsp;<?= $generalSettings->commission_rate; ?>%)</small>
                                </p>
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
    <?php endforeach;
endif; ?>

<script>
    //calculate product earned value
    $(document).on("input keyup paste change", ".price-input", function () {
        var input_val = $(this).val();
        var data_item_id = $(this).attr('data-item-id');
        var data_product_quantity = $(this).attr('data-product-quantity');
        input_val = input_val.replace(',', '.');
        var price = parseFloat(input_val);
        var commission_rate = parseInt(MdsConfig.commissionRate);
        //calculate earned price
        if (!Number.isNaN(price)) {
            var earned_price = price - ((price * commission_rate) / 100);
            earned_price = earned_price.toFixed(2);
            if (MdsConfig.thousandsSeparator == ',') {
                earned_price = earned_price.replace('.', ',');
            }
        } else {
            earned_price = '0' + MdsConfig.thousandsSeparator + '00';
        }
        //calculate unit price
        if (!Number.isNaN(price)) {
            var unit_price = price / data_product_quantity;
            unit_price = unit_price.toFixed(2);
            if (MdsConfig.thousandsSeparator == ',') {
                unit_price = unit_price.replace('.', ',');
            }
        } else {
            unit_price = '0' + MdsConfig.thousandsSeparator + '00';
        }
        $("#earned_price_" + data_item_id).html(earned_price);
        $("#unit_price_" + data_item_id).html(unit_price);
    });

    $(document).on("click", ".btn_submit_quote", function () {
        $('.modal-title').text("<?= trans("submit_a_quote"); ?>");
    });
    $(document).on("click", ".btn_update_quote", function () {
        $('.modal-title').text("<?= trans("update_quote"); ?>");
    });
</script>


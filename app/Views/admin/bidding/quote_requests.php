<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?= $title; ?></h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" role="grid">
                        <div class="row table-filter-container">
                            <div class="col-sm-12">
                                <form action="<?= adminUrl('quote-requests') ?>" method="get">
                                    <div class="item-table-filter" style="width: 80px; min-width: 80px;">
                                        <label><?= trans("show"); ?></label>
                                        <select name="show" class="form-control">
                                            <option value="15" <?= inputGet('show') == '15' ? 'selected' : ''; ?>>15</option>
                                            <option value="30" <?= inputGet('show') == '30' ? 'selected' : ''; ?>>30</option>
                                            <option value="60" <?= inputGet('show') == '60' ? 'selected' : ''; ?>>60</option>
                                            <option value="100" <?= inputGet('show') == '100' ? 'selected' : ''; ?>>100</option>
                                        </select>
                                    </div>
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
                                        <button type="submit" class="btn bg-purple"><?= trans("filter"); ?></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <thead>
                        <tr role="row">
                            <th><?= trans('quote'); ?></th>
                            <th><?= trans('product'); ?></th>
                            <th><?= trans('seller'); ?></th>
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
                                    <td class="td-product">
                                        <?php $product = getProduct($item->product_id);
                                        if (!empty($product)):?>
                                            <div class="img-table">
                                                <a href="<?= generateProductUrl($product); ?>" target="_blank">
                                                    <img src="<?= getProductMainImage($product->id, 'image_small'); ?>" data-src="" alt="" class="lazyload img-responsive post-image"/>
                                                </a>
                                            </div>
                                            <a href="<?= generateProductUrl($product); ?>" target="_blank" class="table-product-title">
                                                <?= esc($item->product_title); ?>
                                            </a><br>
                                            <?= trans("quantity") . ': ' . $item->product_quantity; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php $user = getUser($item->seller_id);
                                        if (!empty($user)):?>
                                            <a href="<?= generateProfileUrl($user->slug); ?>" target="_blank" class="link-black">
                                                <strong class="font-600"><?= esc(getUsername($user)); ?></strong>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php $user = getUser($item->buyer_id);
                                        if (!empty($user)):?>
                                            <a href="<?= generateProfileUrl($user->slug); ?>" target="_blank" class="link-black">
                                                <strong class="font-600"><?= esc(getUsername($user)); ?></strong>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= trans($item->status); ?></td>
                                    <td>
                                        <?php if ($item->status != 'new_quote_request' && $item->price_offered != 0): ?>
                                            <div class="table-seller-bid">
                                                <p><strong><?= priceFormatted($item->price_offered, $item->price_currency); ?></strong></p>
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
                                                <li><a href="javascript:void(0)" onclick="deleteItem('ProductController/deleteQuoteRequestPost','<?= $item->id; ?>','<?= trans("confirm_delete", true); ?>');"><i class="fa fa-trash option-icon"></i><?= trans('delete'); ?></a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach;
                        endif; ?>
                        </tbody>
                    </table>
                    <?php if (empty($quoteRequests)): ?>
                        <p class="text-center">
                            <?= trans("no_records_found"); ?>
                        </p>
                    <?php endif; ?>
                    <div class="col-sm-12 table-ft">
                        <div class="row">
                            <div class="pull-right">
                                <?= view('partials/_pagination'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
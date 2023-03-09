<div class="row">
    <?php if (hasPermission('orders')): ?>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box admin-small-box bg-success">
                <div class="inner">
                    <h3 class="increase-count"><?= $orderCount; ?></h3>
                    <a href="<?= adminUrl('orders'); ?>"><p><?= trans("orders"); ?></p></a>
                </div>
                <div class="icon">
                    <a href="<?= adminUrl('orders'); ?>"><i class="fa fa-shopping-cart"></i></a>
                </div>
            </div>
        </div>
    <?php endif;
    if (hasPermission('products')): ?>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box admin-small-box bg-purple">
                <div class="inner">
                    <h3 class="increase-count"><?= $productCount; ?></h3>
                    <a href="<?= adminUrl('products'); ?>"><p><?= trans("products"); ?></p></a>
                </div>
                <div class="icon">
                    <a href="<?= adminUrl('products'); ?>"><i class="fa fa-shopping-basket"></i></a>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box admin-small-box bg-danger">
                <div class="inner">
                    <h3 class="increase-count"><?= $pendingProductCount; ?></h3>
                    <a href="<?= adminUrl('products'); ?>?list=pending">
                        <p><?= trans("pending_products"); ?></p>
                    </a>
                </div>
                <div class="icon">
                    <a href="<?= adminUrl('products'); ?>?list=pending"><i class="fa fa-low-vision"></i></a>
                </div>
            </div>
        </div>
    <?php endif;
    if (hasPermission('membership')): ?>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box admin-small-box bg-warning">
                <div class="inner">
                    <h3 class="increase-count"><?= $membersCount; ?></h3>
                    <a href="<?= adminUrl('users'); ?>"><p><?= trans("members"); ?></p></a>
                </div>
                <div class="icon">
                    <a href="<?= adminUrl('users'); ?>"><i class="fa fa-users"></i></a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php if (hasPermission('orders')): ?>
    <div class="row">
        <div class="col-lg-6 col-sm-12 col-xs-12">
            <div class="box box-primary box-sm">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= trans("latest_orders"); ?></h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <div class="box-body index-table">
                    <div class="table-responsive">
                        <table class="table no-margin">
                            <thead>
                            <tr>
                                <th><?= trans("order"); ?></th>
                                <th><?= trans("total"); ?></th>
                                <th><?= trans("status"); ?></th>
                                <th><?= trans("date"); ?></th>
                                <th><?= trans("details"); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($latestOrders)):
                                foreach ($latestOrders as $item): ?>
                                    <tr>
                                        <td>#<?= $item->order_number; ?></td>
                                        <td><?= priceFormatted($item->price_total, $item->price_currency); ?></td>
                                        <td>
                                            <?php if ($item->status == 1):
                                                echo trans("completed");
                                            elseif ($item->status == 2):
                                                echo trans("cancelled");
                                            else:
                                                echo trans("order_processing");
                                            endif; ?>
                                        </td>
                                        <td><?= formatDate($item->created_at); ?></td>
                                        <td style="width: 10%">
                                            <a href="<?= adminUrl('order-details') . '/' . esc($item->id); ?>" class="btn btn-xs btn-info"><?= trans('details'); ?></a>
                                        </td>
                                    </tr>
                                <?php endforeach;
                            endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="box-footer clearfix">
                    <a href="<?= adminUrl('orders'); ?>" class="btn btn-sm btn-default pull-right"><?= trans("view_all"); ?></a>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-sm-12 col-xs-12">
            <div class="box box-primary box-sm">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= trans("latest_transactions"); ?></h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <div class="box-body index-table">
                    <div class="table-responsive">
                        <table class="table no-margin">
                            <thead>
                            <tr>
                                <th><?= trans("id"); ?></th>
                                <th><?= trans("order"); ?></th>
                                <th><?= trans("payment_amount"); ?></th>
                                <th><?= trans('payment_method'); ?></th>
                                <th><?= trans('status'); ?></th>
                                <th><?= trans("date"); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($latestTransactions)):
                                foreach ($latestTransactions as $item): ?>
                                    <tr>
                                        <td style="width: 10%"><?= esc($item->id); ?></td>
                                        <td style="white-space: nowrap">#<?= $item->order_id + 10000; ?></td>
                                        <td><?= priceCurrencyFormat($item->payment_amount, $item->currency); ?></td>
                                        <td><?= getPaymentMethod($item->payment_method); ?></td>
                                        <td><?= trans($item->payment_status); ?></td>
                                        <td><?= formatDate($item->created_at); ?></td>
                                    </tr>
                                <?php endforeach;
                            endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="box-footer clearfix">
                    <a href="<?= adminUrl('transactions'); ?>" class="btn btn-sm btn-default pull-right"><?= trans("view_all"); ?></a>
                </div>
            </div>
        </div>
    </div>
<?php endif;
if (hasPermission('products')): ?>
    <div class="row">
        <div class="col-lg-6 col-sm-12 col-xs-12">
            <div class="box box-primary box-sm">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= trans("latest_products"); ?></h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <div class="box-body index-table">
                    <div class="table-responsive">
                        <table class="table no-margin">
                            <thead>
                            <tr>
                                <th><?= trans("id"); ?></th>
                                <th><?= trans("name"); ?></th>
                                <th><?= trans("details"); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($latestProducts)):
                                foreach ($latestProducts as $item): ?>
                                    <tr>
                                        <td style="width: 10%"><?= esc($item->id); ?></td>
                                        <td class="td-product-small">
                                            <div class="img-table">
                                                <a href="<?= generateProductUrl($item); ?>" target="_blank">
                                                    <img src="<?= getProductMainImage($item->id, 'image_small'); ?>" data-src="" alt="" class="lazyload img-responsive post-image"/>
                                                </a>
                                            </div>
                                            <a href="<?= generateProductUrl($item); ?>" target="_blank" class="table-product-title"><?= getProductTitle($item); ?></a>
                                            <br>
                                            <div class="table-sm-meta"><?= timeAgo($item->created_at); ?></div>
                                        </td>
                                        <td style="width: 10%">
                                            <a href="<?= adminUrl('product-details') . '/' . esc($item->id); ?>" class="btn btn-xs btn-info"><?= trans('details'); ?></a>
                                        </td>
                                    </tr>
                                <?php endforeach;
                            endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="box-footer clearfix">
                    <a href="<?= adminUrl('products'); ?>" class="btn btn-sm btn-default pull-right"><?= trans("view_all"); ?></a>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-sm-12 col-xs-12">
            <div class="box box-primary box-sm">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= trans("latest_pending_products"); ?></h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <div class="box-body index-table">
                    <div class="table-responsive">
                        <table class="table no-margin">
                            <thead>
                            <tr>
                                <th><?= trans("id"); ?></th>
                                <th><?= trans("name"); ?></th>
                                <th><?= trans("details"); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($latestPendingProducts)):
                                foreach ($latestPendingProducts as $item): ?>
                                    <tr>
                                        <td style="width: 10%"><?= esc($item->id); ?></td>
                                        <td class="td-product-small">
                                            <div class="img-table">
                                                <a href="<?= generateProductUrl($item); ?>" target="_blank">
                                                    <img src="<?= getProductMainImage($item->id, 'image_small'); ?>" data-src="" alt="" class="lazyload img-responsive post-image"/>
                                                </a>
                                            </div>
                                            <a href="<?= generateProductUrl($item); ?>" target="_blank" class="table-product-title"><?= getProductTitle($item); ?></a>
                                            <br>
                                            <div class="table-sm-meta">
                                                <?= timeAgo($item->created_at); ?>
                                            </div>
                                        </td>
                                        <td style="width: 10%;vertical-align: center !important;">
                                            <a href="<?= adminUrl('product-details') . '/' . esc($item->id); ?>" class="btn btn-xs btn-info"><?= trans('details'); ?></a>
                                        </td>
                                    </tr>
                                <?php endforeach;
                            endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="box-footer clearfix">
                    <a href="<?= adminUrl('products'); ?>?list=pending" class="btn btn-sm btn-default pull-right"><?= trans("view_all"); ?></a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<div class="row">
    <?php if (hasPermission('products')): ?>
        <div class="col-lg-6 col-sm-12 col-xs-12">
            <div class="box box-primary box-sm">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= trans("latest_transactions"); ?>&nbsp;<small style="font-size: 13px;">(<?= trans("featured_products"); ?>)</small>
                    </h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <div class="box-body index-table">
                    <div class="table-responsive">
                        <table class="table no-margin">
                            <thead>
                            <tr>
                                <th><?= trans("id"); ?></th>
                                <th><?= trans('payment_method'); ?></th>
                                <th><?= trans("payment_amount"); ?></th>
                                <th><?= trans('status'); ?></th>
                                <th><?= trans("date"); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($latestPromotedTransactions)):
                                foreach ($latestPromotedTransactions as $item): ?>
                                    <tr>
                                        <td style="width: 10%"><?= esc($item->id); ?></td>
                                        <td><?= getPaymentMethod($item->payment_method); ?></td>
                                        <td><?= priceCurrencyFormat($item->payment_amount, $item->currency); ?></td>
                                        <td><?= trans($item->payment_status); ?></td>
                                        <td><?= formatDate($item->created_at); ?></td>
                                    </tr>
                                <?php endforeach;
                            endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="box-footer clearfix">
                    <a href="<?= adminUrl('featured-products-transactions'); ?>" class="btn btn-sm btn-default pull-right"><?= trans("view_all"); ?></a>
                </div>
            </div>
        </div>
    <?php endif;
    if (hasPermission('reviews')): ?>
        <div class="col-lg-6 col-sm-12 col-xs-12">
            <div class="box box-primary box-sm">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= trans("latest_reviews"); ?></h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <div class="box-body index-table">
                    <div class="table-responsive">
                        <table class="table no-margin">
                            <thead>
                            <tr>
                                <th><?= trans("id"); ?></th>
                                <th><?= trans("username"); ?></th>
                                <th style="width: 60%"><?= trans("review"); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($latestReviews)):
                                foreach ($latestReviews as $item): ?>
                                    <tr>
                                        <td style="width: 10%"><?= esc($item->id); ?></td>
                                        <td style="width: 25%" class="break-word"><?= esc($item->user_username); ?></td>
                                        <td style="width: 65%" class="break-word">
                                            <div><?= view('admin/includes/_review_stars', ['review' => $item->rating]); ?></div>
                                            <?= characterLimiter($item->review, 100); ?>
                                            <div class="table-sm-meta"><?= timeAgo($item->created_at); ?></div>
                                        </td>
                                    </tr>
                                <?php endforeach;
                            endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="box-footer clearfix">
                    <a href="<?= adminUrl('reviews'); ?>" class="btn btn-sm btn-default pull-right"><?= trans("view_all"); ?></a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="row">
    <?php if (hasPermission('reviews')): ?>
        <div class="col-lg-6 col-sm-12 col-xs-12">
            <div class="box box-primary box-sm">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= trans("latest_comments"); ?></h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <div class="box-body index-table">
                    <div class="table-responsive">
                        <table class="table no-margin">
                            <thead>
                            <tr>
                                <th><?= trans("id"); ?></th>
                                <th><?= trans("user"); ?></th>
                                <th style="width: 60%"><?= trans("comment"); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($latestComments)):
                                foreach ($latestComments as $item): ?>
                                    <tr>
                                        <td style="width: 10%"><?= esc($item->id); ?></td>
                                        <td style="width: 25%" class="break-word"><?= esc($item->name); ?></td>
                                        <td style="width: 65%" class="break-word">
                                            <?= characterLimiter($item->comment, 100); ?>
                                            <div class="table-sm-meta"><?= timeAgo($item->created_at); ?></div>
                                        </td>
                                    </tr>
                                <?php endforeach;
                            endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="box-footer clearfix">
                    <a href="<?= adminUrl('product-comments'); ?>" class="btn btn-sm btn-default pull-right"><?= trans("view_all"); ?></a>
                </div>
            </div>
        </div>
    <?php endif;
    if (hasPermission('membership')): ?>
        <div class="no-padding margin-bottom-20">
            <div class="col-lg-6 col-sm-12 col-xs-12">
                <div class="box box-primary box-sm">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?= trans("latest_members"); ?></h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <ul class="users-list clearfix">
                            <?php if (!empty($latestMembers)):
                                foreach ($latestMembers as $item):?>
                                    <li>
                                        <a href="<?= generateProfileUrl($item->slug); ?>">
                                            <img src="<?= getUserAvatar($item); ?>" alt="user" class="img-responsive">
                                        </a>
                                        <a href="<?= generateProfileUrl($item->slug); ?>" class="users-list-name"><?= esc(getUsername($item)); ?></a>
                                        <span class="users-list-date"><?= timeAgo($item->created_at); ?></span>
                                    </li>
                                <?php endforeach;
                            endif; ?>
                        </ul>
                    </div>
                    <div class="box-footer text-center">
                        <a href="<?= adminUrl('users'); ?>" class="btn btn-sm btn-default btn-flat pull-right"><?= trans("view_all"); ?></a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
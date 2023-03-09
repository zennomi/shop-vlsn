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
                                <form action="<?= adminUrl('featured-products-transactions'); ?>" method="get">
                                    <div class="item-table-filter" style="width: 80px; min-width: 80px;">
                                        <label><?= trans("show"); ?></label>
                                        <select name="show" class="form-control">
                                            <option value="15" <?= inputGet('show', true) == '15' ? 'selected' : ''; ?>>15</option>
                                            <option value="30" <?= inputGet('show', true) == '30' ? 'selected' : ''; ?>>30</option>
                                            <option value="60" <?= inputGet('show', true) == '60' ? 'selected' : ''; ?>>60</option>
                                            <option value="100" <?= inputGet('show', true) == '100' ? 'selected' : ''; ?>>100</option>
                                        </select>
                                    </div>
                                    <div class="item-table-filter">
                                        <label><?= trans("search"); ?></label>
                                        <input name="q" class="form-control" placeholder="<?= trans("payment_id"); ?>" type="search" value="<?= esc(inputGet('q')); ?>">
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
                            <th><?= trans('id'); ?></th>
                            <th><?= trans('payment_method'); ?></th>
                            <th><?= trans('payment_id'); ?></th>
                            <th><?= trans('user'); ?></th>
                            <th><?= trans('product_id'); ?></th>
                            <th><?= trans('currency'); ?></th>
                            <th><?= trans('payment_amount'); ?></th>
                            <th><?= trans('payment_status'); ?></th>
                            <th><?= trans('purchased_plan'); ?></th>
                            <th><?= trans('ip_address'); ?></th>
                            <th><?= trans('date'); ?></th>
                            <th class="max-width-120"><?= trans('options'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($transactions)):
                            foreach ($transactions as $item): ?>
                                <tr>
                                    <td><?= $item->id; ?></td>
                                    <td><?= getPaymentMethod($item->payment_method); ?></td>
                                    <td><?= esc($item->payment_id); ?></td>
                                    <td>
                                        <?php $user = getUser($item->user_id);
                                        if (!empty($user)):?>
                                            <div class="table-orders-user">
                                                <a href="<?= generateProfileUrl($user->slug); ?>" target="_blank" class="table-link">
                                                    <?= esc(getUsername($user)); ?>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $item->product_id; ?></td>
                                    <td><?= $item->currency; ?></td>
                                    <td><?= $item->payment_amount; ?></td>
                                    <td>
                                        <?php if ($item->payment_status == "awaiting_payment"):
                                            echo trans("awaiting_payment");
                                        else:
                                            echo $item->payment_status;
                                        endif; ?>
                                    </td>
                                    <td><?= $item->purchased_plan; ?></td>
                                    <td><?= $item->ip_address; ?></td>
                                    <td><?= formatDate($item->created_at); ?></td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn bg-purple dropdown-toggle btn-select-option" type="button" data-toggle="dropdown"><?= trans('select_option'); ?>
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu options-dropdown">
                                                <?php $product = getProduct($item->product_id);
                                                if (!empty($product)):
                                                    if ($product->is_promoted != 1): ?>
                                                        <li>
                                                            <a href="javascript:void(0)" onclick="$('#day_count_product_id').val('<?= $item->product_id; ?>');$('#input_transaction_id').val('<?= $item->id; ?>');" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus option-icon"></i><?= trans('add_to_featured'); ?></a>
                                                        </li>
                                                    <?php endif;
                                                endif;
                                                ?>
                                                <li>
                                                    <a href="javascript:void(0)" onclick="deleteItem('ProductController/deleteFeaturedTransactionPost','<?= $item->id; ?>','<?= trans("confirm_delete", true); ?>');"><i class="fa fa-trash option-icon"></i><?= trans('delete'); ?></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach;
                        endif; ?>
                        </tbody>
                    </table>
                    <?php if (empty($transactions)): ?>
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

<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('ProductController/addRemoveFeaturedProduct'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?= trans('add_to_featured'); ?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><?= trans('day_count'); ?></label>
                        <input type="hidden" class="form-control" name="product_id" id="day_count_product_id" value="">
                        <input type="hidden" class="form-control" name="transaction_id" id="input_transaction_id" value="">
                        <input type="hidden" class="form-control" name="is_ajax" value="0">
                        <input type="number" class="form-control" name="day_count" placeholder="<?= trans('day_count'); ?>" value="1" min="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success"><?= trans("submit"); ?></button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><?= trans("close"); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?= $title; ?></h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <div class="row table-filter-container">
                        <div class="col-sm-12">
                            <form action="<?= adminUrl('transactions'); ?>" method="get">
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
                                    <label><?= trans("search"); ?></label>
                                    <input name="q" class="form-control" placeholder="<?= trans("order_number"); ?>" type="search" value="<?= esc(inputGet('q')); ?>">
                                </div>
                                <div class="item-table-filter md-top-10" style="width: 65px; min-width: 65px;">
                                    <label style="display: block">&nbsp;</label>
                                    <button type="submit" class="btn bg-purple"><?= trans("filter"); ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <table class="table table-bordered table-striped" role="grid">
                        <thead>
                        <tr role="row">
                            <th><?= trans('id'); ?></th>
                            <th><?= trans('order'); ?></th>
                            <th><?= trans('payment_method'); ?></th>
                            <th><?= trans('payment_id'); ?></th>
                            <th><?= trans('user'); ?></th>
                            <th><?= trans('currency'); ?></th>
                            <th><?= trans('payment_amount'); ?></th>
                            <th><?= trans('payment_status'); ?></th>
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
                                    <td class="order-number-table">
                                        #<?php
                                        $order = getOrder($item->order_id);
                                        if (!empty($order)):
                                            echo $order->order_number;
                                        endif; ?>
                                    </td>
                                    <td><?= getPaymentMethod($item->payment_method); ?></td>
                                    <td><?= $item->payment_id; ?></td>
                                    <td>
                                        <?php if ($item->user_id == 0): ?>
                                            <label class="label bg-olive"><?= trans("guest"); ?></label>
                                        <?php else:
                                            $user = getUser($item->user_id);
                                            if (!empty($user)):?>
                                                <div class="table-orders-user">
                                                    <a href="<?= generateProfileUrl($user->slug); ?>" target="_blank" class="table-link"><?= esc(getUsername($user)); ?></a>
                                                </div>
                                            <?php endif;
                                        endif; ?>
                                    </td>
                                    <td><?= $item->currency; ?></td>
                                    <td><?= $item->payment_amount; ?></td>
                                    <td><?= getPaymentStatus($item->payment_status); ?></td>
                                    <td><?= $item->ip_address; ?></td>
                                    <td><?= formatDate($item->created_at); ?></td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn bg-purple dropdown-toggle btn-select-option" type="button" data-toggle="dropdown"><?= trans('select_option'); ?>
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu options-dropdown">
                                                <li>
                                                    <a href="javascript:void(0)" onclick="deleteItem('OrderAdminController/deleteTransactionPost','<?= $item->id; ?>','<?= trans("confirm_delete", true); ?>');"><i class="fa fa-trash option-icon"></i><?= trans('delete'); ?></a>
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
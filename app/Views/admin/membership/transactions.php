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
                            <form action="<?= adminUrl('transactions-membership'); ?>" method="get">
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
                                    <input name="q" class="form-control" placeholder="<?= trans("search"); ?>" type="search" value="<?= esc(inputGet('q')); ?>">
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
                            <th><?= trans("id"); ?></th>
                            <th><?= trans("user"); ?></th>
                            <th><?= trans("membership_plan"); ?></th>
                            <th><?= trans("payment_method"); ?></th>
                            <th><?= trans("payment_id"); ?></th>
                            <th><?= trans("payment_amount"); ?></th>
                            <th><?= trans("payment_status"); ?></th>
                            <th><?= trans("ip_address"); ?></th>
                            <th><?= trans("date"); ?></th>
                            <th class="max-width-120"><?= trans("options"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($transactions)):
                            foreach ($transactions as $item): ?>
                                <tr>
                                    <td><?= $item->id; ?></td>
                                    <td><?php $user = getUser($item->user_id);
                                        if (!empty($user)):?>
                                            <div class="tbl-table">
                                                <div class="left">
                                                    <a href="<?= generateProfileUrl($user->slug); ?>" target="_blank">
                                                        <img src="<?= getUserAvatar($user); ?>" alt="buyer" class="img-responsive">
                                                    </a>
                                                </div>
                                                <div class="right">
                                                    <a href="<?= generateProfileUrl($user->slug); ?>" target="_blank" class="table-link"><?= esc(getUsername($user)); ?></a>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($item->plan_title); ?></td>
                                    <td><?= getPaymentMethod($item->payment_method); ?></td>
                                    <td><?= $item->payment_id; ?></td>
                                    <td><?= $item->payment_amount; ?>&nbsp;(<?= $item->currency; ?>)</td>
                                    <td>
                                        <?= getPaymentStatus($item->payment_status); ?>
                                        <?php if ($item->payment_status == "awaiting_payment"): ?>
                                            <form action="<?= base_url('MembershipController/approvePaymentPost'); ?>" method="post">
                                                <?= csrf_field(); ?>
                                                <input type="hidden" name="id" value="<?= $item->id; ?>">
                                                <button type="submit" class="btn btn-sm btn-success m-t-5"><i class="fa fa-check"></i>&nbsp;<?= trans("approve"); ?></button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $item->ip_address; ?></td>
                                    <td><?= formatDate($item->created_at); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-option">
                                            <a href="<?= base_url('invoice-membership/' . $item->id); ?>" class="btn btn-sm btn-default btn-edit" target="_blank"><i class="fa fa-file-text"></i>&nbsp;&nbsp;<?= trans("view_invoice"); ?></a>
                                            <a href="javascript:void(0)" class="btn btn-sm btn-default btn-delete" onclick="deleteItem('MembershipController/deleteTransactionPost','<?= $item->id; ?>','<?= trans("confirm_delete"); ?>');"><i class="fa fa-trash-o"></i></a>
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
                </div>
            </div>
            <div class="col-sm-12">
                <?php if (!empty($transactions)): ?>
                    <div class="number-of-entries">
                        <span><?= trans("number_of_entries"); ?>:</span>&nbsp;&nbsp;<strong><?= $numRows; ?></strong>
                    </div>
                <?php endif; ?>
                <div class="pull-right">
                    <?= view('partials/_pagination'); ?>
                </div>
            </div>
        </div>
    </div>
</div>
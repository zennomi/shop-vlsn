<div class="box">
    <div class="box-header with-border">
        <div class="left">
            <h3 class="box-title"><?= trans('payout_requests'); ?></h3>
        </div>
        <div class="right">
            <a href="<?= adminUrl('add-payout'); ?>" class="btn btn-success btn-add-new">
                <i class="fa fa-plus"></i>&nbsp;&nbsp;<?= trans('add_payout'); ?>
            </a>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" role="grid">
                        <div class="row table-filter-container">
                            <div class="col-sm-12">
                                <form action="<?= adminUrl('payout-requests'); ?>" method="get">
                                    <div class="item-table-filter" style="width: 80px; min-width: 80px;">
                                        <label><?= trans("show"); ?></label>
                                        <select name="show" class="form-control">
                                            <option value="15" <?= inputGet('show') == '15' ? 'selected' : ''; ?>>15</option>
                                            <option value="30" <?= inputGet('show') == '30' ? 'selected' : ''; ?>>30</option>
                                            <option value="60" <?= inputGet('show') == '60' ? 'selected' : ''; ?>>60</option>
                                            <option value="100" <?= inputGet('show') == '100' ? 'selected' : ''; ?>>100</option>
                                        </select>
                                    </div>
                                    <div class="item-table-filter" style="min-width: 150px;">
                                        <label><?= trans("status"); ?></label>
                                        <select name="status" class="form-control">
                                            <option value="all" <?= inputGet('status') == 'all' ? 'selected' : ''; ?>><?= trans("all"); ?></option>
                                            <option value="pending" <?= inputGet('status') == 'pending' ? 'selected' : ''; ?>><?= trans("pending"); ?></option>
                                            <option value="completed" <?= inputGet('status') == 'completed' ? 'selected' : ''; ?>><?= trans("completed"); ?></option>
                                        </select>
                                    </div>
                                    <div class="item-table-filter">
                                        <label><?= trans("search"); ?></label>
                                        <input name="q" class="form-control" placeholder="<?= trans("user_id"); ?>" type="search" value="<?= esc(inputget('q')); ?>">
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
                            <th><?= trans('user'); ?></th>
                            <th><?= trans('withdraw_method'); ?></th>
                            <th><?= trans('withdraw_amount'); ?></th>
                            <th><?= trans('status'); ?></th>
                            <th><?= trans('date'); ?></th>
                            <th class="max-width-120"><?= trans('options'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($payoutRequests)):
                            foreach ($payoutRequests as $item): ?>
                                <tr>
                                    <td><?= $item->id; ?></td>
                                    <td>
                                        <?php $user = getUser($item->user_id);
                                        if (!empty($user)):?>
                                            <div class="tbl-table">
                                                <div class="left">
                                                    <a href="<?= generateProfileUrl($user->slug); ?>" target="_blank" class="table-link">
                                                        <img src="<?= getUserAvatar($user); ?>" alt="user" class="img-responsive">
                                                    </a>
                                                </div>
                                                <div class="right">
                                                    <div class="m-b-5">
                                                        <a href="<?= generateProfileUrl($user->slug); ?>" target="_blank" class="table-link"><?= trans("user_id") . ': ' . esc($user->id); ?></a>
                                                    </div>
                                                    <div class="m-b-5">
                                                        <a href="<?= generateProfileUrl($user->slug); ?>" target="_blank" class="table-link"><?= esc($user->first_name) . ' ' . esc($user->last_name); ?>&nbsp;<?= !empty($user->username) ? '('.$user->username.')' : '';?></a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= trans($item->payout_method); ?>
                                        <p class="m-0">
                                            <button type="button" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#accountDetailsModel_<?= $item->id; ?>"><?= trans("see_details"); ?></button>
                                        </p>
                                    </td>
                                    <td><?= priceFormatted($item->amount, $item->currency); ?></td>
                                    <td>
                                        <?php if ($item->status == 1): ?>
                                            <label class="label label-success"><?= trans('completed'); ?></label>
                                        <?php else: ?>
                                            <label class="label label-warning"><?= trans('pending'); ?></label>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= formatDate($item->created_at); ?></td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn bg-purple dropdown-toggle btn-select-option" type="button" data-toggle="dropdown"><?= trans('select_option'); ?>
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu options-dropdown">
                                                <?php if ($item->status != 1): ?>
                                                    <li>
                                                        <form action="<?= base_url('EarningsController/completePayoutRequestPost'); ?>" method="post">
                                                            <?= csrf_field(); ?>
                                                            <input type="hidden" name="payout_id" value="<?= $item->id; ?>">
                                                            <input type="hidden" name="user_id" value="<?= $item->user_id; ?>">
                                                            <input type="hidden" name="amount" value="<?= $item->amount; ?>">
                                                            <button type="submit" name="option" value="completed" class="btn-list-button">
                                                                <i class="fa fa-check option-icon"></i><?= trans('completed'); ?>
                                                            </button>
                                                        </form>
                                                    </li>
                                                <?php endif; ?>
                                                <li>
                                                    <a href="javascript:void(0)" onclick="deleteItem('EarningsController/deletePayoutPost','<?= $item->id; ?>','<?= trans("confirm_delete", true); ?>');"><i class="fa fa-trash option-icon"></i><?= trans('delete'); ?></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach;
                        endif; ?>
                        </tbody>
                    </table>
                    <?php if (empty($payoutRequests)): ?>
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

<?php if (!empty($payoutRequests)):
    foreach ($payoutRequests as $item):
        $payout = getUserPayoutAccount($item->user_id); ?>
        <div id="accountDetailsModel_<?= $item->id; ?>" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><?= trans($item->payout_method); ?></h4>
                    </div>
                    <div class="modal-body">
                        <?php if (!empty($payout)): ?>
                            <?php if ($item->payout_method == 'paypal'): ?>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <?= trans("user"); ?>
                                    </div>
                                    <div class="col-sm-8">
                                        <?php $user = getUser($payout->user_id);
                                        if (!empty($user)):?>
                                            <strong>&nbsp;<?= esc(getUsername($user)); ?></strong>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <?= trans("paypal_email_address"); ?>
                                    </div>
                                    <div class="col-sm-8">
                                        <strong>&nbsp;<?= esc($payout->payout_paypal_email); ?></strong>
                                    </div>
                                </div>
                            <?php elseif ($item->payout_method == 'bitcoin'): ?>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <?= trans("user"); ?>
                                    </div>
                                    <div class="col-sm-8">
                                        <?php $user = getUser($payout->user_id);
                                        if (!empty($user)):?>
                                            <strong>&nbsp;<?= esc(getUsername($user)); ?></strong>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <?= trans("btc_address"); ?>
                                    </div>
                                    <div class="col-sm-8">
                                        <strong>&nbsp;<?= esc($payout->payout_bitcoin_address); ?></strong>
                                    </div>
                                </div>
                            <?php elseif ($item->payout_method == 'iban'): ?>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <?= trans("user"); ?>
                                    </div>
                                    <div class="col-sm-8">
                                        <?php $user = getUser($payout->user_id);
                                        if (!empty($user)):?>
                                            <strong>&nbsp;<?= esc(getUsername($user)); ?></strong>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <?= trans("full_name"); ?>
                                    </div>
                                    <div class="col-sm-8">
                                        <strong>&nbsp;<?= esc($payout->iban_full_name); ?></strong>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <?= trans("country"); ?>
                                    </div>
                                    <div class="col-sm-8">
                                        <?php $country = getCountry($payout->iban_country_id);
                                        if (!empty($country)):?>
                                            <strong>&nbsp;<?= esc($country->name); ?></strong>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <?= trans("bank_name"); ?>
                                    </div>
                                    <div class="col-sm-8">
                                        <strong>&nbsp;<?= esc($payout->iban_bank_name); ?></strong>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <?= trans("iban"); ?>
                                    </div>
                                    <div class="col-sm-8">
                                        <strong>&nbsp;<?= esc($payout->iban_number); ?></strong>
                                    </div>
                                </div>
                            <?php elseif ($item->payout_method == 'swift'): ?>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <?= trans("user"); ?>
                                    </div>
                                    <div class="col-sm-8">
                                        <?php $user = getUser($payout->user_id);
                                        if (!empty($user)):?>
                                            <strong>&nbsp;<?= esc(getUsername($user)); ?></strong>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <?= trans("full_name"); ?>
                                    </div>
                                    <div class="col-sm-8">
                                        <strong>&nbsp;<?= esc($payout->swift_full_name); ?></strong>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <?= trans("address"); ?>
                                    </div>
                                    <div class="col-sm-8">
                                        <strong>&nbsp;<?= esc($payout->swift_address); ?></strong>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <?= trans("state"); ?>
                                    </div>
                                    <div class="col-sm-8">
                                        <strong>&nbsp;<?= esc($payout->swift_state); ?></strong>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <?= trans("city"); ?>
                                    </div>
                                    <div class="col-sm-8">
                                        <strong>&nbsp;<?= esc($payout->swift_city); ?></strong>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <?= trans("postcode"); ?>
                                    </div>
                                    <div class="col-sm-8">
                                        <strong>&nbsp;<?= esc($payout->swift_postcode); ?></strong>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <?= trans("country"); ?>
                                    </div>
                                    <div class="col-sm-8">
                                        <?php $branchCountry = getCountry($payout->swift_country_id);
                                        if (!empty($branchCountry)):?>
                                            <strong>&nbsp;<?= esc($branchCountry->name); ?></strong>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <?= trans("bank_account_holder_name"); ?>
                                    </div>
                                    <div class="col-sm-8">
                                        <strong>&nbsp;<?= esc($payout->swift_bank_account_holder_name); ?></strong>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <?= trans("iban"); ?>
                                    </div>
                                    <div class="col-sm-8">
                                        <strong>&nbsp;<?= esc($payout->swift_iban); ?></strong>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <?= trans("swift_code"); ?>
                                    </div>
                                    <div class="col-sm-8">
                                        <strong>&nbsp;<?= esc($payout->swift_code); ?></strong>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <?= trans("bank_name"); ?>
                                    </div>
                                    <div class="col-sm-8">
                                        <strong>&nbsp;<?= esc($payout->swift_bank_name); ?></strong>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <?= trans("bank_branch_city"); ?>
                                    </div>
                                    <div class="col-sm-8">
                                        <strong>&nbsp;<?= esc($payout->swift_bank_branch_city); ?></strong>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <?= trans("bank_branch_country"); ?>
                                    </div>
                                    <div class="col-sm-8">
                                        <?php $branchCountry = getCountry($payout->swift_bank_branch_country_id);
                                        if (!empty($branchCountry)):?>
                                            <strong>&nbsp;<?= esc($branchCountry->name); ?></strong>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif;
                        endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?= trans("close"); ?></button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach;
endif; ?>
<style>
    .modal-body .row {
        margin-bottom: 8px;
    }
</style>

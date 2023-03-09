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
                                <form action="<?= adminUrl('seller-balances'); ?>" method="get">
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
                                        <input name="q" class="form-control" placeholder="<?= trans("username"); ?>" type="search" value="<?= esc(inputGet('q')); ?>">
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
                            <th><?= trans('user_id'); ?></th>
                            <th><?= trans('user'); ?></th>
                            <th><?= trans('number_of_total_sales'); ?></th>
                            <th><?= trans('balance'); ?></th>
                            <th class="th-options"><?= trans('options'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($balances)):
                            foreach ($balances as $item): ?>
                                <tr>
                                    <td style="width: 100px;"><?= $item->id; ?></td>
                                    <td>
                                        <?php if (!empty($item)): ?>
                                            <div class="tbl-table">
                                                <div class="left">
                                                    <a href="<?= generateProfileUrl($item->slug); ?>" target="_blank" class="table-link">
                                                        <img src="<?= getUserAvatar($item); ?>" alt="user" class="img-responsive">
                                                    </a>
                                                </div>
                                                <div class="right">
                                                    <div class="m-b-5">
                                                        <a href="<?= generateProfileUrl($item->slug); ?>" target="_blank" class="table-link"><?= esc(getUsername($item)); ?></a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong class="font-600"><?= $item->number_of_sales; ?></strong></td>
                                    <td><strong class="font-600"><?= priceFormatted($item->balance, $paymentSettings->default_currency); ?></strong></td>
                                    <td style="width: 200px;">
                                        <div class="dropdown">
                                            <button class="btn bg-purple dropdown-toggle btn-select-option" type="button" data-toggle="dropdown"><?= trans('select_option'); ?>
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu options-dropdown">
                                                <li>
                                                    <a href="javascript:void(0)" data-toggle="modal" data-target="#modalBalance<?= $item->id; ?>"><i class="fa fa-edit option-icon"></i><?= trans('edit'); ?></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach;
                        endif; ?>
                        </tbody>
                    </table>
                    <?php if (empty($balances)): ?>
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

<?php if (!empty($balances)):
    foreach ($balances as $item): ?>
        <div id="modalBalance<?= $item->id; ?>" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="<?= base_url('EarningsController/editSellerBalancePost'); ?>" method="post">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="user_id" value="<?= $item->id; ?>">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title"><?= trans("update_seller_balance"); ?></h4>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label class="label-sitemap"><?= trans('user_id'); ?>:&nbsp;<?= esc($item->id); ?></label><br>
                                <label class="label-sitemap"><?= trans('username'); ?>:&nbsp;<?= esc(getUsername($item)); ?></label>
                            </div>
                            <div class="form-group">
                                <label class="label-sitemap"><?= trans('number_of_total_sales'); ?></label>
                                <input type="number" class="form-control" name="number_of_sales" value="<?= $item->number_of_sales; ?>" min="0" required>
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?= trans('balance'); ?></label>
                                <input type="text" name="balance" class="form-control form-input price-input" value="<?= getPrice($item->balance, 'input'); ?>" onpaste="return false;" maxlength="32" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success"><?= trans("submit"); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach;
endif; ?>
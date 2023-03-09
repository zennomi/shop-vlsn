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
                    <table class="table table-striped" role="grid">
                        <thead>
                        <tr role="row">
                            <th scope="col"><?= trans("withdraw_method"); ?></th>
                            <th scope="col"><?= trans("withdraw_amount"); ?></th>
                            <th scope="col"><?= trans("status"); ?></th>
                            <th scope="col"><?= trans("date"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($payouts)):
                            foreach ($payouts as $payout): ?>
                                <tr>
                                    <td><?= trans($payout->payout_method); ?></td>
                                    <td><?= priceFormatted($payout->amount, $payout->currency); ?></td>
                                    <td>
                                        <?php if ($payout->status == 1): ?>
                                            <label class="label label-success"><?= trans("completed"); ?></label>
                                        <?php else: ?>
                                            <label class="label label-warning"><?= trans("pending"); ?></label>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= formatDate($payout->created_at); ?></td>
                                </tr>
                            <?php endforeach;
                        endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if (empty($payouts)): ?>
                    <p class="text-center">
                        <?= trans("no_records_found"); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?php if (!empty($payouts)): ?>
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
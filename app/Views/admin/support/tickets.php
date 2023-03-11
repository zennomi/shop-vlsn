<div class="box">
    <div class="box-header with-border">
        <div class="left">
            <h3 class="box-title"><?= trans('support_tickets'); ?></h3>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12 m-b-30">
                <a href="<?= adminUrl('support-tickets?status=1'); ?>" class="btn bnt-support-status<?= $status == 1 ? ' btn-success' : ' btn-outline-success'; ?> m-r-5 font-600">(<?= $numRowsOpen; ?>)&nbsp;<?= trans("open"); ?></a>
                <a href="<?= adminUrl('support-tickets?status=2'); ?>" class="btn bnt-support-status<?= $status == 2 ? ' btn-warning' : ' btn-outline-warning'; ?> m-r-5 font-600">(<?= $numRowsResponded; ?>)&nbsp;<?= trans("responded"); ?></a>
                <a href="<?= adminUrl('support-tickets?status=3'); ?>" class="btn bnt-support-status<?= $status == 3 ? ' btn-secondary' : ' btn-outline-secondary'; ?> font-600">(<?= $numRowsClosed; ?>)&nbsp;<?= trans("closed"); ?></a>
            </div>
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped cs_datatable_lang" role="grid">
                        <thead>
                        <tr role="row">
                            <th width="20"><?= trans("id") ?></th>
                            <th><?= trans("subject") ?></th>
                            <th><?= trans("user") ?></th>
                            <th><?= trans("status") ?></th>
                            <th><?= trans("date") ?></th>
                            <th><?= trans("updated") ?></th>
                            <th class="th-options"><?= trans('options'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($tickets)):
                            foreach ($tickets as $ticket):?>
                                <tr>
                                    <td>#<?= $ticket->id; ?></td>
                                    <td style="max-width: 400px;"><?= esc($ticket->subject); ?></td>
                                    <td>
                                        <?php $user = getUser($ticket->user_id);
                                        if (!empty($user)): ?>
                                            <a href="<?= generateProfileUrl($user->slug); ?>" target="_blank" class="table-username"><?= esc(getUsername($user)); ?></a>
                                        <?php else:
                                            echo trans("guest");
                                        endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($ticket->status == 1): ?>
                                            <label class="label label-success"><?= trans("open"); ?></label>
                                        <?php elseif ($ticket->status == 2): ?>
                                            <label class="label label-warning"><?= trans("responded"); ?></label>
                                        <?php elseif ($ticket->status == 3): ?>
                                            <label class="label label-default"><?= trans("closed"); ?></label>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= formatDate($ticket->created_at); ?></td>
                                    <td><?= timeAgo($ticket->updated_at); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-option">
                                            <a href="<?= adminUrl('support-ticket/'.$ticket->id); ?>" class="btn btn-sm btn-default btn-edit"><?= trans("show"); ?></a>
                                            <a href="javascript:void(0)" class="btn btn-sm btn-default btn-delete" onclick="deleteItem('SupportAdminController/deleteTicketPost','<?= $ticket->id; ?>','<?= trans("confirm_delete", true); ?>');"><i class="fa fa-trash-o"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach;
                        endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="pull-right">
                    <?= view('partials/_pagination'); ?>
                </div>
            </div>
        </div>
    </div>
</div>
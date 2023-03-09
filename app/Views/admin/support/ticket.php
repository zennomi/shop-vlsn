<div class="row support-admin">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><strong><?= trans('ticket'); ?>:&nbsp;#<?= $ticket->id; ?></strong></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('support-tickets'); ?>" class="btn btn-success btn-add-new">
                        <i class="fa fa-list-ul"></i>&nbsp;&nbsp;<?= trans('support_tickets'); ?>
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="col-12">
                    <div class="ticket-container">
                        <div class="new-ticket-content new-ticket-content-reply">
                            <div class="ticket-header">
                                <p><strong><?= trans("subject"); ?>:&nbsp;<?= esc($ticket->subject); ?></strong></p>
                                <div class="row row-ticket-details">
                                    <div class="col-xs-4 col-md-3">
                                        <strong><?= trans("status"); ?></strong>
                                        <?php if ($ticket->status == 1): ?>
                                            <label class="label label-success"><?= trans("open"); ?></label>
                                        <?php elseif ($ticket->status == 2): ?>
                                            <label class="label label-warning"><?= trans("responded"); ?></label>
                                        <?php elseif ($ticket->status == 3): ?>
                                            <label class="label label-default"><?= trans("closed"); ?></label>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-xs-4 col-md-3">
                                        <strong><?= trans("date"); ?></strong>
                                        <span><?= formatDate($ticket->created_at); ?></span>
                                    </div>
                                    <div class="col-xs-4 col-md-3">
                                        <strong><?= trans("last_update"); ?></strong>
                                        <span><?= timeAgo($ticket->updated_at); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="ticket-buttons">
                                        <?php if ($ticket->is_guest != 1): ?>
                                            <button class="btn btn-primary pull-left" type="button" data-toggle="collapse" data-target="#collapseTicketAnswer" aria-expanded="false" aria-controls="collapseTicketAnswer">
                                                <i class="fa fa-reply"></i>&nbsp;&nbsp;<?= trans("reply"); ?>
                                            </button>
                                        <?php endif; ?>
                                        <div class="dropdown pull-right">
                                            <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown"><?= trans("status"); ?>&nbsp;&nbsp;<span class="caret"></span></button>
                                            <ul class="dropdown-menu">
                                                <li><a href="javascript:void(0)" onclick="changeTicketStatus(<?= $ticket->id; ?>,1);"><?= trans("open"); ?></a></li>
                                                <li><a href="javascript:void(0)" onclick="changeTicketStatus(<?= $ticket->id; ?>,2);"><?= trans("responded"); ?></a></li>
                                                <li><a href="javascript:void(0)" onclick="changeTicketStatus(<?= $ticket->id; ?>,3);"><?= trans("closed"); ?></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="collapse " id="collapseTicketAnswer">
                                        <div class="reply-editor">
                                            <form action="<?= base_url('SupportAdminController/sendMessagePost'); ?>" method="post">
                                                <?= csrf_field(); ?>
                                                <input type="hidden" name="ticket_id" value="<?= $ticket->id; ?>">
                                                <div class="form-group m-0">
                                                    <label class="form-label"><?= trans("message"); ?></label>
                                                </div>
                                                <div class="form-group" style="min-height: 400px">
                                                    <textarea name="message" class="tinyMCEticket" aria-hidden="true"><?= old('message'); ?></textarea>
                                                </div>
                                                <div class="form-group m-0">
                                                    <label class="form-label"><?= trans("attachments"); ?></label>
                                                    <div class="dm-uploader-container">
                                                        <div id="drag-and-drop-zone" class="dm-uploader text-center mb-2">
                                                            <p class="dm-upload-text">
                                                                <?= trans("drag_drop_file_here"); ?>&nbsp;<span style="text-decoration: underline; font-weight: 600;"><?= trans('browse_files'); ?>
                                                            </p>
                                                            <a class='btn btn-md dm-btn-select-files'>
                                                                <input type="file" name="file" size="40" multiple="multiple">
                                                            </a>
                                                        </div>
                                                        <ul class="dm-uploaded-files" id="files-file"></ul>
                                                    </div>
                                                    <script type="text/html" id="files-template-file">
                                                        <li class="media">
                                                            <div class="media-body">
                                                                <div class="progress">
                                                                    <div class="dm-progress-waiting"></div>
                                                                    <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </script>
                                                    <div id="response_uploaded_files" class="uploaded-files">
                                                        <?php $ticketAttachments = helperGetSession('ticket_attachments');
                                                        if (!empty($ticketAttachments)):
                                                            foreach ($ticketAttachments as $file):
                                                                if (!empty($file->fileId) && !empty($file->name) && !empty($file->ticketType) && $file->ticketType == 'admin'): ?>
                                                                    <div class="item">
                                                                        <div class="item-inner">
                                                                            <?= esc($file->name); ?><a href="javascript:void(0)" onclick="deleteSupportAttachment('<?= esc($file->fileId); ?>')"><i class="fa fa-times"></i></a>
                                                                        </div>
                                                                    </div>
                                                                <?php endif;
                                                            endforeach;
                                                        endif; ?>
                                                    </div>
                                                </div>
                                                <div class="text-right m-t-20">
                                                    <button type="submit" class="btn btn-primary"><?= trans("send_message"); ?></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="ticket-content ticket-content-reset">
                            <div class="row">
                                <div class="col-sm-12">
                                    <ul class="list-unstyled">
                                        <?php if (!empty($subTickets)):
                                            foreach ($subTickets as $subTicket):
                                                $user = null;
                                                if ($ticket->is_guest != 1):
                                                    $user = getUser($subTicket->user_id);
                                                endif; ?>
                                                <li class="media<?= $subTicket->is_support_reply != 1 ? ' media-client' : ''; ?>">
                                                    <div class="left">
                                                        <img class="img-profile" src="<?= getUserAvatar($user) ?>" alt="">
                                                    </div>
                                                    <div class="right">
                                                        <div class="media-body">
                                                            <h5 class="title mt-0 mb-3">
                                                                <?php if (!empty($user)): ?>
                                                                    <a href="<?= generateProfileUrl($user->slug) ?>" class="font-color" target="_blank"><?= esc(getUsername($user)); ?></a>
                                                                <?php else: ?>
                                                                    <span><?= esc($ticket->name); ?></span><br>
                                                                    <span><?= esc($ticket->email); ?></span>
                                                                <?php endif; ?>
                                                            </h5>
                                                            <span class="date text-right"><?= timeAgo($subTicket->created_at); ?></span>
                                                            <div class="message">
                                                                <?= $subTicket->message; ?>
                                                            </div>
                                                            <?php $files = unserializeData($subTicket->attachments);
                                                            if (!empty($files)):?>
                                                                <div class="row">
                                                                    <div class="col-sm-12">
                                                                        <div class="ticket-attachments">
                                                                            <?php foreach ($files as $file): ?>
                                                                                <form action="<?= base_url('download-attachment-post'); ?>" method="post">
                                                                                    <?= csrf_field(); ?>
                                                                                    <input type="hidden" name="orj_name" value="<?= $file->orj_name; ?>">
                                                                                    <input type="hidden" name="name" value="<?= $file->name; ?>">
                                                                                    <input type="hidden" name="storage" value="<?= $subTicket->storage; ?>">
                                                                                    <p>
                                                                                        <button type="submit"><i class="fa fa-file"></i>&nbsp;&nbsp;<span><?= esc($file->orj_name); ?></span></button>
                                                                                    </p>
                                                                                </form>
                                                                            <?php endforeach; ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php endforeach;
                                        endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="<?= base_url('assets/vendor/file-uploader/css/jquery.dm-uploader.min.css'); ?>"/>
<link rel="stylesheet" href="<?= base_url('assets/vendor/file-uploader/css/styles.css'); ?>"/>
<script src="<?= base_url('assets/vendor/file-uploader/js/jquery.dm-uploader.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendor/file-uploader/js/ui.js'); ?>"></script>
<script>
    $(function () {
        $('#drag-and-drop-zone').dmUploader({
            url: '<?= base_url('SupportController/uploadSupportAttachment'); ?>',
            queue: false,
            extraData: function (id) {
                return {
                    'file_id': id,
                    'ticket_type': 'admin',
                    '<?= csrf_token() ?>': '<?= csrf_hash(); ?>'
                };
            },
            onDragEnter: function () {
                this.addClass('active');
            },
            onDragLeave: function () {
                this.removeClass('active');
            },
            onNewFile: function (id, file) {
                ui_multi_add_file(id, file, "file");
            },
            onBeforeUpload: function (id) {
                $('#uploaderFile' + id + ' .dm-progress-waiting').hide();
                ui_multi_update_file_progress(id, 0, '', true);
                ui_multi_update_file_status(id, 'uploading', 'Uploading...');
            },
            onUploadProgress: function (id, percent) {
                ui_multi_update_file_progress(id, percent);
            },
            onUploadSuccess: function (id, data) {
                var obj = JSON.parse(data);
                if (obj.result == 1) {
                    document.getElementById("response_uploaded_files").innerHTML = obj.response;
                }
                document.getElementById("uploaderFile" + id).remove();
                ui_multi_update_file_status(id, 'success', 'Upload Complete');
                ui_multi_update_file_progress(id, 100, 'success', false);
            },
            onFileSizeError: function (file) {
                alert("<?= trans("file_too_large", true); ?>");
            }
        });
    });
</script>
<div id="wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="support">
                    <nav class="nav-breadcrumb" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= langBaseUrl(); ?>"><?= trans("home"); ?></a></li>
                            <li class="breadcrumb-item"><a href="<?= generateUrl('help_center'); ?>"><?= trans("help_center"); ?></a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?= trans("submit_a_request"); ?></li>
                        </ol>
                    </nav>
                    <div class="row justify-content-center">
                        <div class="col-12 m-t-15 m-b-30">
                            <h1 class="page-title page-title-ticket"><?= trans("ticket"); ?>: #<?= $ticket->id; ?></h1>
                            <a href="<?= generateUrl('help_center', 'tickets'); ?>" class="btn btn-info color-white float-right">
                                <svg width="18" height="18" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg" fill="#fff" class="mds-svg-icon">
                                    <path d="M384 1408q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm0-512q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm1408 416v192q0 13-9.5 22.5t-22.5 9.5h-1216q-13 0-22.5-9.5t-9.5-22.5v-192q0-13 9.5-22.5t22.5-9.5h1216q13 0 22.5 9.5t9.5 22.5zm-1408-928q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm1408 416v192q0 13-9.5 22.5t-22.5 9.5h-1216q-13 0-22.5-9.5t-9.5-22.5v-192q0-13 9.5-22.5t22.5-9.5h1216q13 0 22.5 9.5t9.5 22.5zm0-512v192q0 13-9.5 22.5t-22.5 9.5h-1216q-13 0-22.5-9.5t-9.5-22.5v-192q0-13 9.5-22.5t22.5-9.5h1216q13 0 22.5 9.5t9.5 22.5z"/>
                                </svg>
                                <?= trans("support_tickets") ?>
                            </a>
                        </div>
                        <div class="col-12">
                            <div class="ticket-container shadow-sm">
                                <div class="new-ticket-content new-ticket-content-reply">
                                    <div class="ticket-header">
                                        <p><strong><?= trans("subject"); ?>:&nbsp;<?= esc($ticket->subject); ?></strong></p>
                                        <div class="row row-ticket-details">
                                            <div class="col-12 col-md-2">
                                                <strong><?= trans("status"); ?></strong>
                                                <?php if ($ticket->status == 1): ?>
                                                    <label class="badge badge-lg badge-success color-white"><?= trans("open"); ?></label>
                                                <?php elseif ($ticket->status == 2): ?>
                                                    <label class="badge badge-lg badge-warning color-white"><?= trans("responded"); ?></label>
                                                <?php elseif ($ticket->status == 3): ?>
                                                    <label class="badge badge-lg badge-secondary color-white"><?= trans("closed"); ?></label>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-12 col-md-2">
                                                <strong><?= trans("date"); ?></strong>
                                                <span><?= formatDate($ticket->created_at); ?></span>
                                            </div>
                                            <div class="col-12 col-md-2">
                                                <strong><?= trans("last_update"); ?></strong>
                                                <span><?= timeAgo($ticket->updated_at); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <?= view('partials/_messages'); ?>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="ticket-buttons">
                                                <button class="btn btn-info color-white float-left" type="button" data-toggle="collapse" data-target="#collapseTicketAnswer" aria-expanded="false" aria-controls="collapseTicketAnswer">
                                                    <i class="icon-reply"></i><?= trans("reply"); ?>
                                                </button>
                                                <?php if ($ticket->status != 3): ?>
                                                    <button class="btn btn-secondary color-white float-right" type="button" onclick="closeSupportTicket(<?= $ticket->id; ?>);">
                                                        <i class="icon-times"></i><?= trans("close_ticket"); ?>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="collapse " id="collapseTicketAnswer">
                                                <div class="reply-editor">
                                                    <form action="<?= base_url('reply-ticket-post'); ?>" method="post">
                                                        <?= csrf_field(); ?>
                                                        <input type="hidden" name="ticket_id" value="<?= $ticket->id; ?>">
                                                        <div class="form-group m-0">
                                                            <label class="control-label"><?= trans("message"); ?></label>
                                                        </div>
                                                        <div class="form-group" style="min-height: 400px">
                                                            <textarea name="message" class="tinyMCEticket" aria-hidden="true"><?= old('message'); ?></textarea>
                                                        </div>
                                                        <div class="form-group m-0">
                                                            <label class="control-label"><?= trans("attachments"); ?></label>
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
                                                                <?php $attachments = helperGetSession('ticket_attachments');
                                                                if (!empty($attachments)):
                                                                    foreach ($attachments as $file):
                                                                        if (!empty($file->fileId) && !empty($file->name) && !empty($file->ticketType) && $file->ticketType == 'client'): ?>
                                                                            <div class="item">
                                                                                <div class="item-inner">
                                                                                    <?= esc($file->name); ?>
                                                                                    <a href="javascript:void(0)" onclick="deleteSupportAttachment('<?= esc($file->fileId); ?>')">
                                                                                        <i class="icon-times"></i>
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                        <?php endif;
                                                                    endforeach;
                                                                endif; ?>
                                                            </div>
                                                        </div>
                                                        <div class="text-right m-t-20">
                                                            <button type="submit" class="btn btn-md btn-custom"><?= trans("send_message"); ?></button>
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
                                                <?php if (!empty($subtickets)):
                                                    foreach ($subtickets as $subticket):
                                                        $user = getUser($subticket->user_id); ?>
                                                        <li class="media<?= $subticket->is_support_reply != 1 ? ' media-client' : ''; ?>">
                                                            <img class="img-profile" src="<?= getUserAvatar($user) ?>" alt="">
                                                            <div class="media-body">
                                                                <h5 class="title mt-0 mb-3">
                                                                    <a href="<?= generateProfileUrl($user->slug) ?>" class="font-color" target="_blank"><?= esc(getUsername($user)); ?></a>
                                                                </h5>
                                                                <span class="date text-right"><?= timeAgo($subticket->created_at); ?></span>
                                                                <div class="message">
                                                                    <?= $subticket->message; ?>
                                                                </div>
                                                                <?php $files = unserializeData($subticket->attachments);
                                                                if (!empty($files)):?>
                                                                    <div class="row">
                                                                        <div class="col-sm-12">
                                                                            <div class="ticket-attachments">
                                                                                <?php foreach ($files as $file): ?>
                                                                                    <form action="<?= base_url('download-attachment-post'); ?>" method="post">
                                                                                        <?= csrf_field(); ?>
                                                                                        <input type="hidden" name="orj_name" value="<?= $file->orj_name; ?>">
                                                                                        <input type="hidden" name="name" value="<?= $file->name; ?>">
                                                                                        <input type="hidden" name="storage" value="<?= $subticket->storage; ?>">
                                                                                        <p>
                                                                                            <button type="submit">
                                                                                                <svg width="18" height="18" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg" fill="#3f7cbd" class="mds-svg-icon" style="top: -1px;">
                                                                                                    <path d="M1152 512v-472q22 14 36 28l408 408q14 14 28 36h-472zm-128 32q0 40 28 68t68 28h544v1056q0 40-28 68t-68 28h-1344q-40 0-68-28t-28-68v-1600q0-40 28-68t68-28h800v544z"/>
                                                                                                </svg>
                                                                                                <span><?= esc($file->orj_name); ?></span>
                                                                                            </button>
                                                                                        </p>
                                                                                    </form>
                                                                                <?php endforeach; ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <?php endif; ?>
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
    </div>
</div>
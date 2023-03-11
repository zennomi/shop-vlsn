<div id="comment-result">
    <div class="row">
        <div class="col-12">
            <div class="comments">
                <div class="row-custom row-comment-label">
                    <label class="label-comment"><?= trans("comments"); ?>&nbsp;(<?= $commentCount; ?>)</label>
                </div>
                <?php if (empty($comments)): ?>
                    <p class="no-comments-found"><?= trans("no_comments_found"); ?></p>
                <?php else: ?>
                    <ul class="comment-list">
                        <?php foreach ($comments as $comment): ?>
                            <li>
                                <div class="left">
                                    <?php if (!empty($comment->user_slug)): ?>
                                        <a href="<?= generateProfileUrl($comment->user_slug); ?>">
                                            <img src="<?= getUserAvatarByImageURL($comment->user_avatar, $comment->user_type); ?>" alt="<?= esc($comment->name); ?>">
                                        </a>
                                    <?php else: ?>
                                        <img src="<?= getUserAvatarByImageURL($comment->user_avatar, $comment->user_type); ?>" alt="<?= esc($comment->name); ?>">
                                    <?php endif; ?>
                                </div>
                                <div class="right">
                                    <div class="row-custom">
                                        <p class="username">
                                            <?= (!empty($comment->user_slug)) ? '<a href="' . generateProfileUrl($comment->user_slug) . '">' : '';
                                            if (!empty($comment->user_id)):
                                                echo !empty($comment->user_username) ? esc($comment->user_username) : esc($comment->name);
                                            else:
                                                echo esc($comment->name);
                                            endif;
                                            echo (!empty($comment->user_slug)) ? '</a>' : ''; ?>
                                        </p>
                                    </div>
                                    <div class="row-custom comment">
                                        <?= esc($comment->comment); ?>
                                    </div>
                                    <div class="row-custom">
                                        <span class="date"><?= timeAgo($comment->created_at); ?></span>
                                        <a href="javascript:void(0)" class="btn-reply" onclick="showCommentForm('<?= $comment->id; ?>');"><i class="icon-reply"></i> <?= trans('reply'); ?></a>
                                        <?php if (authCheck()):
                                            if ($comment->user_id == user()->id || hasPermission('comments')): ?>
                                                <a href="javascript:void(0)" class="btn-delete-comment" onclick="deleteComment('<?= $comment->id; ?>','<?= $product->id; ?>','<?= trans("confirm_comment", true); ?>');">&nbsp;<i class="icon-trash"></i>&nbsp;<?= trans("delete"); ?></a>
                                            <?php endif;
                                        endif;
                                        if (authCheck()): ?>
                                            <?php if ($comment->user_id != user()->id): ?>
                                                <a href="javascript:void(0)" class="text-muted link-abuse-report float-right" data-toggle="modal" data-target="#reportCommentModal" onclick="$('#report_comment_id').val('<?= $comment->id; ?>');">
                                                    <?= trans("report"); ?>
                                                </a>
                                            <?php endif;
                                        else: ?>
                                            <a href="javascript:void(0)" class="text-muted link-abuse-report float-right" data-toggle="modal" data-target="#loginModal">
                                                <?= trans("report"); ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <div id="sub_comment_form_<?= $comment->id; ?>" class="row-custom row-sub-comment visible-sub-comment">
                                    </div>
                                    <div class="row-custom row-sub-comment">
                                        <?= view('product/details/_subcomments', ['parentComment' => $comment]); ?>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($commentCount > $commentLimit): ?>
            <div id="load_comment_spinner" class="col-12 load-more-spinner">
                <div class="row">
                    <div class="spinner">
                        <div class="bounce1"></div>
                        <div class="bounce2"></div>
                        <div class="bounce3"></div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <button type="button" class="btn-load-more" onclick="loadMoreComments('<?= $product->id; ?>');">
                    <?= trans("load_more"); ?>
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="reportCommentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content modal-custom">
            <form id="form_report_comment" method="post">
                <div class="modal-header">
                    <h5 class="modal-title"><?= trans("report_comment"); ?></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true"><i class="icon-close"></i> </span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div id="response_form_report_comment" class="col-12"></div>
                        <div class="col-12">
                            <input type="hidden" id="report_comment_id" name="id" value="">
                            <div class="form-group m-0">
                                <label class="control-label"><?= trans("description"); ?></label>
                                <textarea name="description" class="form-control form-textarea" placeholder="<?= trans("abuse_report_exp"); ?>" minlength="5" maxlength="10000" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer text-right">
                    <button type="submit" class="btn btn-md btn-custom"><?= trans("submit"); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
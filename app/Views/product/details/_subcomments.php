<?php $subComments = getSubComments($parentComment->id);
if (!empty($subComments)): ?>
    <div class="row">
        <div class="col-12">
            <div class="comments">
                <ul class="comment-list">
                    <?php foreach ($subComments as $subComment): ?>
                        <li>
                            <div class="left">
                                <?php if (!empty($subComment->user_slug)): ?>
                                    <a href="<?= generateProfileUrl($subComment->user_slug); ?>">
                                        <img src="<?= getUserAvatarByImageURL($subComment->user_avatar, $subComment->user_type); ?>" alt="<?= esc($subComment->name); ?>">
                                    </a>
                                <?php else: ?>
                                    <img src="<?= getUserAvatarByImageURL($subComment->user_avatar, $subComment->user_type); ?>" alt="<?= esc($subComment->name); ?>">
                                <?php endif; ?>
                            </div>
                            <div class="right">
                                <div class="row-custom">
                                    <p class="username">
                                        <?= (!empty($subComment->user_slug)) ? '<a href="' . generateProfileUrl($subComment->user_slug) . '">' : '';
                                        if (!empty($subComment->user_id)):
                                            echo !empty($subComment->user_username) ? esc($subComment->user_username) : esc($subComment->name);
                                        else:
                                            echo esc($subComment->name);
                                        endif;
                                        echo (!empty($subComment->user_slug)) ? '</a>' : ''; ?>
                                    </p>
                                </div>
                                <div class="row-custom comment">
                                    <?= esc($subComment->comment); ?>
                                </div>
                                <div class="row-custom">
                                    <span class="date"><?= timeAgo($subComment->created_at); ?></span>
                                    <?php if (authCheck()):
                                        if ($subComment->user_id == user()->id || hasPermission('comments')): ?>
                                            <a href="javascript:void(0)" class="btn-delete-comment" onclick="deleteComment('<?= $subComment->id; ?>','<?= $subComment->product_id; ?>','<?= trans("confirm_comment", true); ?>');">&nbsp;<i class="icon-trash"></i>&nbsp;<?= trans("delete"); ?></a>
                                        <?php endif;
                                    endif;
                                    if (authCheck()):
                                        if ($subComment->user_id != user()->id):?>
                                            <a href="javascript:void(0)" class="text-muted link-abuse-report float-right" data-toggle="modal" data-target="#reportCommentModal" onclick="$('#report_comment_id').val('<?= $subComment->id; ?>');">
                                                <?= trans("report"); ?>
                                            </a>
                                        <?php endif;
                                    else: ?>
                                        <a href="javascript:void(0)" class="text-muted link-abuse-report float-right" data-toggle="modal" data-target="#loginModal">
                                            <?= trans("report"); ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
<?php endif; ?>
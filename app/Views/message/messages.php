<div id="wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="nav-breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= langBaseUrl(); ?>"><?= trans("home"); ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= trans("messages"); ?></li>
                    </ol>
                </nav>
                <h1 class="page-title"><?= trans("messages"); ?></h1>
            </div>
        </div>
        <div class="row row-col-messages">
            <?php if (empty($unreadConversations) && empty($readConversations)): ?>
                <div class="col-12">
                    <p class="text-center"><?= trans("no_messages_found"); ?></p>
                </div>
            <?php else: ?>
                <div class="col-sm-12 col-md-12 col-lg-3 col-message-sidebar">
                    <div class="message-sidebar-custom-scrollbar">
                        <div class="row-custom messages-sidebar">
                            <?php if (!empty($unreadConversations)):
                                foreach ($unreadConversations as $item):
                                    $userId = 0;
                                    if ($item->receiver_id != user()->id) {
                                        $userId = $item->receiver_id;
                                    } else {
                                        $userId = $item->sender_id;
                                    }
                                    $user = getUser($userId);
                                    if (!empty($user)):?>
                                        <div class="conversation-item <?= $item->id == $conversation->id ? 'active-conversation-item' : ''; ?>">
                                            <a href="<?= generateUrl('messages'); ?>?conv=<?= $item->id; ?>" class="conversation-item-link">
                                                <div class="middle">
                                                    <img src="<?= getUserAvatar($user); ?>" alt="<?= esc(getUsername($user)); ?>">
                                                </div>
                                                <div class="right">
                                                    <div class="row-custom">
                                                        <strong class="username"><?= esc(getUsername($user)); ?></strong>
                                                        <label class="badge badge-success badge-new"><?= trans("new_message"); ?></label>
                                                    </div>
                                                    <div class="row-custom m-b-0">
                                                        <p class="subject"><?= esc(characterLimiter($item->subject, 28, '...')); ?></p>
                                                    </div>
                                                </div>
                                            </a>
                                            <a href="javascript:void(0)" class="delete-conversation-link" onclick='deleteConversation(<?= $item->id; ?>,"<?= trans("confirm_message", true); ?>");'><i class="icon-trash"></i></a>
                                        </div>
                                    <?php endif;
                                endforeach;
                            endif;
                            if (!empty($readConversations)):
                                foreach ($readConversations as $item):
                                    $userId = 0;
                                    if ($item->receiver_id != user()->id) {
                                        $userId = $item->receiver_id;
                                    } else {
                                        $userId = $item->sender_id;
                                    }
                                    $user = getUser($userId);
                                    if (!empty($user)):?>
                                        <div class="conversation-item <?= $item->id == $conversation->id ? 'active-conversation-item' : ''; ?>">
                                            <a href="<?= generateUrl('messages'); ?>?conv=<?= $item->id; ?>" class="conversation-item-link">
                                                <div class="middle">
                                                    <img src="<?= getUserAvatar($user); ?>" alt="<?= esc(getUsername($user)); ?>">
                                                </div>
                                                <div class="right">
                                                    <div class="row-custom">
                                                        <strong class="username"><?= esc(getUsername($user)); ?></strong>
                                                    </div>
                                                    <div class="row-custom m-b-0">
                                                        <p class="subject"><?= esc(characterLimiter($item->subject, 28, '...')); ?></p>
                                                    </div>
                                                </div>
                                            </a>
                                            <a href="javascript:void(0)" class="delete-conversation-link" onclick='deleteConversation(<?= $item->id; ?>,"<?= trans("confirm_message", true); ?>");'><i class="icon-trash"></i></a>
                                        </div>
                                    <?php endif;
                                endforeach;
                            endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-9 col-message-content">
                    <?php
                    $profileId = $conversation->sender_id;
                    if (user()->id == $conversation->sender_id) {
                        $profileId = $conversation->receiver_id;
                    }
                    $profile = getUser($profileId);
                    if (!empty($profile)):?>
                        <div class="row-custom messages-head">
                            <div class="sender-head">
                                <div class="left">
                                    <img src="<?= getUserAvatar($profile); ?>" alt="<?= esc(getUsername($profile)); ?>" class="img-profile">
                                </div>
                                <div class="right">
                                    <strong class="username"><?= esc(getUsername($profile)); ?></strong>
                                    <p class="p-last-seen">
                                        <span class="last-seen <?= isUserOnline($profile->last_seen) ? 'last-seen-online' : ''; ?>"> <i class="icon-circle"></i> <?= trans("last_seen"); ?>&nbsp;<?= timeAgo($profile->last_seen); ?></span>
                                    </p>
                                    <?php if (!empty($conversation->product_id)):
                                        $product = getProduct($conversation->product_id);
                                        if (!empty($product)):?>
                                            <p class="subject m-0 font-600"><a href="<?= generateProductUrl($product); ?>" class="link-black link-underlined" target="_blank"><?= esc($conversation->subject); ?></a></p>
                                        <?php endif;
                                    else: ?>
                                        <p class="subject m-0 font-600"><?= esc($conversation->subject); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="row-custom messages-content">
                        <div id="message-custom-scrollbar" class="messages-list">
                            <?php if (!empty($messages)):
                                foreach ($messages as $item):
                                    if ($item->deleted_user_id != user()->id): ?>
                                        <?php if (user()->id == $item->receiver_id): ?>
                                            <div class="message-list-item">
                                                <div class="message-list-item-row-received">
                                                    <div class="user-avatar">
                                                        <div class="message-user">
                                                            <img src="<?= getUserAvatarById($item->sender_id); ?>" alt="" class="img-profile">
                                                        </div>
                                                    </div>
                                                    <div class="user-message">
                                                        <div class="message-text">
                                                            <?= esc($item->message); ?>
                                                        </div>
                                                        <span class="time"><?= timeAgo($item->created_at); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="message-list-item">
                                                <div class="message-list-item-row-sent">
                                                    <div class="user-message">
                                                        <div class="message-text">
                                                            <?= esc($item->message); ?>
                                                        </div>
                                                        <span class="time"><?= timeAgo($item->created_at); ?></span>
                                                    </div>
                                                    <div class="user-avatar">
                                                        <div class="message-user">
                                                            <img src="<?= getUserAvatarById($item->sender_id); ?>" alt="" class="img-profile">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif;
                                    endif;
                                endforeach;
                            endif; ?>
                        </div>
                        <div class="message-reply">
                            <form action="<?= base_url('send-message-post'); ?>" method="post" id="form_validate">
                                <?= csrf_field(); ?>
                                <input type="hidden" name="conversation_id" value="<?= $conversation->id; ?>">
                                <input type="hidden" name="back_url" value="<?= getCurrentUrl(); ?>">
                                <?php if (user()->id == $conversation->sender_id): ?>
                                    <input type="hidden" name="receiver_id" value="<?= $conversation->receiver_id; ?>">
                                <?php else: ?>
                                    <input type="hidden" name="receiver_id" value="<?= $conversation->sender_id; ?>">
                                <?php endif; ?>
                                <div class="form-group m-b-10">
                                    <textarea class="form-control form-textarea" name="message" placeholder="<?= trans('write_a_message'); ?>" required></textarea>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-md btn-custom float-right"><i class="icon-send"></i> <?= trans("send"); ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
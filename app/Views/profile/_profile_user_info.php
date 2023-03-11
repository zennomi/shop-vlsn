<div class="row-custom">
    <div class="profile-details">
        <div class="left">
            <img src="<?= getUserAvatar($user); ?>" alt="<?= esc(getUsername($user)); ?>" class="img-profile">
        </div>
        <div class="right">
            <div class="row-custom row-profile-username">
                <h1 class="username">
                    <a href="<?= generateProfileUrl($user->slug); ?>"> <?= esc(getUsername($user)); ?></a>
                </h1>
                <?php if (isVendor($user)): ?>
                    <i class="icon-verified icon-verified-member"></i>
                <?php endif; ?>
            </div>
            <div class="row-custom">
                <p class="p-last-seen">
                    <span class="last-seen <?= isUserOnline($user->last_seen) ? 'last-seen-online' : ''; ?>"> <i class="icon-circle"></i> <?= trans("last_seen"); ?>&nbsp;<?= timeAgo($user->last_seen); ?></span>
                </p>
            </div>
            <?php if (isVendor()): ?>
                <div class="row-custom">
                    <p class="description"><?= esc($user->about_me); ?></p>
                </div>
            <?php endif; ?>
            <div class="row-custom user-contact">
                <span class="info"><?= trans("member_since"); ?>&nbsp;<?= formatDateLong($user->created_at, false); ?></span>
                <?php if ($generalSettings->hide_vendor_contact_information != 1):
                    if (!empty($user->phone_number) && $user->show_phone == 1): ?>
                        <span class="info"><i class="icon-phone"></i>
                        <a href="javascript:void(0)" id="show_phone_number"><?= trans("show"); ?></a>
                        <a href="tel:<?= esc($user->phone_number); ?>" id="phone_number" class="display-none"><?= esc($user->phone_number); ?></a>
                    </span>
                    <?php endif;
                    if (!empty($user->email) && $user->show_email == 1): ?>
                        <span class="info"><i class="icon-envelope"></i><?= esc($user->email); ?></span>
                    <?php endif;
                endif;
                if (!empty(getLocation($user)) && $user->show_location == 1): ?>
                    <span class="info"><i class="icon-map-marker"></i><?= getLocation($user); ?></span>
                <?php endif; ?>
            </div>
            <?php if ($generalSettings->reviews == 1): ?>
                <div class="profile-rating">
                    <?php if ($userRating->count > 0):
                        echo view('partials/_review_stars', ['rating' => $userRating->rating]); ?>
                        &nbsp;<span>(<?= $userRating->count; ?>)</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <div class="row-custom profile-buttons">
                <div class="buttons">
                    <?php if (authCheck()):
                        if (user()->id != $user->id): ?>
                            <button class="btn btn-md btn-outline-gray" data-toggle="modal" data-target="#messageModal"><i class="icon-envelope"></i><?= trans("ask_question") ?></button>
                            <form action="<?= base_url('follow-unfollow-user-post'); ?>" method="post" class="form-inline">
                                <?= csrf_field(); ?>
                                <input type="hidden" name="back_url" value="<?= getCurrentUrl(); ?>">
                                <input type="hidden" name="user_id" value="<?= $user->id; ?>">
                                <?php if (isUserFollows($user->id, user()->id)): ?>
                                    <button class="btn btn-md btn-outline-gray"><i class="icon-user-minus"></i><?= trans("unfollow"); ?></button>
                                <?php else: ?>
                                    <button class="btn btn-md btn-outline-gray"><i class="icon-user-plus"></i><?= trans("follow"); ?></button>
                                <?php endif; ?>
                            </form>
                        <?php endif;
                  else: ?>
                        <button class="btn btn-md btn-outline-gray" data-toggle="modal" data-target="#loginModal"><i class="icon-envelope"></i><?= trans("ask_question") ?></button>
                        <button class="btn btn-md btn-outline-gray" data-toggle="modal" data-target="#loginModal"><i class="icon-user-plus"></i><?= trans("follow"); ?></button>
                    <?php endif; ?>
                </div>
                <?php if ($generalSettings->hide_vendor_contact_information != 1): ?>
                    <div class="social">
                        <ul>
                            <?php if (!empty($user->personal_website_url)): ?>
                                <li><a href="<?= esc($user->personal_website_url); ?>" target="_blank"><i class="icon-globe"></i></a></li>
                            <?php endif;
                            if (!empty($user->facebook_url)): ?>
                                <li><a href="<?= esc($user->facebook_url); ?>" target="_blank"><i class="icon-facebook"></i></a></li>
                            <?php endif;
                            if (!empty($user->twitter_url)): ?>
                                <li><a href="<?= esc($user->twitter_url); ?>" target="_blank"><i class="icon-twitter"></i></a></li>
                            <?php endif;
                            if (!empty($user->instagram_url)): ?>
                                <li><a href="<?= esc($user->instagram_url); ?>" target="_blank"><i class="icon-instagram"></i></a></li>
                            <?php endif;
                            if (!empty($user->pinterest_url)): ?>
                                <li><a href="<?= esc($user->pinterest_url); ?>" target="_blank"><i class="icon-pinterest"></i></a></li>
                            <?php endif;
                            if (!empty($user->linkedin_url)): ?>
                                <li><a href="<?= esc($user->linkedin_url); ?>" target="_blank"><i class="icon-linkedin"></i></a></li>
                            <?php endif;
                            if (!empty($user->vk_url)): ?>
                                <li><a href="<?= esc($user->vk_url); ?>" target="_blank"><i class="icon-vk"></i></a></li>
                            <?php endif;
                            if (!empty($user->whatsapp_url)): ?>
                                <li><a href="<?= esc($user->whatsapp_url); ?>" target="_blank"><i class="icon-whatsapp"></i></a></li>
                            <?php endif;
                            if (!empty($user->telegram_url)): ?>
                                <li><a href="<?= esc($user->telegram_url); ?>" target="_blank"><i class="icon-telegram"></i></a></li>
                            <?php endif;
                            if (!empty($user->youtube_url)): ?>
                                <li><a href="<?= esc($user->youtube_url); ?>" target="_blank"><i class="icon-youtube"></i></a></li>
                            <?php endif;
                            if ($generalSettings->rss_system == 1 && $user->show_rss_feeds == 1 && getUserTotalProductsCount($user->id) > 0): ?>
                                <li><a href="<?= langBaseUrl() . '/rss/' . getRoute('seller', true) . $user->slug; ?>" target="_blank"><i class="icon-rss"></i></a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<div id="products" class="row-custom"></div>

<?= view('partials/_modal_send_message', ['subject' => null]); ?>
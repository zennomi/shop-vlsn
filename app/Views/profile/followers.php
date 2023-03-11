<?= view("profile/_cover_image"); ?>
<div id="wrapper">
    <div class="container">
        <?php if (empty($user->cover_image)): ?>
            <div class="row">
                <div class="col-12">
                    <nav class="nav-breadcrumb" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= langBaseUrl(); ?>"><?= trans("home"); ?></a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?= trans("followers"); ?></li>
                        </ol>
                    </nav>
                </div>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-12">
                <div class="profile-page-top">
                    <?= view('profile/_profile_user_info'); ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?= view('profile/_tabs'); ?>
            </div>
            <div class="col-12">
                <div class="profile-tab-content">
                    <div class="row row-follower">
                        <?php if (!empty($followers)):
                            foreach ($followers as $item): ?>
                                <div class="col-3 col-sm-2">
                                    <div class="follower-item">
                                        <a href="<?= generateProfileUrl($item->slug); ?>">
                                            <img src="<?= getUserAvatar($item); ?>" alt="<?= esc(getUsername($item)); ?>" class="img-fluid img-profile lazyload">
                                            <span class="username"><?= esc(getUsername($item)); ?></span>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach;
                        else:?>
                            <div class="col-12">
                                <p class="text-center text-muted"><?= trans("no_records_found"); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="row-custom">
                        <?= view('partials/_ad_spaces', ['adSpace' => 'profile', 'class' => 'm-t-30']); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


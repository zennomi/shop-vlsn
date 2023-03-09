<div id="wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="nav-breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= langBaseUrl(); ?>"><?= trans("home"); ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= $title; ?></li>
                    </ol>
                </nav>
                <h1 class="page-title"><?= trans("settings"); ?></h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-3">
                <div class="row-custom">
                    <?= view("settings/_tabs"); ?>
                </div>
            </div>
            <div class="col-sm-12 col-md-9">
                <div class="row-custom">
                    <div class="profile-tab-content">
                        <?= view('partials/_messages'); ?>
                        <form action="<?= base_url('social-media-post'); ?>" method="post" id="form_validate">
                            <?= csrf_field(); ?>
                            <div class="form-group">
                                <label class="control-label"><?= trans('personal_website_url'); ?></label>
                                <input type="text" class="form-control form-input" name="personal_website_url" placeholder="<?= trans('personal_website_url'); ?>" value="<?= esc(user()->personal_website_url); ?>" maxlength="900">
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?= trans('facebook_url'); ?></label>
                                <input type="text" class="form-control form-input" name="facebook_url" placeholder="<?= trans('facebook_url'); ?>" value="<?= esc(user()->facebook_url); ?>" maxlength="900">
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?= trans('twitter_url'); ?></label>
                                <input type="text" class="form-control form-input" name="twitter_url" placeholder="<?= trans('twitter_url'); ?>" value="<?= esc(user()->twitter_url); ?>" maxlength="900">
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?= trans('instagram_url'); ?></label>
                                <input type="text" class="form-control form-input" name="instagram_url" placeholder="<?= trans('instagram_url'); ?>" value="<?= esc(user()->instagram_url); ?>" maxlength="900">
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?= trans('pinterest_url'); ?></label>
                                <input type="text" class="form-control form-input" name="pinterest_url" placeholder="<?= trans('pinterest_url'); ?>" value="<?= esc(user()->pinterest_url); ?>" maxlength="900">
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?= trans('linkedin_url'); ?></label>
                                <input type="text" class="form-control form-input" name="linkedin_url" placeholder="<?= trans('linkedin_url'); ?>" value="<?= esc(user()->linkedin_url); ?>" maxlength="900">
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?= trans('vk_url'); ?></label>
                                <input type="text" class="form-control form-input" name="vk_url" placeholder="<?= trans('vk_url'); ?>" value="<?= esc(user()->vk_url); ?>" maxlength="900">
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?= trans('whatsapp_url'); ?></label>
                                <input type="text" class="form-control form-input" name="whatsapp_url" placeholder="<?= trans('whatsapp_url'); ?>" value="<?= esc(user()->whatsapp_url); ?>" maxlength="900">
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?= trans('telegram_url'); ?></label>
                                <input type="text" class="form-control form-input" name="telegram_url" placeholder="<?= trans('telegram_url'); ?>" value="<?= esc(user()->telegram_url); ?>" maxlength="900">
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?= trans('youtube_url'); ?></label>
                                <input type="text" class="form-control form-input" name="youtube_url" placeholder="<?= trans('youtube_url'); ?>" value="<?= esc(user()->youtube_url); ?>" maxlength="900">
                            </div>
                            <button type="submit" class="btn btn-md btn-custom"><?= trans("save_changes") ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
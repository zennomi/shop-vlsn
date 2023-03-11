<?php $activeTab = inputGet('tab');
if (empty($activeTab)):
    $activeTab = '1';
endif; ?>
<div class="row">
    <div class="col-md-12">
        <form action="<?= base_url('AdminController/generalSettingsPost'); ?>" method="post">
            <?= csrf_field(); ?>
            <input type="hidden" name="active_tab" id="input_active_tab" value="<?= clrNum($activeTab); ?>">
            <input type="hidden" name="lang_id" value="<?= clrNum(inputGet('lang')); ?>">
            <div class="form-group">
                <label><?= trans("settings_language"); ?></label>
                <select name="lang_id" class="form-control" onchange="window.location.href = '<?= adminUrl(); ?>/general-settings?lang='+this.value+'&tab=<?= clrNum($activeTab); ?>';" style="max-width: 600px;">
                    <?php foreach ($activeLanguages as $language): ?>
                        <option value="<?= $language->id; ?>" <?= $language->id == $settingsLang ? 'selected' : ''; ?>><?= esc($language->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="<?= $activeTab == '1' ? ' active' : ''; ?>"><a href="#tab_1" data-toggle="tab" onclick="$('#input_active_tab').val('1');"><?= trans('general_settings'); ?></a></li>
                    <li class="<?= $activeTab == '2' ? ' active' : ''; ?>"><a href="#tab_2" data-toggle="tab" onclick="$('#input_active_tab').val('2');"><?= trans('contact_settings'); ?></a></li>
                    <li class="<?= $activeTab == '3' ? ' active' : ''; ?>"><a href="#tab_3" data-toggle="tab" onclick="$('#input_active_tab').val('3');"><?= trans('social_media_settings'); ?></a></li>
                    <li class="<?= $activeTab == '4' ? ' active' : ''; ?>"><a href="#tab_4" data-toggle="tab" onclick="$('#input_active_tab').val('4');"><?= trans('facebook_comments'); ?></a></li>
                    <li class="<?= $activeTab == '5' ? ' active' : ''; ?>"><a href="#tab_5" data-toggle="tab" onclick="$('#input_active_tab').val('5');"><?= trans('custom_header_codes'); ?></a></li>
                    <li class="<?= $activeTab == '6' ? ' active' : ''; ?>"><a href="#tab_6" data-toggle="tab" onclick="$('#input_active_tab').val('6');"><?= trans('custom_footer_codes'); ?></a></li>
                    <li class="<?= $activeTab == '7' ? ' active' : ''; ?>"><a href="#tab_7" data-toggle="tab" onclick="$('#input_active_tab').val('7');"><?= trans('cookies_warning'); ?></a></li>
                </ul>
                <div class="tab-content settings-tab-content">
                    <div class="tab-pane<?= $activeTab == '1' ? ' active' : ''; ?>" id="tab_1">
                        <div class="form-group">
                            <label class="control-label"><?= trans('app_name'); ?></label>
                            <input type="text" class="form-control" name="application_name" placeholder="<?= trans('app_name'); ?>" value="<?= esc($generalSettings->application_name); ?>">
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?= trans('site_title'); ?></label>
                            <input type="text" class="form-control" name="site_title" placeholder="<?= trans('site_title'); ?>" value="<?= esc($settings->site_title); ?>">
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?= trans('homepage_title'); ?></label>
                            <input type="text" class="form-control" name="homepage_title" placeholder="<?= trans('homepage_title'); ?>" value="<?= esc($settings->homepage_title); ?>">
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?= trans('site_description'); ?></label>
                            <input type="text" class="form-control" name="site_description" placeholder="<?= trans('site_description'); ?>" value="<?= esc($settings->site_description); ?>">
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?= trans('keywords'); ?></label>
                            <input type="text" class="form-control" name="keywords" placeholder="<?= trans('keywords'); ?>" value="<?= esc($settings->keywords); ?>">
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?= trans('copyright'); ?></label>
                            <input type="text" class="form-control" name="copyright" placeholder="<?= trans('copyright'); ?>" value="<?= esc($settings->copyright); ?>">
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?= trans('footer_about_section'); ?></label>
                            <textarea class="form-control tinyMCEsmall" name="about_footer" placeholder="<?= trans('footer_about_section'); ?>" style="min-height: 140px;"><?= esc($settings->about_footer); ?></textarea>
                        </div>
                    </div>
                    <div class="tab-pane<?= $activeTab == '2' ? ' active' : ''; ?>" id="tab_2">
                        <div class="form-group">
                            <label class="control-label"><?= trans('address'); ?></label>
                            <input type="text" class="form-control" name="contact_address" placeholder="<?= trans('address'); ?>" value="<?= esc($settings->contact_address); ?>">
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?= trans('email_address'); ?></label>
                            <input type="text" class="form-control" name="contact_email" placeholder="<?= trans('email_address'); ?>" value="<?= esc($settings->contact_email); ?>">
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?= trans('phone'); ?></label>
                            <input type="text" class="form-control" name="contact_phone" placeholder="<?= trans('phone'); ?>" value="<?= esc($settings->contact_phone); ?>">
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?= trans('contact_text'); ?></label>
                            <textarea class="form-control tinyMCEsmall" name="contact_text" placeholder="<?= trans('contact_text'); ?>"><?= esc($settings->contact_text); ?></textarea>
                        </div>
                    </div>
                    <div class="tab-pane<?= $activeTab == '3' ? ' active' : ''; ?>" id="tab_3">
                        <div class="form-group">
                            <label class="control-label"><?= trans('facebook_url'); ?></label>
                            <input type="text" class="form-control" name="facebook_url" placeholder="<?= trans('facebook_url'); ?>" value="<?= esc($settings->facebook_url); ?>">
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?= trans('twitter_url'); ?></label>
                            <input type="text" class="form-control" name="twitter_url" placeholder="<?= trans('twitter_url'); ?>" value="<?= esc($settings->twitter_url); ?>">
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?= trans('instagram_url'); ?></label>
                            <input type="text" class="form-control" name="instagram_url" placeholder="<?= trans('instagram_url'); ?>" value="<?= esc($settings->instagram_url); ?>">
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?= trans('pinterest_url'); ?></label>
                            <input type="text" class="form-control" name="pinterest_url" placeholder="<?= trans('pinterest_url'); ?>" value="<?= esc($settings->pinterest_url); ?>">
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?= trans('linkedin_url'); ?></label>
                            <input type="text" class="form-control" name="linkedin_url" placeholder="<?= trans('linkedin_url'); ?>" value="<?= esc($settings->linkedin_url); ?>">
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?= trans('vk_url'); ?></label>
                            <input type="text" class="form-control" name="vk_url" placeholder="<?= trans('vk_url'); ?>" value="<?= esc($settings->vk_url); ?>">
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?= trans('whatsapp_url'); ?></label>
                            <input type="text" class="form-control form-input" name="whatsapp_url" placeholder="<?= trans('whatsapp_url'); ?>" value="<?= esc($settings->whatsapp_url); ?>">
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?= trans('telegram_url'); ?></label>
                            <input type="text" class="form-control form-input" name="telegram_url" placeholder="<?= trans('telegram_url'); ?>" value="<?= esc($settings->telegram_url); ?>">
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?= trans('youtube_url'); ?></label>
                            <input type="text" class="form-control" name="youtube_url" placeholder="<?= trans('youtube_url'); ?>" value="<?= esc($settings->youtube_url); ?>">
                        </div>
                    </div>
                    <div class="tab-pane<?= $activeTab == '4' ? ' active' : ''; ?>" id="tab_4">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-3 col-xs-12">
                                    <label><?= trans('facebook_comments'); ?></label>
                                </div>
                                <div class="col-md-2 col-sm-4 col-xs-12 col-option">
                                    <input type="radio" name="facebook_comment_status" value="1" id="facebook_comment_status_1" class="square-purple" <?= $generalSettings->facebook_comment_status == 1 ? 'checked' : ''; ?>>
                                    <label for="facebook_comment_status_1" class="option-label"><?= trans('enable'); ?></label>
                                </div>
                                <div class="col-md-2 col-sm-4 col-xs-12 col-option">
                                    <input type="radio" name="facebook_comment_status" value="0" id="facebook_comment_status_2" class="square-purple" <?= $generalSettings->facebook_comment_status != 1 ? 'checked' : ''; ?>>
                                    <label for="facebook_comment_status_2" class="option-label"><?= trans('disable'); ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?= trans('facebook_comments_code'); ?></label>
                            <textarea class="form-control text-area" name="facebook_comment" placeholder="<?= trans('facebook_comments_code'); ?>" style="min-height: 140px;"><?= $generalSettings->facebook_comment; ?></textarea>
                        </div>
                    </div>
                    <div class="tab-pane<?= $activeTab == '5' ? ' active' : ''; ?>" id="tab_5">
                        <div class="form-group">
                            <label class="control-label"><?= trans('custom_header_codes'); ?></label>&nbsp;<small class="small-title-inline">(<?= trans("custom_header_codes_exp"); ?>)</small>
                            <textarea class="form-control text-area" name="custom_header_codes" placeholder="<?= trans('custom_header_codes'); ?>" style="min-height: 200px;"><?= $generalSettings->custom_header_codes; ?></textarea>
                        </div>
                        E.g. <?= esc("<style> body {background-color: #00a65a;} </style>"); ?>
                    </div>
                    <div class="tab-pane<?= $activeTab == '6' ? ' active' : ''; ?>" id="tab_6">
                        <div class="form-group">
                            <label class="control-label"><?= trans('custom_footer_codes'); ?></label>&nbsp;<small class="small-title-inline">(<?= trans("custom_footer_codes_exp"); ?>)</small>
                            <textarea class="form-control text-area" name="custom_footer_codes" placeholder="<?= trans('custom_footer_codes'); ?>" style="min-height: 200px;"><?= $generalSettings->custom_footer_codes; ?></textarea>
                        </div>
                        E.g. <?= esc("<script> alert('Hello!'); </script>"); ?>
                    </div>
                    <div class="tab-pane<?= $activeTab == '7' ? ' active' : ''; ?>" id="tab_7">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-3 col-xs-12 col-lang">
                                    <label><?= trans('show_cookies_warning'); ?></label>
                                </div>
                                <div class="col-md-2 col-sm-4 col-xs-12 col-option">
                                    <input type="radio" name="cookies_warning" value="1" id="cookies_warning_1" class="square-purple" <?= $settings->cookies_warning == 1 ? 'checked' : ''; ?>>
                                    <label for="cookies_warning_1" class="option-label"><?= trans('yes'); ?></label>
                                </div>
                                <div class="col-md-2 col-sm-4 col-xs-12 col-option">
                                    <input type="radio" name="cookies_warning" value="0" id="cookies_warning_2" class="square-purple" <?= $settings->cookies_warning != 1 ? 'checked' : ''; ?>>
                                    <label for="cookies_warning_2" class="option-label"><?= trans('no'); ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?= trans('cookies_warning_text'); ?></label>
                            <textarea class="form-control tinyMCEsmall" name="cookies_warning_text"><?= $settings->cookies_warning_text; ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('google_recaptcha'); ?></h3>
            </div>
            <form action="<?= base_url('AdminController/recaptchaSettingsPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <input type="hidden" name="lang_id" value="<?= clrNum(inputGet('lang')); ?>">
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label"><?= trans('site_key'); ?></label>
                        <input type="text" class="form-control" name="recaptcha_site_key" placeholder="<?= trans('site_key'); ?>" value="<?= esc($generalSettings->recaptcha_site_key); ?>">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('secret_key'); ?></label>
                        <input type="text" class="form-control" name="recaptcha_secret_key" placeholder="<?= trans('secret_key'); ?>" value="<?= esc($generalSettings->recaptcha_secret_key); ?>">
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-lg-6 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('maintenance_mode'); ?></h3>
            </div>
            <form action="<?= base_url('AdminController/maintenanceModePost'); ?>" method="post">
                <?= csrf_field(); ?>
                <input type="hidden" name="lang_id" value="<?= clrNum(inputGet('lang')); ?>">
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label"><?= trans('title'); ?></label>
                        <input type="text" class="form-control" name="maintenance_mode_title" placeholder="<?= trans('title'); ?>" value="<?= esc($generalSettings->maintenance_mode_title); ?>">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('description'); ?></label>
                        <textarea class="form-control text-area" name="maintenance_mode_description" placeholder="<?= trans('description'); ?>" style="min-height: 100px;"><?= esc($generalSettings->maintenance_mode_description); ?></textarea>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4 col-xs-12">
                                <label><?= trans('status'); ?></label>
                            </div>
                            <div class="col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="maintenance_mode_status" value="1" id="maintenance_mode_status_1" class="square-purple" <?= $generalSettings->maintenance_mode_status == 1 ? 'checked' : ''; ?>>
                                <label for="maintenance_mode_status_1" class="option-label"><?= trans('enable'); ?></label>
                            </div>
                            <div class="col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="maintenance_mode_status" value="0" id="maintenance_mode_status_2" class="square-purple" <?= $generalSettings->maintenance_mode_status != 1 ? 'checked' : ''; ?>>
                                <label for="maintenance_mode_status_2" class="option-label"><?= trans('disable'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><?= trans('image'); ?></label>: assets/img/maintenance_bg.jpg
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
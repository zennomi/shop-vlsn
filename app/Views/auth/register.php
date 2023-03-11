<div id="wrapper">
    <div class="container">
        <div class="auth-container">
            <div class="auth-box">
                <div class="row">
                    <div class="col-12">
                        <h1 class="title"><?= trans("register"); ?></h1>
                        <form action="<?= base_url('register-post'); ?>" method="post" id="form_validate" class="validate_terms" <?= $baseVars->recaptchaStatus ? 'onsubmit="checkRecaptchaRegisterForm(this);"' : ''; ?>>
                            <?= csrf_field(); ?>
                            <div class="social-login">
                                <?= view('auth/_social_login', ['orText' => trans("register_with_email")]); ?>
                            </div>
                            <div id="result-register">
                                <?= view('partials/_messages'); ?>
                            </div>
                            <div class="spinner display-none spinner-activation-register">
                                <div class="bounce1"></div>
                                <div class="bounce2"></div>
                                <div class="bounce3"></div>
                            </div>
                            <div class="form-group">
                                <input type="text" name="first_name" class="form-control auth-form-input" placeholder="<?= trans("first_name"); ?>" value="<?= old("first_name"); ?>" maxlength="255" required>
                            </div>
                            <div class="form-group">
                                <input type="text" name="last_name" class="form-control auth-form-input" placeholder="<?= trans("last_name"); ?>" value="<?= old("last_name"); ?>" maxlength="255" required>
                            </div>
                            <div class="form-group">
                                <input type="email" name="email" class="form-control auth-form-input" placeholder="<?= trans("email_address"); ?>" value="<?= old("email"); ?>" maxlength="255" required>
                            </div>
                            <div class="form-group">
                                <input type="password" name="password" class="form-control auth-form-input" placeholder="<?= trans("password"); ?>" value="<?= old("password"); ?>" minlength="4" maxlength="255" required>
                            </div>
                            <div class="form-group">
                                <input type="password" name="confirm_password" class="form-control auth-form-input" placeholder="<?= trans("password_confirm"); ?>" maxlength="255" required>
                            </div>
                            <div class="form-group m-t-5 m-b-15">
                                <div class="custom-control custom-checkbox custom-control-validate-input">
                                    <input type="checkbox" class="custom-control-input" name="terms" id="checkbox_terms" required>
                                    <label for="checkbox_terms" class="custom-control-label"><?= trans("terms_conditions_exp"); ?>&nbsp;
                                        <?php $pageTerms = getPageByDefaultName("terms_conditions", selectedLangId());
                                        if (!empty($pageTerms)): ?>
                                            <a href="<?= generateUrl($pageTerms->page_default_name); ?>" class="link-terms" target="_blank"><strong><?= esc($pageTerms->title); ?></strong></a>
                                        <?php endif; ?>
                                    </label>
                                </div>
                            </div>
                            <?php if ($baseVars->recaptchaStatus): ?>
                                <div class="form-group m-b-15">
                                    <div class="display-flex justify-content-center">
                                        <?php reCaptcha('generate'); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="form-group">
                                <button type="submit" class="btn btn-custom btn-block"><?= trans("register"); ?></button>
                            </div>
                            <p class="p-social-media m-0 m-t-15"><?= trans("have_account"); ?>&nbsp;<a href="javascript:void(0)" class="link font-600" data-toggle="modal" data-target="#loginModal"><?= trans("login"); ?></a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
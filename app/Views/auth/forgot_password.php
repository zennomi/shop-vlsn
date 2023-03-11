<div id="wrapper">
    <div class="container">
        <div class="auth-container">
            <div class="auth-box">
                <div class="row">
                    <div class="col-12">
                        <h1 class="title"><?= trans("reset_password"); ?></h1>
                        <form action="<?= base_url('forgot-password-post'); ?>" method="post" id="form_validate">
                            <?= csrf_field(); ?>
                            <div class="form-group">
                                <p class="p-social-media m-0"><?= trans("reset_password_subtitle"); ?></p>
                            </div>
                            <?= view('partials/_messages'); ?>
                            <div class="form-group m-b-30">
                                <input type="email" name="email" class="form-control auth-form-input" placeholder="<?= trans("email_address"); ?>" value="<?= old("email"); ?>" maxlength="255" required>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-md btn-custom btn-block"><?= trans("submit"); ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="wrapper">
    <div class="container">
        <div class="auth-container">
            <div class="auth-box">
                <div class="row">
                    <div class="col-12">
                        <h1 class="title"><?= trans("reset_password"); ?></h1>
                        <form action="<?= base_url('reset-password-post'); ?>" method="post" id="form_validate">
                            <?= csrf_field(); ?>
                            <?= view('partials/_messages'); ?>
                            <?php if (!empty($user)): ?>
                                <input type="hidden" name="token" value="<?= esc($user->token); ?>">
                            <?php endif;
                            if (!empty($success)): ?>
                                <div class="form-group m-t-30">
                                    <a href="<?= langBaseUrl(); ?>" class="btn btn-md btn-custom btn-block"><?= trans("goto_home"); ?></a>
                                </div>
                            <?php else: ?>
                                <div class="form-group">
                                    <input type="password" name="password" class="form-control form-input" value="<?= old("password"); ?>" placeholder="<?= trans("new_password"); ?>" minlength="4" maxlength="255" required>
                                </div>
                                <div class="form-group m-b-30">
                                    <input type="password" name="password_confirm" class="form-control form-input" value="<?= old("password_confirm"); ?>" placeholder="<?= trans("password_confirm"); ?>" maxlength="255" required>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-md btn-custom btn-block"><?= trans("submit"); ?></button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
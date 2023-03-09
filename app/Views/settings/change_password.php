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
                        <form action="<?= base_url('change-password-post'); ?>" method="post" id="form_validate">
                            <?= csrf_field(); ?>
                            <?php if (!empty(user()->password)): ?>
                                <div class="form-group">
                                    <label class="control-label"><?= trans("old_password"); ?></label>
                                    <input type="password" name="old_password" class="form-control form-input" value="<?= old("old_password"); ?>" placeholder="<?= trans("old_password"); ?>" maxlength="255" required>
                                </div>
                            <?php endif; ?>
                            <div class="form-group">
                                <label class="control-label"><?= trans("password"); ?></label>
                                <input type="password" name="password" class="form-control form-input" value="<?= old("password"); ?>" placeholder="<?= trans("password"); ?>" minlength="4" maxlength="255" required>
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?= trans("password_confirm"); ?></label>
                                <input type="password" name="password_confirm" class="form-control form-input" value="<?= old("password_confirm"); ?>" placeholder="<?= trans("password_confirm"); ?>" maxlength="255" required>
                            </div>
                            <button type="submit" class="btn btn-md btn-custom"><?= trans("change_password") ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
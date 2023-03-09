<div id="wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="page-confirm">
                    <?php if (!empty($success)): ?>
                        <div class="circle-loader">
                            <div class="checkmark draw"></div>
                        </div>
                        <h1 class="title">
                            <?= $success; ?>
                        </h1>
                        <a href="<?= langBaseUrl(); ?>" class="btn btn-md btn-custom btn-block m-t-30"><?= trans("goto_home"); ?></a>
                    <?php elseif (!empty($error)): ?>
                        <div class="error-circle">
                            <i class="icon-close-thin"></i>
                        </div>
                        <h1 class="title">
                            <?= $error; ?>
                        </h1>
                        <a href="<?= langBaseUrl(); ?>" class="btn btn-md btn-custom btn-block m-t-30"><?= trans("goto_home"); ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
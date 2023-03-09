<?php if (!empty($user->cover_image)):
    if ($user->cover_image_type == 'boxed'):?>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <img src="<?= IMG_BASE64_1x1; ?>" data-src="<?= base_url($user->cover_image); ?>" class="lazyload img-profile-cover">
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="container-fluid">
            <div class="row">
                <img src="<?= IMG_BASE64_1x1; ?>" data-src="<?= base_url($user->cover_image); ?>" class="lazyload img-profile-cover">
            </div>
        </div>
    <?php endif;
endif; ?>
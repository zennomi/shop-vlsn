<?php $imageCount = 0;
if (!empty($productImages)) {
    $imageCount = countItems($productImages);
}
if ($imageCount <= 1 && (!empty($video) || !empty($audio))):
    if (!empty($video)): ?>
        <div class="product-video-preview">
            <video id="player" playsinline controls>
                <source src="<?= getProductVideoUrl($video); ?>" type="video/mp4">
            </video>
        </div>
    <?php endif;
    if (!empty($audio)):
        echo view('product/details/_audio_player');
    endif; ?>
<?php else: ?>
    <div class="product-slider-container">
        <?php if (countItems($productImages) > 1): ?>
            <div class="left">
                <div class="product-slider-content">
                    <div id="product_thumbnails_slider" class="product-thumbnails-slider">
                        <?php foreach ($productImages as $image): ?>
                            <div class="item">
                                <div class="item-inner">
                                    <img src="<?= IMG_BASE64_1x1; ?>" class="img-bg" alt="slider-bg">
                                    <img src="<?= IMG_BASE64_1x1; ?>" data-lazy="<?= getProductImageURL($image, 'image_small'); ?>" class="img-thumbnail" alt="<?= !empty($productDetails) ? esc($productDetails->title) : ''; ?>">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (countItems($productImages) > 7): ?>
                        <div id="product-thumbnails-slider-nav" class="product-thumbnails-slider-nav">
                            <button class="prev"><i class="icon-arrow-up"></i></button>
                            <button class="next"><i class="icon-arrow-down"></i></button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        <div class="right">
            <div class="product-slider-content">
                <div id="product_slider" class="product-slider gallery">
                    <?php if (!empty($productImages)):
                        foreach ($productImages as $image): ?>
                            <div class="item">
                                <a href="<?= getProductImageURL($image, 'image_big'); ?>" title="">
                                    <img src="<?= base_url(IMG_BG_PRODUCT_SLIDER); ?>" class="img-bg" alt="slider-bg">
                                    <img src="<?= IMG_BASE64_1x1; ?>" data-lazy="<?= getProductImageURL($image, 'image_default'); ?>" alt="<?= !empty($productDetails) ? esc($productDetails->title) : ''; ?>" class="img-product-slider">
                                </a>
                            </div>
                        <?php endforeach;
                    else: ?>
                        <div class="item">
                            <a href="javascript:void(0)" title="">
                                <img src="<?= base_url(IMG_BG_PRODUCT_SLIDER); ?>" class="img-bg" alt="slider-bg">
                                <img src="<?= IMG_BASE64_1x1; ?>" data-lazy="<?= base_url() . 'assets/img/no-image.jpg'; ?>" alt="<?= !empty($productDetails) ? esc($productDetails->title) : ''; ?>" class="img-product-slider">
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if (countItems($productImages) > 1): ?>
                    <div id="product-slider-nav" class="product-slider-nav">
                        <button class="prev"><i class="icon-arrow-left"></i></button>
                        <button class="next"><i class="icon-arrow-right"></i></button>
                    </div>
                <?php endif; ?>
            </div>
            <div class="row-custom text-center">
                <?php if (!empty($video)): ?>
                    <button class="btn btn-lg btn-video-preview" data-toggle="modal" data-target="#productVideoModal"><i class="icon-play"></i><?= trans("video"); ?></button>
                <?php endif;
                if (!empty($audio)): ?>
                    <button class="btn btn-lg btn-video-preview" data-toggle="modal" data-target="#productAudioModal"><i class="icon-music"></i><?= trans("audio"); ?></button>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif;
if ($imageCount > 1 && !empty($video)): ?>
    <div class="modal fade" id="productVideoModal" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-product-video" role="document">
            <div class="modal-content">
                <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                <div class="product-video-preview m-0">
                    <video id="player" playsinline controls>
                        <source src="<?= getProductVideoUrl($video); ?>" type="video/mp4">
                    </video>
                </div>
            </div>
        </div>
    </div>
<?php endif;
if ($imageCount > 1 && !empty($audio)): ?>
    <div class="modal fade" id="productAudioModal" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-product-video" role="document">
            <div class="modal-content">
                <div class="row-custom" style="width: auto !important;">
                    <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                    <?= view('product/details/_audio_player'); ?>
                </div>
            </div>
        </div>
    </div>
<?php endif;
if (countItems($productImages) <= 7): ?>
    <style>
        .product-thumbnails-slider .slick-track {
            transform: none !important;
        }
    </style>
<?php endif; ?>


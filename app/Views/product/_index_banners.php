<?php if (!empty($indexBannersArray) && !empty($bannerLocation) && !empty($indexBannersArray[$bannerLocation])): ?>
    <div class="col-12 section section-index-bn">
        <div class="row">
            <?php foreach ($indexBannersArray[$bannerLocation] as $banner):
                if ($banner->banner_location == $bannerLocation):?>
                    <div class="col-6 col-index-bn index_bn_<?= $banner->id; ?>">
                        <a href="<?= $banner->banner_url; ?>">
                            <img src="<?= IMG_BASE64_1x1; ?>" data-src="<?= base_url($banner->banner_image_path); ?>" alt="banner" class="lazyload img-fluid">
                        </a>
                    </div>
                <?php endif;
            endforeach; ?>
        </div>
    </div>
<?php endif; ?>
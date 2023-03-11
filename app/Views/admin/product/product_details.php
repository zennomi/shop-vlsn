<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('product_details'); ?></h3>
            </div>
            <div class="box-body">
                <?php $images = getProductImages($product->id);
                if (!empty($images)):?>
                    <div class="row row-product-details row-product-images">
                        <div class="col-sm-12">
                            <?php foreach ($images as $image): ?>
                                <div class="image m-b-10">
                                    <img src="<?= getProductImageURL($image, 'image_small'); ?>" alt="">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="row row-product-details">
                    <div class="col-md-3 col-sm-12">
                        <label class="control-label"><?= trans('link'); ?></label>
                    </div>
                    <div class="col-md-9 col-sm-12 right">
                        <a href="<?= generateProductUrl($product); ?>" target="_blank"><?= generateProductUrl($product); ?></a>
                    </div>
                </div>
                <div class="row row-product-details">
                    <div class="col-md-3 col-sm-12">
                        <label class="control-label"><?= trans('status'); ?></label>
                    </div>
                    <div class="col-md-9 col-sm-12 right">
                        <?php if ($product->status == 1): ?>
                            <label class="label label-success"><?= trans("active"); ?></label>
                        <?php else: ?>
                            <?php if ($product->is_rejected == 1): ?>
                                <label class="label label-danger"><?= trans("rejected"); ?></label>
                                <button type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="#modalReason"><i class="fa fa-info-circle"></i>&nbsp;&nbsp;<?= trans("show_reason"); ?></button>
                                <div id="modalReason" class="modal fade" role="dialog">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title"><?= trans("reason"); ?></h4>
                                            </div>
                                            <div class="modal-body">
                                                <p class="m-t-10"><?= esc($product->reject_reason); ?></p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal"><?= trans("close"); ?></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <label class="label label-default"><?= trans("pending"); ?></label>
                            <?php endif;
                        endif; ?>
                    </div>
                </div>
                <div class="row row-product-details">
                    <div class="col-md-3 col-sm-12">
                        <label class="control-label"><?= trans('visibility'); ?></label>
                    </div>
                    <div class="col-md-9 col-sm-12 right">
                        <?php if ($product->visibility == 1): ?>
                            <label class="label label-success"><?= trans("visible"); ?></label>
                        <?php else: ?>
                            <label class="label label-danger"><?= trans("hidden"); ?></label>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row row-product-details">
                    <div class="col-md-3 col-sm-12">
                        <label class="control-label"><?= trans('id'); ?></label>
                    </div>
                    <div class="col-md-9 col-sm-12 right">
                        <?= $product->id; ?>
                    </div>
                </div>
                <div class="row row-product-details">
                    <div class="col-md-3 col-sm-12">
                        <label class="control-label"><?= trans('title'); ?></label>
                    </div>
                    <div class="col-md-9 col-sm-12 right">
                        <?= esc($productDetails->title); ?>
                    </div>
                </div>
                <div class="row row-product-details">
                    <div class="col-md-3 col-sm-12">
                        <label class="control-label"><?= trans('slug'); ?></label>
                    </div>
                    <div class="col-md-9 col-sm-12 right">
                        <?= esc($product->slug); ?>
                    </div>
                </div>
                <div class="row row-product-details">
                    <div class="col-md-3 col-sm-12">
                        <label class="control-label"><?= trans('product_type'); ?></label>
                    </div>
                    <div class="col-md-9 col-sm-12 right">
                        <?= trans($product->product_type); ?>
                    </div>
                </div>
                <div class="row row-product-details">
                    <div class="col-md-3 col-sm-12">
                        <label class="control-label"><?= trans('listing_type'); ?></label>
                    </div>
                    <div class="col-md-9 col-sm-12 right">
                        <?= getProductListingType($product); ?>
                    </div>
                </div>
                <div class="row row-product-details">
                    <div class="col-md-3 col-sm-12">
                        <label class="control-label"><?= trans('category'); ?></label>
                    </div>
                    <div class="col-md-9 col-sm-12 right">
                        <?php $category = getCategory($product->category_id);
                        if (!empty($category)) {
                            $i = 0;
                            $categories = getCategoryParentTree($category, false);
                            if (!empty($categories)) {
                                foreach ($categories as $category) {
                                    if ($i != 0) {
                                        echo ', ';
                                    }
                                    echo esc($category->name);
                                    $i++;
                                }
                            }
                        } ?>
                    </div>
                </div>
                <div class="row row-product-details">
                    <div class="col-md-3 col-sm-12">
                        <label class="control-label"><?= trans('price'); ?></label>
                    </div>
                    <div class="col-md-9 col-sm-12 right">
                        <?= priceFormatted($product->price, $product->currency) . ' ' . $product->currency; ?>
                    </div>
                </div>
                <div class="row row-product-details">
                    <div class="col-md-3 col-sm-12">
                        <label class="control-label"><?= trans('stock'); ?></label>
                    </div>
                    <div class="col-md-9 col-sm-12 right">
                        <?php if ($product->product_type == 'digital'):
                            echo trans("in_stock");
                        else:
                            echo $product->stock;
                        endif; ?>
                    </div>
                </div>
                <div class="row row-product-details">
                    <div class="col-md-3 col-sm-12">
                        <label class="control-label"><?= trans('location'); ?></label>
                    </div>
                    <div class="col-md-9 col-sm-12 right">
                        <?= getLocation($product); ?>
                    </div>
                </div>
                <div class="row row-product-details">
                    <div class="col-md-3 col-sm-12">
                        <label class="control-label"><?= trans('user'); ?></label>
                    </div>
                    <div class="col-md-9 col-sm-12 right">
                        <?php $user = getUser($product->user_id);
                        if (!empty($user)): ?>
                            <a href="<?= generateProfileUrl($user->slug); ?>" target="_blank">
                                <img src="<?= getUserAvatar($user); ?>" alt="" style="width: 50px; height: 50px;">
                                &nbsp;<strong><?= esc(getUsername($user)); ?></strong>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row row-product-details">
                    <div class="col-md-3 col-sm-12">
                        <label class="control-label"><?= trans('promoted'); ?></label>
                    </div>
                    <div class="col-md-9 col-sm-12 right">
                        <?php if ($product->is_promoted == 1): ?>
                            <label class="label label-success"><?= trans("yes"); ?></label><br><br>
                            <?php if ($product->status == 1): ?>
                                <label><?= trans("start"); ?>: &nbsp;<?= $product->promote_start_date; ?></label><br>
                                <label><?= trans("end"); ?>: &nbsp;<?= $product->promote_end_date; ?></label><br>
                                <label><?= trans("remaining_days"); ?>: &nbsp;<strong><?= dateDifference($product->promote_end_date, date('Y-m-d H:i:s')); ?></strong></label>
                            <?php else: ?>
                                <label><?= trans("purchased_plan") . ': ' . esc($product->promote_plan); ?></label>
                            <?php endif;
                        else: ?>
                            <label class="label label-danger"><?= trans("no"); ?></label>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row row-product-details">
                    <div class="col-md-3 col-sm-12">
                        <label class="control-label"><?= trans('reviews'); ?></label>
                    </div>
                    <div class="col-md-9 col-sm-12 right">
                        <?= view('admin/includes/_review_stars', ['review' => $product->rating]); ?>
                        <span>(<?= $reviewCount; ?>)</span>
                        <style>
                            .rating {
                                float: left;
                                display: inline-block;
                                margin-right: 10px;
                            }
                        </style>
                    </div>
                </div>
                <div class="row row-product-details">
                    <div class="col-md-3 col-sm-12">
                        <label class="control-label"><?= trans('page_views'); ?></label>
                    </div>
                    <div class="col-md-9 col-sm-12 right">
                        <?= $product->pageviews; ?>
                    </div>
                </div>
                <div class="row row-product-details">
                    <div class="col-md-3 col-sm-12">
                        <label class="control-label"><?= trans('demo_url'); ?></label>
                    </div>
                    <div class="col-md-9 col-sm-12 right">
                        <?php if (!empty($product->demo_url)): ?>
                            <a href="<?= $product->demo_url; ?>" target="_blank"><?= $product->demo_url; ?></a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row row-product-details">
                    <div class="col-md-3 col-sm-12">
                        <label class="control-label"><?= trans('external_link'); ?></label>
                    </div>
                    <div class="col-md-9 col-sm-12 right">
                        <?php if (!empty($product->external_link)): ?>
                            <a href="<?= $product->external_link; ?>" target="_blank" rel="nofollow"><?= $product->external_link; ?></a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row row-product-details">
                    <div class="col-md-3 col-sm-12">
                        <label class="control-label"><?= trans('files_included'); ?></label>
                    </div>
                    <div class="col-md-9 col-sm-12 right">
                        <?= $product->files_included; ?>
                    </div>
                </div>
                <div class="row row-product-details">
                    <div class="col-md-3 col-sm-12">
                        <label class="control-label"><?= trans('draft'); ?></label>
                    </div>
                    <div class="col-md-9 col-sm-12 right">
                        <?php if ($product->is_draft == 1): ?>
                            <label class="label label-success"><?= trans("yes"); ?></label>
                        <?php else: ?>
                            <label class="label label-danger"><?= trans("no"); ?></label>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row row-product-details">
                    <div class="col-md-3 col-sm-12">
                        <label class="control-label"><?= trans('video_preview'); ?></label>
                    </div>
                    <div class="col-md-9 col-sm-12 right">
                        <?php if (!empty($video)): ?>
                            <div style="width: 500px; max-width: 100%;">
                                <video controls style="width: 100%;">
                                    <source src="<?= getProductVideoUrl($video); ?>" type="video/mp4">
                                </video>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row row-product-details">
                    <div class="col-md-3 col-sm-12">
                        <label class="control-label"><?= trans('audio_preview'); ?></label>
                    </div>
                    <div class="col-md-9 col-sm-12 right">
                        <?php if (!empty($audio)): ?>
                            <div style="width: 500px; max-width: 100%;">
                                <audio controls style="width: 100%;">
                                    <source src="<?= getProductAudioUrl($audio); ?>" type="audio/mp3"/>
                                </audio>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row row-product-details">
                    <div class="col-md-3 col-sm-12">
                        <label class="control-label"><?= trans('digital_files'); ?></label>
                    </div>
                    <div class="col-md-9 col-sm-12 right">
                        <?php if (!empty($digitalFile)): ?>
                            <form action="<?= base_url('FileController/downloadDigitalFile'); ?>" method="post" id="form_download_digital_file">
                                <?= csrf_field(); ?>
                                <input type="hidden" name="file_id" value="<?= $digitalFile->id; ?>">
                                <div class="dm-uploaded-digital-file">
                                    <a href="javascript:void(0)" class="float-left button-link-style" onclick="$('#form_download_digital_file').submit();">
                                        <i class="icon-file-archive file-icon"></i>&nbsp;&nbsp;<strong><?= esc($digitalFile->file_name); ?></strong>
                                    </a>
                                    <button type="submit" class="btn btn-sm btn-info color-white float-right m-r-5">
                                        <i class="icon-cloud-download"></i><?= trans("download"); ?>
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row row-product-details">
                    <div class="col-md-3 col-sm-12">
                        <label class="control-label"><?= trans('description'); ?></label>
                    </div>
                    <div class="col-md-9 col-sm-12 right description">
                        <?= $productDetails->description; ?>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <a href="<?= generateDashUrl('edit_product') . '/' . $product->id; ?>" target="_blank" class="btn btn-info pull-right"><i class="fa fa-edit"></i>&nbsp;&nbsp;<?= trans('edit'); ?></a>
                <button type="button" class="btn btn-danger pull-right m-r-5" data-toggle="modal" data-target="#modalReject"><i class="fa fa-ban"></i>&nbsp;&nbsp;<?= trans('reject'); ?></button>
                <form action="<?= base_url('ProductController/approveProduct'); ?>" method="post" style="display: inline-block !important; float: right;">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="id" value="<?= $product->id; ?>">
                    <?php if ($product->status != 1): ?>
                        <button type="submit" name="option" value="approve" class="btn btn-success pull-right m-r-5"><i class="fa fa-check"></i>&nbsp;&nbsp;<?= trans('approve'); ?></button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="modalReject" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('ProductController/rejectProduct'); ?>" method="post">
                <?= csrf_field(); ?>
                <input type="hidden" name="id" value="<?= $product->id; ?>">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?= trans("reject"); ?></h4>
                </div>
                <div class="modal-body">
                    <textarea name="reject_reason" class="form-control form-textarea" placeholder="<?= trans("reason"); ?>.." style="min-height: 150px;"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success"><?= trans("submit"); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-lg-8">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans("edit_banner"); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('homepage-manager'); ?>" class="btn btn-success btn-add-new"><i class="fa fa-bars"></i><?= trans("homepage_manager"); ?></a>
                </div>
            </div>

            <form action="<?= base_url('AdminController/editIndexBannerPost'); ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <input type="hidden" name="id" value="<?= $banner->id; ?>">
                <div class="box-body">
                    <div class="form-group">
                        <input type="text" name="banner_url" class="form-control" value="<?= $banner->banner_url; ?>" placeholder="<?= trans("banner"); ?>&nbsp;<?= trans("url"); ?>" required>
                    </div>
                    <div class="form-group">
                        <input type="number" name="banner_order" min="1" max="9999999" value="<?= $banner->banner_order; ?>" class="form-control" placeholder="<?= trans("order"); ?>" required>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <input type="number" name="banner_width" min="1" max="100" step="0.01" value="<?= $banner->banner_width; ?>" class="form-control" placeholder="<?= trans("banner_width"); ?>&nbsp;(E.g: 50)" required>
                            <span class="input-group-addon"><strong>%</strong></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label><?= trans("location"); ?>&nbsp;<small>(<?= trans("banner_location_exp"); ?>)</small></label>
                            </div>
                            <div class="col-sm-12 col-option">
                                <input type="radio" name="banner_location" value="featured_categories" id="location_1" class="square-purple" <?= $banner->banner_location == 'featured_categories' ? 'checked' : ''; ?>>
                                <label for="location_1" class="option-label"><?= trans("featured_categories"); ?></label>
                            </div>
                            <div class="col-sm-12 col-option">
                                <input type="radio" name="banner_location" value="special_offers" id="location_2" class="square-purple" <?= $banner->banner_location == 'special_offers' ? 'checked' : ''; ?>>
                                <label for="location_2" class="option-label"><?= trans("special_offers"); ?></label>
                            </div>
                            <div class="col-sm-12 col-option">
                                <input type="radio" name="banner_location" value="featured_products" id="location_3" class="square-purple" <?= $banner->banner_location == 'featured_products' ? 'checked' : ''; ?>>
                                <label for="location_3" class="option-label"><?= trans("featured_products"); ?></label>
                            </div>
                            <div class="col-sm-12 col-option">
                                <input type="radio" name="banner_location" value="new_arrivals" id="location_4" class="square-purple" <?= $banner->banner_location == 'new_arrivals' ? 'checked' : ''; ?>>
                                <label for="location_4" class="option-label"><?= trans("new_arrivals"); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans("banner"); ?></label><br>
                        <img src="<?= base_url($banner->banner_image_path); ?>" style="max-width: 320px; max-height: 320px;"><br><br>
                        <div class="display-block">
                            <a class='btn btn-default btn-sm btn-file-upload'>
                                <i class="fa fa-image text-muted"></i>&nbsp;&nbsp;<?= trans("select_image"); ?>
                                <input type="file" name="file" size="40" accept=".png, .jpg, .jpeg, .gif" onchange="$('#upload-file-info').html($(this).val().replace(/.*[\/\\]/, ''));">
                            </a>
                            <br>
                            <span class='label label-default label-file-upload' id="upload-file-info"></span>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
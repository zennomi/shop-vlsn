<div class="row">
    <div class="col-lg-6 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('pricing'); ?></h3>
            </div>
            <form action="<?= base_url('ProductController/featuredProductsPricingPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label"><?= trans('price_per_day'); ?></label>
                        <input type="text" name="price_per_day" class="form-control form-input price-input" value="<?= getPrice($paymentSettings->price_per_day, 'input'); ?>" onpaste="return false;" maxlength="32" required>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('price_per_month'); ?></label>
                        <input type="text" name="price_per_month" class="form-control form-input price-input" value="<?= getPrice($paymentSettings->price_per_month, 'input'); ?>" onpaste="return false;" maxlength="32" required>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4 col-xs-12">
                                <label><?= trans("free_promotion"); ?></label>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="free_product_promotion" value="1" id="free_promotion_1" class="square-purple" <?= $paymentSettings->free_product_promotion == 1 ? 'checked' : ''; ?>>
                                <label for="free_promotion_1" class="option-label"><?= trans('enable'); ?></label>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="free_product_promotion" value="0" id="free_promotion_2" class="square-purple" <?= $paymentSettings->free_product_promotion != 1 ? 'checked' : ''; ?>>
                                <label for="free_promotion_2" class="option-label"><?= trans('disable'); ?></label>
                            </div>
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
<div class="row">
    <div class="col-lg-8 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('system_settings'); ?></h3>
            </div>
            <form action="<?= base_url('AdminController/systemSettingsPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12 m-b-10">
                                <label><strong><?= trans('physical_products'); ?></strong></label>
                            </div>
                            <div class="col-sm-6 col-xs-12 col-option">
                                <input type="radio" name="physical_products_system" value="1" id="physical_products_1" class="square-purple" <?= $generalSettings->physical_products_system == 1 ? 'checked' : ''; ?>>
                                <label for="physical_products_1" class="option-label"><?= trans('enable'); ?></label>
                            </div>
                            <div class="col-sm-6 col-xs-12 col-option">
                                <input type="radio" name="physical_products_system" value="0" id="physical_products_2" class="square-purple" <?= $generalSettings->physical_products_system != 1 ? 'checked' : ''; ?>>
                                <label for="physical_products_2" class="option-label"><?= trans('disable'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12 m-b-10">
                                <label><strong><?= trans('digital_products'); ?></strong></label>
                            </div>
                            <div class="col-sm-6 col-xs-12 col-option">
                                <input type="radio" name="digital_products_system" value="1" id="digital_products_1" class="square-purple" <?= $generalSettings->digital_products_system == 1 ? 'checked' : ''; ?>>
                                <label for="digital_products_1" class="option-label"><?= trans('enable'); ?></label>
                            </div>
                            <div class="col-sm-6 col-xs-12 col-option">
                                <input type="radio" name="digital_products_system" value="0" id="digital_products_2" class="square-purple" <?= $generalSettings->digital_products_system != 1 ? 'checked' : ''; ?>>
                                <label for="digital_products_2" class="option-label"><?= trans('disable'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12 m-b-10">
                                <label><strong><?= trans('marketplace_selling_product_on_the_site'); ?></strong></label>
                            </div>
                            <div class="col-sm-6 col-xs-12 col-option">
                                <input type="radio" name="marketplace_system" value="1" id="marketplace_system_1" class="square-purple" <?= $generalSettings->marketplace_system == 1 ? 'checked' : ''; ?>>
                                <label for="marketplace_system_1" class="option-label"><?= trans('enable'); ?></label>
                            </div>
                            <div class="col-sm-6 col-xs-12 col-option">
                                <input type="radio" name="marketplace_system" value="0" id="marketplace_system_2" class="square-purple" <?= $generalSettings->marketplace_system != 1 ? 'checked' : ''; ?>>
                                <label for="marketplace_system_2" class="option-label"><?= trans('disable'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12 m-b-10">
                                <label><strong><?= trans('classified_ads_adding_product_as_listing'); ?></strong></label>
                            </div>
                            <div class="col-sm-6 col-xs-12 col-option">
                                <input type="radio" name="classified_ads_system" value="1" id="classified_ads_system_1" class="square-purple" <?= $generalSettings->classified_ads_system == 1 ? 'checked' : ''; ?>>
                                <label for="classified_ads_system_1" class="option-label"><?= trans('enable'); ?></label>
                            </div>
                            <div class="col-sm-6 col-xs-12 col-option">
                                <input type="radio" name="classified_ads_system" value="0" id="classified_ads_system_2" class="square-purple" <?= $generalSettings->classified_ads_system != 1 ? 'checked' : ''; ?>>
                                <label for="classified_ads_system_2" class="option-label"><?= trans('disable'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12 m-b-10">
                                <label><strong><?= trans('bidding_system_request_quote'); ?></strong></label>
                            </div>
                            <div class="col-sm-6 col-xs-12 col-option">
                                <input type="radio" name="bidding_system" value="1" id="bidding_system_1" class="square-purple" <?= $generalSettings->bidding_system == 1 ? 'checked' : ''; ?>>
                                <label for="bidding_system_1" class="option-label"><?= trans('enable'); ?></label>
                            </div>
                            <div class="col-sm-6 col-xs-12 col-option">
                                <input type="radio" name="bidding_system" value="0" id="bidding_system_2" class="square-purple" <?= $generalSettings->bidding_system != 1 ? 'checked' : ''; ?>>
                                <label for="bidding_system_2" class="option-label"><?= trans('disable'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12 m-b-10">
                                <label><strong><?= trans('selling_license_keys'); ?></strong></label>
                            </div>
                            <div class="col-sm-6 col-xs-12 col-option">
                                <input type="radio" name="selling_license_keys_system" value="1" id="selling_license_keys_system_1" class="square-purple" <?= $generalSettings->selling_license_keys_system == 1 ? 'checked' : ''; ?>>
                                <label for="selling_license_keys_system_1" class="option-label"><?= trans('enable'); ?></label>
                            </div>
                            <div class="col-sm-6 col-xs-12 col-option">
                                <input type="radio" name="selling_license_keys_system" value="0" id="selling_license_keys_system_2" class="square-purple" <?= $generalSettings->selling_license_keys_system != 1 ? 'checked' : ''; ?>>
                                <label for="selling_license_keys_system_2" class="option-label"><?= trans('disable'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12 m-b-10">
                                <label><?= trans('multi_vendor_system'); ?></label>
                                <small style="font-size: 13px;">(<?= trans("multi_vendor_system_exp"); ?>)</small>
                            </div>
                            <div class="col-sm-6 col-xs-12 col-option">
                                <input type="radio" name="multi_vendor_system" value="1" id="multi_vendor_system_1" class="square-purple" <?= $generalSettings->multi_vendor_system == 1 ? 'checked' : ''; ?>>
                                <label for="multi_vendor_system_1" class="option-label"><?= trans('enable'); ?></label>
                            </div>
                            <div class="col-sm-6 col-xs-12 col-option">
                                <input type="radio" name="multi_vendor_system" value="0" id="multi_vendor_system_2" class="square-purple" <?= $generalSettings->multi_vendor_system != 1 ? 'checked' : ''; ?>>
                                <label for="multi_vendor_system_2" class="option-label"><?= trans('disable'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12 m-b-10">
                                <label><?= trans('vat'); ?></label>
                                <small style="font-size: 13px;">(<?= trans("vat_exp"); ?>)</small>
                            </div>
                            <div class="col-sm-6 col-xs-12 col-option">
                                <input type="radio" name="vat_status" value="1" id="vat_status_1" class="square-purple" <?= $generalSettings->vat_status == 1 ? 'checked' : ''; ?>>
                                <label for="vat_status_1" class="option-label"><?= trans('enable'); ?></label>
                            </div>
                            <div class="col-sm-6 col-xs-12 col-option">
                                <input type="radio" name="vat_status" value="0" id="vat_status_2" class="square-purple" <?= $generalSettings->vat_status != 1 ? 'checked' : ''; ?>>
                                <label for="vat_status_2" class="option-label"><?= trans('disable'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><?= trans('commission_rate'); ?>(%)</label>
                        <input type="number" name="commission_rate" class="form-control" min="0" max="100" value="<?= $generalSettings->commission_rate; ?>">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('timezone'); ?></label>
                        <select name="timezone" class="form-control">
                            <?php $timezones = timezone_identifiers_list();
                            if (!empty($timezones)):
                                foreach ($timezones as $timezone):?>
                                    <option value="<?= $timezone; ?>" <?= $timezone == $generalSettings->timezone ? 'selected' : ''; ?>><?= $timezone; ?></option>
                                <?php endforeach;
                            endif; ?>
                        </select>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
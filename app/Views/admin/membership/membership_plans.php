<div class="row">
    <div class="col-lg-7 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans("create_new_plan"); ?></h3>
            </div>
            <form action="<?= base_url('MembershipController/addPlanPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label"><?= trans("title"); ?></label>
                        <?php foreach ($activeLanguages as $language): ?>
                            <input type="text" class="form-control m-b-5" name="title_<?= $language->id; ?>" placeholder="<?= esc($language->name); ?>" maxlength="255" required>
                        <?php endforeach; ?>
                    </div>
                    <div class="form-inline option-plan-type m-b-15">
                        <label class="control-label m-b-5"><?= trans("number_of_ads"); ?></label>
                        <div>
                            <div class="form-group form-group-number-of-ads">
                                <input type="number" class="form-control form-input m-r-10" name="number_of_ads" min="1" max="999999999" placeholder="E.g: 10" required style="min-width: 400px; max-width: 100%;">
                            </div>
                            <div class="form-group">
                                <div class="col-custom-option">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" name="is_unlimited_number_of_ads" id="checkbox_is_unlimited_number_of_ads" value="1" class="custom-control-input">
                                        <label for="checkbox_is_unlimited_number_of_ads" class="custom-control-label"><?= trans("unlimited"); ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-inline m-b-15">
                        <label class="control-label m-b-5"><?= trans("duration") . " (" . trans("time_limit_for_plan") . ")"; ?></label>
                        <div>
                            <div class="form-group form-group-duration">
                                <input type="number" class="form-control form-input m-r-10" name="number_of_days" min="1" max="999999999" placeholder="<?= trans("number_of_days") ?>&nbsp;&nbsp;(E.g: 30)" required style="min-width: 400px; max-width: 100%;">
                            </div>
                            <div class="form-group">
                                <div class="col-custom-option">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" name="is_unlimited_time" id="checkbox_is_unlimited_time" value="1" class="custom-control-input">
                                        <label for="checkbox_is_unlimited_time" class="custom-control-label"><?= trans("unlimited"); ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-inline m-b-15">
                        <label class="control-label m-b-5"><?= trans("price"); ?></label>
                        <div>
                            <div class="form-group form-group-price">
                                <div class="input-group" style="min-width: 410px; max-width: 100%; padding-right: 10px;">
                                    <span class="input-group-addon"><?= $defaultCurrency->symbol; ?></span>
                                    <input type="text" name="price" class="form-control form-input price-input validate-price-input" placeholder="<?= $baseVars->inputInitialPrice; ?>" onpaste="return false;" maxlength="32" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-custom-option">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" name="is_free" id="checkbox_free" value="1" class="custom-control-input">
                                        <label for="checkbox_free" class="custom-control-label"><?= trans("free"); ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans("order"); ?></label>
                        <input type="number" class="form-control" name="plan_order" min="1" max="99999" placeholder="<?= trans("order"); ?>" required>
                    </div>
                    <div class="form-group">
                        <div class="col-custom-option">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="is_popular" id="checkbox_is_popular" value="1" class="custom-control-input">
                                <label for="checkbox_is_popular" class="custom-control-label"><?= trans("popular"); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans("features"); ?></label>
                        <hr style="margin-top: 5px;margin-bottom: 5px;">
                        <div class="membership-plans-container">
                            <div class="feature">
                                <p class="m-b-5"><?= trans("feature"); ?></p>
                                <?php foreach ($activeLanguages as $language): ?>
                                    <input type="text" name="feature_<?= $language->id; ?>[]" class="form-control m-b-5" placeholder="<?= esc($language->name); ?>" required>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 text-right">
                                <button type="button" class="btn btn-sm btn-success" onclick="addMembershipFeature();">
                                    <i class="fa fa-plus"></i>&nbsp;<?= trans("add_feature"); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans("submit"); ?></button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-lg-5 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans("settings"); ?></h3>
            </div>
            <form action="<?= base_url('MembershipController/settingsPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                <label><?= trans("status"); ?></label>
                            </div>
                            <div class="col-sm-6 col-xs-12 col-option">
                                <input type="radio" name="membership_plans_system" value="1" id="radio_status_1" class="square-purple" <?= $generalSettings->membership_plans_system == 1 ? 'checked' : ''; ?>>
                                <label for="radio_status_1" class="option-label"><?= trans("enable"); ?></label>
                            </div>
                            <div class="col-sm-6 col-xs-12 col-option">
                                <input type="radio" name="membership_plans_system" value="0" id="radio_status_2" class="square-purple" <?= $generalSettings->membership_plans_system != 1 ? 'checked' : ''; ?>>
                                <label for="radio_status_2" class="option-label"><?= trans("disable"); ?></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans("save_changes"); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if (!empty($membershipPlans)): ?>
    <div class="row" style="margin-bottom: 100px;">
        <div class="col-sm-12 m-b-15">
            <h3 class="box-title" style="font-size: 18px; font-weight: 600;"><?= trans("membership_plans"); ?></h3>
        </div>
        <div class="col-sm-12">
            <div class="price-box-container">
                <?php foreach ($membershipPlans as $plan): ?>
                    <div class="price-box">
                        <?php if ($plan->is_popular == 1): ?>
                            <div class="ribbon ribbon-top-right"><span><?= trans("popular"); ?></span></div>
                        <?php endif; ?>
                        <div class="price-box-inner">
                            <div class="pricing-name text-center">
                                <h4 class="name font-600"><?= getMembershipPlanName($plan->title_array, selectedLangId()); ?></h4>
                            </div>
                            <div class="plan-price text-center">
                                <h3><strong class="price font-600">
                                        <?php if ($plan->price == 0):
                                            echo trans("free");
                                        else:
                                            echo priceFormatted($plan->price, $paymentSettings->default_currency);
                                        endif; ?>
                                    </strong>
                                </h3>
                            </div>
                            <div class="price-features">
                                <?php $features = getMembershipPlanFeatures($plan->features_array, selectedLangId());
                                if (!empty($features)):
                                    foreach ($features as $feature):?>
                                        <p>
                                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-check" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M10.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.236.236 0 0 1 .02-.022z"/>
                                            </svg>
                                            <?= esc($feature); ?>
                                        </p>
                                    <?php endforeach;
                                endif; ?>
                            </div>
                            <div class="text-center">
                                <div class="btn-group">
                                    <a href="<?= adminUrl('edit-plan/' . $plan->id); ?>" class="btn btn-default btn-edit"><?= trans("edit"); ?></a>
                                    <a href="javascript:void(0)" class="btn btn-default btn-delete" onclick="deleteItem('MembershipController/deletePlanPost','<?= $plan->id; ?>','<?= trans("confirm_delete", true); ?>');"><i class="fa fa-trash-o"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    function addMembershipFeature() {
        var feature = '<div class="feature">\n';
        feature += '<p class="m-b-5"><?= trans("feature"); ?><span class="btn btn-xs btn-danger btn-delete-membership-feature m-l-5"><i class="fa fa-times"></i></span></p>\n';
        <?php foreach ($activeLanguages as $language): ?>
        feature += '<input type="text" name="feature_<?= $language->id; ?>[]" class="form-control m-b-5" placeholder="<?= esc($language->name); ?>" required>';
        <?php endforeach; ?>
        feature += '</div>';
        $('.membership-plans-container').append(feature);
    }
    $(document).on('click', '.btn-delete-membership-feature', function () {
        $(this).closest('.feature').remove();
    });
    $(document).on('change', '#checkbox_is_unlimited_time', function () {
        if ($(this).is(':checked')) {
            $(".form-group-duration").hide();
            $(".form-group-duration .form-input").prop('required', false);
        } else {
            $(".form-group-duration").show();
            $(".form-group-duration .form-input").prop('required', true);
        }
    });
    $(document).on('change', '#checkbox_is_unlimited_number_of_ads', function () {
        if ($(this).is(':checked')) {
            $(".form-group-number-of-ads").hide();
            $(".form-group-number-of-ads .form-input").prop('required', false);
        } else {
            $(".form-group-number-of-ads").show();
            $(".form-group-number-of-ads .form-input").prop('required', true);
        }
    });
    $(document).on('change', '#checkbox_is_unlimited_sum_of_prices', function () {
        if ($(this).is(':checked')) {
            $(".form-group-sum-of-prices").hide();
            $(".form-group-sum-of-prices .form-input").prop('required', false);
        } else {
            $(".form-group-sum-of-prices").show();
            $(".form-group-sum-of-prices .form-input").prop('required', true);
        }
    });
    $(document).on('change', '#checkbox_free', function () {
        if ($(this).is(':checked')) {
            $(".form-group-price").hide();
            $(".form-group-price .form-input").prop('required', false);
        } else {
            $(".form-group-price").show();
            $(".form-group-price .form-input").prop('required', true);
        }
    });
</script>

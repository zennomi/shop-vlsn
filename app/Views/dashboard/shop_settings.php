<div class="row">
    <div class="col-sm-12">
        <?= view('dashboard/includes/_messages'); ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-7">
        <div class="box">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= esc($title); ?></h3>
                </div>
            </div>
            <div class="box-body">
                <form action="<?= base_url('shop-settings-post'); ?>" method="post">
                    <?= csrf_field(); ?>
                    <div class="form-group">
                        <label class="control-label"><?= trans("shop_name"); ?></label>
                        <input type="text" name="shop_name" class="form-control form-input" value="<?= esc(getUsername(user())); ?>" placeholder="<?= trans("shop_name"); ?>" maxlength="<?= $baseVars->usernameMaxlength; ?>">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans("shop_description"); ?></label>
                        <textarea name="about_me" class="form-control form-textarea" placeholder="<?= trans("shop_description"); ?>"><?= esc(user()->about_me); ?></textarea>
                    </div>
                    <?php if ($generalSettings->rss_system == 1): ?>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-12 col-xs-12">
                                    <label><?= trans('rss_feeds'); ?></label>
                                </div>
                                <div class="col-md-6 col-sm-12 col-custom-option">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="show_rss_feeds" value="1" id="show_rss_feeds_1" class="custom-control-input" <?= user()->show_rss_feeds == 1 ? 'checked' : ''; ?>>
                                        <label for="show_rss_feeds_1" class="custom-control-label"><?= trans("enable"); ?></label>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12 col-custom-option">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="show_rss_feeds" value="0" id="show_rss_feeds_2" class="custom-control-input" <?= user()->show_rss_feeds != 1 ? 'checked' : ''; ?>>
                                        <label for="show_rss_feeds_2" class="custom-control-label"><?= trans("disable"); ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="show_rss_feeds" value="<?= user()->show_rss_feeds; ?>">
                    <?php endif; ?>
                    <div class="form-group m-0">
                        <label><?= trans("shop_location"); ?></label>
                        <?= view('partials/_location', ['countries' => getCountries(), 'countryId' => user()->country_id, 'stateId' => user()->state_id, 'cityId' => user()->city_id, 'map' => false]); ?>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12 col-sm-9 m-b-sm-15">
                                <input type="text" name="address" id="address_input" class="form-control form-input" value="<?= esc(user()->address); ?>" placeholder="<?= trans("address") ?>" maxlength="490">
                            </div>
                            <div class="col-sm-12 col-sm-3">
                                <input type="text" name="zip_code" id="zip_code_input" class="form-control form-input" value="<?= esc(user()->zip_code); ?>" placeholder="<?= trans("zip_code") ?>" maxlength="90">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div id="map-result">
                            <div class="map-container">
                                <iframe src="https://maps.google.com/maps?width=100%&height=600&hl=en&q=<?= getLocation(user()); ?>&ie=UTF8&t=&z=8&iwloc=B&output=embed&disableDefaultUI=true" id="IframeMap" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-right">
                        <button type="submit" name="submit" value="update" class="btn btn-md btn-success"><?= trans("save_changes") ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php if ($generalSettings->membership_plans_system == 1): ?>
        <div class="col-sm-5">
            <div class="box">
                <div class="box-header with-border">
                    <div class="left">
                        <h3 class="box-title"><?= trans("membership_plan"); ?></h3>
                    </div>
                </div>
                <?php if (isSuperAdmin()): ?>
                    <div class="box-body">
                        <div class="alert alert-info alert-large">
                            <?= trans("warning_membership_admin_role"); ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="box-body">
                        <?php if (!empty($userPlan)): ?>
                            <div class="form-group">
                                <label class="control-label"><?= trans("current_plan"); ?></label><br>
                                <?php $plan = null;
                                if (!empty($userPlan->plan_id)) {
                                    $plan = getMembershipPlan($userPlan->plan_id);
                                }
                                if (empty($plan)):?>
                                    <p class="label label-success label-user-plan"><?= esc($userPlan->plan_title); ?></p>
                                <?php else: ?>
                                    <p class="label label-success label-user-plan"><?= esc(getMembershipPlanTitle($plan)); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?= trans("plan_expiration_date"); ?></label><br>
                                <?php if ($userPlan->is_unlimited_time): ?>
                                    <p class="text-success"><?= trans("unlimited"); ?></p>
                                <?php else: ?>
                                    <p><?= formatDate($userPlan->plan_end_date); ?>&nbsp;<span class="text-danger">(<?= ucfirst(trans("days_left")); ?>:&nbsp;<?= $daysLeft < 0 ? 0 : $daysLeft; ?>)</span></p>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?= trans("number_remaining_ads"); ?></label><br>
                                <?php if ($userPlan->is_unlimited_number_of_ads): ?>
                                    <p class="text-success"><?= trans("unlimited"); ?></p>
                                <?php else: ?>
                                    <p><?= esc($adsLeft); ?></p>
                                <?php endif; ?>
                            </div>
                            <?php if (user()->is_membership_plan_expired == 1): ?>
                                <div class="form-group text-center">
                                    <p class="label label-danger label-user-plan"><?= trans("msg_plan_expired"); ?></p>
                                </div>
                            <?php endif; ?>
                            <div class="form-group text-center">
                                <a href="<?= generateUrl('select_membership_plan'); ?>" class="btn btn-md btn-block btn-slate m-t-30" style="padding: 10px 12px;"><?= trans("renew_your_plan") ?></a>
                            </div>
                        <?php else: ?>
                            <div class="form-group">
                                <p><?= trans("do_not_have_membership_plan"); ?></p>
                            </div>
                            <div class="form-group text-center">
                                <a href="<?= generateUrl('select_membership_plan'); ?>" class="btn btn-md btn-block btn-slate m-t-30" style="padding: 10px 12px;"><?= trans("select_your_plan") ?></a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (!empty($userPlan) && $userPlan->is_unlimited_time != 1): ?>
                <div class="alert alert-info alert-large">
                    <strong><?= trans("warning"); ?>!</strong>&nbsp;&nbsp;<?= trans("msg_expired_plan"); ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif;
    if ($paymentSettings->cash_on_delivery_enabled == 1): ?>
        <div class="col-sm-5">
            <div class="box">
                <div class="box-header with-border">
                    <div class="left">
                        <h3 class="box-title"><?= trans("cash_on_delivery"); ?></h3><br>
                        <small><?= trans("cash_on_delivery_vendor_exp"); ?></small>
                    </div>
                </div>
                <div class="box-body">
                    <form action="<?= base_url('shop-settings-post'); ?>" method="post">
                        <?= csrf_field(); ?>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6 col-sm-12 col-custom-option">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="cash_on_delivery" value="1" id="cash_on_delivery_1" class="custom-control-input" <?= user()->cash_on_delivery == 1 ? 'checked' : ''; ?>>
                                        <label for="cash_on_delivery_1" class="custom-control-label"><?= trans("enable"); ?></label>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12 col-custom-option">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="cash_on_delivery" value="0" id="cash_on_delivery_2" class="custom-control-input" <?= user()->cash_on_delivery != 1 ? 'checked' : ''; ?>>
                                        <label for="cash_on_delivery_2" class="custom-control-label"><?= trans("disable"); ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group text-right">
                            <button type="submit" name="submit" value="cash_on_delivery" class="btn btn-md btn-success"><?= trans("save_changes") ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
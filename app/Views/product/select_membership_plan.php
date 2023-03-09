<div id="wrapper">
    <div class="container">
        <div class="row">
            <div id="content" class="col-12">
                <nav class="nav-breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb"></ol>
                </nav>
                <h1 class="page-title page-title-product m-b-15"><?= esc($title); ?></h1>
                <div class="form-add-product">
                    <div class="row justify-content-center">
                        <div class="col-12 col-md-12">
                            <div class="row">
                                <div class="col-12">
                                    <p class="start-selling-description text-muted"><?= trans("select_your_plan_exp"); ?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <?= view('partials/_messages'); ?>
                                </div>
                            </div>
                            <form action="<?= base_url('renew-membership-plan-post'); ?>" method="post">
                                <?= csrf_field(); ?>
                                <?php if (!empty($membershipPlans)): ?>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="price-box-container">
                                                <?php foreach ($membershipPlans as $plan):
                                                    $validPlan = 1;
                                                    if ($plan->is_unlimited_number_of_ads != 1 && $userAdsCount > $plan->number_of_ads) {
                                                        $validPlan = 0;
                                                    }
                                                    if ($plan->is_free == 1 && user()->is_used_free_plan == 1) {
                                                        $validPlan = 0;
                                                    } ?>
                                                    <div class="price-box">
                                                        <?php if ($plan->is_popular == 1): ?>
                                                            <div class="ribbon ribbon-top-right"><span><?= trans("popular"); ?></span></div>
                                                        <?php endif; ?>
                                                        <div class="price-box-inner">
                                                            <div class="pricing-name text-center">
                                                                <h4 class="name font-600"><?= getMembershipPlanName($plan->title_array, selectedLangId()); ?></h4>
                                                            </div>
                                                            <div class="plan-price text-center">
                                                                <h3><strong class="price font-700">
                                                                        <?php if ($plan->price == 0):
                                                                            echo trans("free");
                                                                        else:
                                                                            echo priceFormatted($plan->price, $paymentSettings->default_currency, true);
                                                                        endif; ?>
                                                                    </strong>
                                                                </h3>
                                                            </div>
                                                            <div class="price-features">
                                                                <?php $features = getMembershipPlanFeatures($plan->features_array, selectedLangId());
                                                                if (!empty($features)):
                                                                    foreach ($features as $feature):?>
                                                                        <p>
                                                                            <i class="icon-check-thin"></i>
                                                                            <?= esc($feature); ?>
                                                                        </p>
                                                                    <?php endforeach;
                                                                endif; ?>
                                                            </div>
                                                            <div class="text-center btn-plan-pricing-container">
                                                                <?php if ($validPlan == 1):
                                                                    if ($requestType == 'renew'): ?>
                                                                        <button type="submit" name="plan_id" value="<?= $plan->id; ?>" class="btn btn-md btn-custom"><?= trans("choose_plan"); ?></button>
                                                                    <?php elseif ($requestType == 'new'): ?>
                                                                        <a href="<?= generateUrl('start_selling'); ?>?plan=<?= $plan->id; ?>" class="btn btn-md btn-custom"><?= trans("choose_plan"); ?></a>
                                                                    <?php endif;
                                                                else: ?>
                                                                    <button type="button" class="btn btn-md btn-custom btn-pricing-table-disabled"><?= trans("choose_plan"); ?></button>
                                                                    <?php if ($plan->is_free == 1 && user()->is_used_free_plan == 1): ?>
                                                                        <span class="warning-pricing-table-plan text-muted"><?= trans("warning_plan_used"); ?></span>
                                                                    <?php else: ?>
                                                                        <span class="warning-pricing-table-plan text-muted"><?= trans("warning_cannot_choose_plan"); ?></span>
                                                                    <?php endif;
                                                                endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
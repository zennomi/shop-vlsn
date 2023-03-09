<div id="modalPricing" class="modal fade modal-pricing-table" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                <h4 class="modal-title"><?= trans("promote_your_product"); ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-6">
                        <div id="pricingPlan_1" class="price-box">
                            <div class="pricing-name text-center">
                                <h4 class="name"><?= trans("daily_plan"); ?></h4>
                            </div>
                            <div class="plan-price text-center">
                                <?php if ($paymentSettings->free_product_promotion == 1): ?>
                                    <h3><span class="price"><?= priceFormatted(0, $defaultCurrency->code); ?></span><span class="time">/<?= trans("day"); ?></span></h3>
                                <?php else: ?>
                                    <h3><span class="price"><?= priceFormatted($paymentSettings->price_per_day, $defaultCurrency->code); ?></span><span class="time">/<?= trans("day"); ?></span></h3>
                                <?php endif; ?>
                            </div>
                            <div class="price-features">
                                <p>
                                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-check" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M10.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.236.236 0 0 1 .02-.022z"/>
                                    </svg>
                                    <?= trans("featured_badge"); ?>
                                </p>
                                <p>
                                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-check" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M10.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.236.236 0 0 1 .02-.022z"/>
                                    </svg>
                                    <?= trans("appear_on_homepage"); ?>
                                </p>
                                <p>
                                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-check" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M10.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.236.236 0 0 1 .02-.022z"/>
                                    </svg>
                                    <?= trans("show_first_search_lists"); ?>
                                </p>
                            </div>
                            <div class="text-center">
                                <a href="javascript:void(0)" class="btn btn-md btn-pricing-table" data-pricing-plan="pricingPlan_1"><?= trans("choose_plan"); ?></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-6">
                        <div id="pricingPlan_2" class="price-box">
                            <div class="pricing-name text-center">
                                <h4 class="name"><?= trans("monthly_plan"); ?></h4>
                            </div>
                            <div class="plan-price text-center">
                                <?php if ($paymentSettings->free_product_promotion == 1): ?>
                                    <h3><span class="price"><?= priceFormatted(0, $defaultCurrency->code); ?></span><span class="time">/<?= trans("month"); ?></span></h3>
                                <?php else: ?>
                                    <h3><span class="price"><?= priceFormatted($paymentSettings->price_per_month, $defaultCurrency->code); ?></span><span class="time">/<?= trans("month"); ?></span></h3>
                                <?php endif; ?>
                            </div>
                            <div class="price-features">
                                <p>
                                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-check" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M10.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.236.236 0 0 1 .02-.022z"/>
                                    </svg>
                                    <?= trans("featured_badge"); ?>
                                </p>
                                <p>
                                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-check" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M10.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.236.236 0 0 1 .02-.022z"/>
                                    </svg>
                                    <?= trans("appear_on_homepage"); ?>
                                </p>
                                <p>
                                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-check" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M10.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.236.236 0 0 1 .02-.022z"/>
                                    </svg>
                                    <?= trans("show_first_search_lists"); ?>
                                </p>
                            </div>
                            <div class="text-center">
                                <a href="javascript:void(0)" class="btn btn-md btn-pricing-table" data-pricing-plan="pricingPlan_2"><?= trans("choose_plan"); ?></a>
                            </div>
                        </div>
                    </div>

                    <?php $pricePerDay = getPrice($paymentSettings->price_per_day, 'separator_format');
                    $pricePerMonth = getPrice($paymentSettings->price_per_month, 'separator_format'); ?>
                    <input type="hidden" id="pricePerDay" value="<?= $pricePerDay; ?>">
                    <input type="hidden" id="pricePerMonth" value="<?= $pricePerMonth; ?>">
                    <div class="col-sm-12 container-pricing-plan" id="container_pricingPlan_1">
                        <form action="<?= base_url('promote-product-post'); ?>" method="post" onkeypress="return event.keyCode != 13;">
                            <?= csrf_field(); ?>
                            <input type="hidden" class="pricing_product_id" name="product_id">
                            <input type="hidden" name="plan_type" value="daily">
                            <div class="form-group">
                                <label><?= trans("day_count"); ?></label>
                                <input type="number" id="pricing_day_count" name="day_count" class="form-control form-input price-input" min="1" value="1" maxlength="5" required>
                            </div>
                            <?php if ($paymentSettings->free_product_promotion != 1): ?>
                                <div class="form-group">
                                    <?php if ($defaultCurrency->symbol_direction == 'left'): ?>
                                        <strong class="price-total"><?= trans("total_amount"); ?>&nbsp;<?= $defaultCurrency->symbol; ?><span class="span-price-total-daily"><?= $pricePerDay; ?></span>&nbsp;<?= $defaultCurrency->code; ?></strong>
                                    <?php else: ?>
                                        <strong class="price-total"><?= trans("total_amount"); ?>&nbsp;<span class="span-price-total-daily"><?= $pricePerDay; ?></span><?= $defaultCurrency->symbol; ?>&nbsp;<?= $defaultCurrency->code; ?></strong>
                                    <?php endif; ?>
                                </div>
                                <div class="form-group m-0">
                                    <button type="submit" class="btn btn-lg btn-success"><?= trans("continue_to_checkout"); ?></button>
                                </div>
                            <?php else: ?>
                                <div class="form-group m-0">
                                    <button type="submit" class="btn btn-lg btn-success"><?= trans("submit"); ?></button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>

                    <div class="col-sm-12 container-pricing-plan" id="container_pricingPlan_2">
                        <form action="<?= base_url('promote-product-post'); ?>" method="post" onkeypress="return event.keyCode != 13;">
                            <?= csrf_field(); ?>
                            <input type="hidden" class="pricing_product_id" name="product_id">
                            <input type="hidden" name="plan_type" value="monthly">
                            <div class="form-group">
                                <label><?= trans("month_count"); ?></label>
                                <input type="number" id="pricing_month_count" name="month_count" class="form-control form-input price-input" min="1" value="1" required>
                            </div>
                            <?php if ($paymentSettings->free_product_promotion != 1): ?>
                                <div class="form-group">
                                    <?php if ($defaultCurrency->symbol_direction == "left"): ?>
                                        <strong class="price-total"><?= trans("total_amount"); ?>&nbsp;<?= $defaultCurrency->symbol; ?><span class="span-price-total-monthly"><?= $pricePerMonth; ?></span>&nbsp;<?= $defaultCurrency->code; ?></strong>
                                    <?php else: ?>
                                        <strong class="price-total"><?= trans("total_amount"); ?>&nbsp;<span class="span-price-total-monthly"><?= $pricePerMonth; ?></span><?= $defaultCurrency->symbol; ?>&nbsp;<?= $defaultCurrency->code; ?></strong>
                                    <?php endif; ?>
                                </div>
                                <div class="form-group m-0">
                                    <button type="submit" class="btn btn-lg btn-success"><?= trans("continue_to_checkout"); ?></button>
                                </div>
                            <?php else: ?>
                                <div class="form-group m-0">
                                    <button type="submit" class="btn btn-lg btn-success"><?= trans("submit"); ?></button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/vendor/jquery-number/jquery.number.min.js'); ?>"></script>

<script>
    $(document).on('click', '.btn-pricing-table', function () {
        var pricingPlan = $(this).attr("data-pricing-plan");
        $('.price-box').removeClass('selected-plan');
        $('#' + pricingPlan).addClass('selected-plan');
        $('.container-pricing-plan').hide();
        $('#container_' + pricingPlan).show();
    });

    $("#pricing_day_count").on("input keypress paste change", function () {
        var dayCount = $("#pricing_day_count").val();
        if (dayCount > 1440) {
            dayCount = 1440;
            $("#pricing_day_count").val('1440');
        }
        var pricePerDay = '<?= getPrice($paymentSettings->price_per_day, 'decimal'); ?>';
        var calculated = dayCount * pricePerDay;
        if (!Number.isInteger(calculated)) {
            calculated = calculated.toFixed(2);
        }
        <?php if($baseVars->thousandsSeparator == ','): ?>
        var calculatedFormatted = $.number(calculated, 2, ',', '.');
        <?php else: ?>
        var calculatedFormatted = $.number(calculated, 2, '.', ',');
        <?php endif; ?>
        $(".span-price-total-daily").text(calculatedFormatted);
    });

    $("#pricing_month_count").on("input keypress paste change", function () {
        var monthCount = $("#pricing_month_count").val();
        if (monthCount > 48) {
            monthCount = 48;
            $("#pricing_month_count").val('48');
        }
        var pricePerMonth = '<?= getPrice($paymentSettings->price_per_month, 'decimal'); ?>';
        var calculated = monthCount * pricePerMonth;
        if (!Number.isInteger(calculated)) {
            calculated = calculated.toFixed(2);
        }
        <?php if($baseVars->thousandsSeparator == ','): ?>
        var calculatedFormatted = $.number(calculated, 2, ',', '.');
        <?php else: ?>
        var calculatedFormatted = $.number(calculated, 2, '.', ',');
        <?php endif; ?>
        $(".span-price-total-monthly").text(calculatedFormatted);
    });
</script>
<?php if ($product->listing_type == 'sell_on_site' || $product->listing_type == 'license_key'): ?>
    <div class="form-box form-box-price">
        <div class="form-box-head">
            <h4 class="title"><?= trans("product_price"); ?></h4>
        </div>
        <div class="form-box-body">
            <div id="price_input_container" class="form-group">
                <div class="row">
                    <div class="col-xs-12 col-sm-4 m-b-sm-15">
                        <label class="font-600"><?= trans("price"); ?></label>
                        <div class="input-group">
                            <span class="input-group-addon"><?= $defaultCurrency->symbol; ?></span>
                            <input type="hidden" name="currency" value="<?= esc($defaultCurrency->code); ?>">
                            <input type="text" name="price" id="product_price_input" aria-describedby="basic-addon1" class="form-control form-input price-input" value="<?= $product->price != 0 ? getPrice($product->price, 'input') : ''; ?>" placeholder="<?= $baseVars->inputInitialPrice; ?>" onpaste="return false;" maxlength="32" <?= $product->is_free_product != 1 ? 'required' : ''; ?>>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-4 m-b-sm-15">
                        <div class="row align-items-center">
                            <div class="col-sm-12">
                                <label class="font-600"><?= trans("discount_rate"); ?></label>
                                <div id="discount_input_container" class="<?= $product->discount_rate == 0 ? 'display-none' : ''; ?>">
                                    <div class="input-group">
                                        <span class="input-group-addon">%</span>
                                        <input type="hidden" name="currency" value="<?= $paymentSettings->default_currency; ?>">
                                        <input type="number" name="discount_rate" id="input_discount_rate" aria-describedby="basic-addon-discount" class="form-control form-input" value="<?= $product->discount_rate; ?>" min="0" max="99">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 m-t-10">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="discount_rate" id="checkbox_discount_rate" <?= $product->discount_rate == 0 ? 'checked' : ''; ?>>
                                    <label for="checkbox_discount_rate" class="custom-control-label"><?= trans("no_discount"); ?></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if ($generalSettings->vat_status == 1): ?>
                        <div class="col-xs-12 col-sm-4">
                            <div class="row align-items-center">
                                <div class="col-sm-12">
                                    <label class="font-600"><?= trans("vat"); ?><small>&nbsp;(<?= trans("vat_exp"); ?>)</small></label>
                                    <div id="vat_input_container" class="<?= $product->vat_rate == 0 ? 'display-none' : ''; ?>">
                                        <div class="input-group">
                                            <span class="input-group-addon">%</span>
                                            <input type="hidden" name="currency" value="<?= $paymentSettings->default_currency; ?>">
                                            <input type="number" name="vat_rate" id="input_vat_rate" aria-describedby="basic-addon-vat" class="form-control form-input" value="<?= $product->vat_rate; ?>" min="0" max="100" step="0.01">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 m-t-10">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="vat_included" id="checkbox_vat_included" <?= $product->vat_rate == 0 ? 'checked' : ''; ?>>
                                        <label for="checkbox_vat_included" class="custom-control-label"><?= trans("vat_included"); ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="col-sm-12 m-t-30">
                        <p class="calculated-price">
                            <strong><?= trans("calculated_price"); ?> (<?= $defaultCurrency->symbol; ?>):&nbsp;&nbsp;</strong>
                            <b id="calculated_amount" class="earned-price">
                                <?php $earnedAmount = calculateProductPrice($product->price, $product->discount_rate);
                                if (!empty($earnedAmount)):
                                    $earnedAmount = number_format($earnedAmount, 2, '.', '');
                                    echo getPrice($earnedAmount, 'input');
                                endif; ?>
                            </b>
                        </p>
                        <p class="calculated-price calculated_vat_container <?= $product->vat_rate == 0 ? 'display-none' : ''; ?>">
                            <strong><?= trans("vat"); ?> (<?= $defaultCurrency->symbol; ?>):&nbsp;&nbsp;</strong>
                            <b id="vat_amount" class="earned-price">
                                <?php $earnedAmount = calculateProductVat($product);
                                if (!empty($earnedAmount)):
                                    $earnedAmount = number_format($earnedAmount, 2, '.', '');
                                    echo getPrice($earnedAmount, 'input');
                                endif; ?>
                            </b>
                        </p>
                        <p class="calculated-price">
                            <strong><?= trans("you_will_earn"); ?> (<?= $defaultCurrency->symbol; ?>):&nbsp;&nbsp;</strong>
                            <b id="earned_amount" class="earned-price">
                                <?php $earnedAmount = 0;
                                if (!empty($product)) {
                                    $price = calculateProductPrice($product->price, $product->discount_rate);
                                    $earnedAmount = $price - (($price * $generalSettings->commission_rate) / 100);
                                }
                                if (!empty($earnedAmount)):
                                    $earnedAmount = $earnedAmount + calculateProductVat($product);
                                    $earnedAmount = number_format($earnedAmount, 2, '.', '');
                                endif;
                                echo getPrice($earnedAmount, 'input'); ?>
                            </b>
                            <?php if ($product->product_type != 'digital'): ?>
                                &nbsp;&nbsp;&nbsp;<b>+&nbsp;&nbsp;&nbsp;<?= trans("shipping_cost"); ?></b>&nbsp;&nbsp;
                            <?php endif; ?>
                            <small> (<?= trans("commission_rate"); ?>:&nbsp;&nbsp;<?= $generalSettings->commission_rate; ?>%)</small>
                        </p>
                    </div>
                </div>
            </div>
            <?php if ($product->product_type == 'digital'): ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="is_free_product" id="checkbox_free_product" <?= $product->is_free_product == 1 ? 'checked' : ''; ?>>
                            <label for="checkbox_free_product" class="custom-control-label text-danger"><?= trans("free_product"); ?></label>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php elseif ($product->listing_type == 'ordinary_listing'):
    if ($productSettings->classified_price == 1): ?>
        <div class="form-box">
            <div class="form-box-head">
                <h4 class="title"><?= trans('price'); ?></h4>
            </div>
            <div class="form-box-body">
                <div class="form-group">
                    <div class="row">
                        <?php if ($paymentSettings->allow_all_currencies_for_classied == 1): ?>
                            <div class="col-xs-12 col-sm-4 m-b-sm-15">
                                <select name="currency" class="form-control custom-select" required>
                                    <?php if (!empty($currencies)):
                                        foreach ($currencies as $key => $value):?>
                                            <option value="<?= $key; ?>" <?= $key == $product->currency ? 'selected' : ''; ?>><?= esc($value->name) . ' (' . $value->symbol . ')'; ?></option>
                                        <?php endforeach;
                                    endif; ?>
                                </select>
                            </div>
                            <div class="col-xs-12 col-sm-4 m-b-sm-15">
                                <input type="text" name="price" class="form-control form-input price-input" value="<?= $product->price != 0 ? getPrice($product->price, 'input') : ''; ?>" placeholder="<?= $baseVars->inputInitialPrice; ?>" onpaste="return false;" maxlength="32" <?= $productSettings->classified_price_required == 1 ? 'required' : ''; ?>>
                            </div>
                        <?php else: ?>
                            <div class="col-xs-12 col-sm-6 m-b-sm-15">
                                <div class="input-group">
                                    <span class="input-group-addon"><?= $defaultCurrency->symbol; ?></span>
                                    <input type="hidden" name="currency" value="<?= $defaultCurrency->code; ?>">
                                    <input type="text" name="price" id="product_price_input" aria-describedby="basic-addon2" class="form-control form-input price-input" value="<?= $product->price != 0 ? getPrice($product->price, 'input') : ''; ?>" placeholder="<?= $baseVars->inputInitialPrice; ?>" onpaste="return false;" maxlength="32" <?= $productSettings->classified_price_required == 1 ? 'required' : ''; ?>>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif;
elseif ($product->listing_type == 'bidding'): ?>
    <input type="hidden" name="currency" value="<?= $paymentSettings->default_currency; ?>">
<?php endif; ?>

<script>
    $(document).on('click', '#checkbox_free_product', function () {
        if ($(this).is(':checked')) {
            $('#price_input_container').hide();
            $(".price-input").prop('required', false);
        } else {
            $('#price_input_container').show();
            $(".price-input").prop('required', true);
        }
    });
</script>
<?php if ($product->is_free_product == 1): ?>
    <style>
        #price_input_container {
            display: none;;
        }
    </style>
<?php endif;
if ($product->listing_type == 'sell_on_site' || $product->listing_type == 'license_key'): ?>
    <script>
        //calculate product earned value
        $(document).on("input keyup paste change", "#product_price_input", function () {
            calculateEarnAmount();
        });
        $(document).on("input keyup paste change", "#input_discount_rate", function () {
            var val = parseInt($(this).val());
            if (val == '' || val == null || isNaN(val)) {
                val = 0;
            }
            if (val > 99) {
                val = 99;
            }
            if ($(this).val() < 0) {
                val = 0;
            }
            $(this).val(val);
            calculateEarnAmount();
        });
        $(document).on("input keyup paste change", "#input_vat_rate", function () {
            val = document.getElementById("input_vat_rate").value;
            if (isNaN(parseFloat(val)) || val < 0 || val > 100) {
                $(this).val('');
            }
            calculateEarnAmount();
        });

        function calculateEarnAmount() {
            var inputPrice = $("#product_price_input").val();
            var discount = 0;
            var vat = 0;
            if ($('#input_discount_rate').val() != '' && $('#input_discount_rate').val() != null) {
                discount = $("#input_discount_rate").val();
            }
            if ($('#input_vat_rate').val() != '' && $('#input_vat_rate').val() != null) {
                vat = $("#input_vat_rate").val();
            }
            inputPrice = inputPrice.replace(',', '.');
            var price = parseFloat(inputPrice);
            var commissionRate = parseInt(MdsConfig.commissionRate);
            //calculate
            var calculatedAmount = 0;
            var vatAmount = 0;
            var earnedAmount = 0;
            if (!Number.isNaN(price)) {
                calculatedAmount = price - ((price * discount) / 100);
                vatAmount = (calculatedAmount * vat) / 100;
                earnedAmount = calculatedAmount - ((calculatedAmount * commissionRate) / 100);
                earnedAmount = earnedAmount + vatAmount;
                earnedAmount = earnedAmount.toFixed(2);
                calculatedAmount = calculatedAmount.toFixed(2);
                vatAmount = vatAmount.toFixed(2);
                if (MdsConfig.thousandsSeparator == ',') {
                    calculatedAmount = calculatedAmount.replace('.', ',');
                    vatAmount = vatAmount.replace('.', ',');
                    earnedAmount = earnedAmount.replace('.', ',');
                }
            } else {
                calculatedAmount = '0' + MdsConfig.thousandsSeparator + '00';
                vatAmount = '0' + MdsConfig.thousandsSeparator + '00';
                earnedAmount = '0' + MdsConfig.thousandsSeparator + '00';
            }
            $("#calculated_amount").html(calculatedAmount);
            $("#vat_amount").html(vatAmount);
            $("#earned_amount").html(earnedAmount);
        }
    </script>
<?php endif; ?>
<script>
    $('#checkbox_discount_rate').change(function () {
        if (!this.checked) {
            $("#discount_input_container").show();
        } else {
            $('#input_discount_rate').val("0");
            $('#input_discount_rate').change();
            $("#discount_input_container").hide();
        }
    });
    $('#checkbox_vat_included').change(function () {
        if (!this.checked) {
            $("#vat_input_container").show();
            $(".calculated_vat_container").show();
        } else {
            $('#input_vat_rate').val("0");
            $('#input_vat_rate').change();
            $("#vat_input_container").hide();
            $(".calculated_vat_container").hide();
        }
    });
</script>

<?php $session = \Config\Services::session();
$selectedCategories = array();
if (!empty(old("category_ids"))) {
    $selectedCategories = explode(',', old("category_ids"));
}

$selectedProducts = array();
if ($session->getFlashdata('selectedProductsIds')) {
    $selectedProducts = $session->getFlashdata('selectedProductsIds');
}

function printSubCategories($categories, $categoryIds, $selectedCategories, $selectedProducts)
{
    $html = '<ul>';
    foreach ($categories as $category):
        if (in_array($category->id, $categoryIds)):
            $html .= "<li><div class='category-title'>";
            if (in_array($category->id, $selectedCategories)):
                $html .= "<input type='checkbox' name='category_id[]' id='cb_category_" . $category->id . "' value='" . $category->id . "' data-id='" . $category->id . "' data-parent='" . $category->parent_tree . "' class='category-checkbox' checked>";
            else:
                $html .= "<input type='checkbox' name='category_id[]' id='cb_category_" . $category->id . "' value='" . $category->id . "' data-id='" . $category->id . "' data-parent='" . $category->parent_tree . "' class='category-checkbox'>";
            endif;
            $html .= "&nbsp;&nbsp;<label for='cb_category_" . $category->id . "' class='lbl-cat'>" . esc($category->name) . "</label></div></li>";
            $products = getCouponProductsByCategory(user()->id, $category->id);
            if (!empty($products)):
                $html .= "<div class='items'>";
                foreach ($products as $product):
                    $html .= "<div class='item'>";
                    if (in_array($product->id, $selectedProducts)):
                        $html .= "<input type='checkbox' name='product_id[]' id='cb_product_" . $product->id . "' value='" . $product->id . "' data-parent='" . $category->parent_tree . "," . $category->id . "' checked>";
                    else:
                        $html .= "<input type='checkbox' name='product_id[]' id='cb_product_" . $product->id . "' value='" . $product->id . "' data-parent='" . $category->parent_tree . "," . $category->id . "'>";
                    endif;
                    $html .= "&nbsp;&nbsp;<label for='cb_product_" . $product->id . "'>" . esc($product->title) . "</label>";
                    $html .= "</div>";
                endforeach;
                $html .= "</div>";
            endif;
            $subCategories = getSubCategories($category->id);
            if (!empty($subCategories)):
                $html .= printSubCategories($subCategories, $categoryIds, $selectedCategories, $selectedProducts);
            endif;
        endif;
    endforeach;
    $html .= '</ul>';
    return $html;
} ?>
<div class="row">
    <div class="col-sm-12">
        <?= view('dashboard/includes/_messages'); ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= esc($title); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= generateDashUrl('coupons'); ?>" class="btn btn-success btn-add-new">
                        <i class="fa fa-list-ul"></i>&nbsp;&nbsp;<?= trans("coupons"); ?>
                    </a>
                </div>
            </div>
            <div class="box-body">
                <form action="<?= base_url('add-coupon-post'); ?>" method="post">
                    <?= csrf_field(); ?>
                    <div class="form-group">
                        <label class="control-label"><?= trans("coupon_code"); ?>&nbsp;&nbsp;<small>(<?= trans("exp_special_characters"); ?> E.g: #, *, % ..)</small></label>
                        <div class="position-relative">
                            <input type="text" name="coupon_code" id="input_coupon_code" value="<?= old("coupon_code"); ?>" class="form-control form-input" placeholder="<?= trans("coupon_code"); ?>" maxlength="49" required>
                            <button type="button" class="btn btn-default btn-generate-sku" onclick="$('#input_coupon_code').val(Math.random().toString(36).substr(2,8).toUpperCase());"><?= trans("generate"); ?></button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans("discount_rate"); ?></label>
                        <div class="input-group">
                            <span class="input-group-addon">%</span>
                            <input type="number" name="discount_rate" id="input_discount_rate" value="<?= old("discount_rate"); ?>" aria-describedby="basic-addon-discount" class="form-control form-input" placeholder="E.g: 5" min="0" max="99" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans("number_of_coupons"); ?>&nbsp;<small>(<?= trans("number_of_coupons_exp"); ?>)</small></label>
                        <input type="number" name="coupon_count" value="<?= old("coupon_count"); ?>" class="form-control form-input" placeholder="E.g: 100" min="1" max="99999999" required>
                    </div>
                    <div class="form-group">
                        <label class="font-600"><?= trans("minimum_order_amount"); ?>&nbsp;<small>(<?= trans("coupon_minimum_cart_total_exp"); ?>)</small></label>
                        <div class="input-group">
                            <span class="input-group-addon"><?= $defaultCurrency->symbol; ?></span>
                            <input type="hidden" name="currency" value="<?= $defaultCurrency->code; ?>">
                            <input type="text" name="minimum_order_amount" id="product_price_input" value="<?= old("minimum_order_amount"); ?>" aria-describedby="basic-addon1" class="form-control form-input price-input validate-price-input" placeholder="<?= $baseVars->inputInitialPrice; ?>" onpaste="return false;" maxlength="32">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                <label><?= trans("coupon_usage_type"); ?></label>
                            </div>
                            <div class="col-md-6 col-sm-12 col-custom-option">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="usage_type" value="single" id="usage_type_1" class="custom-control-input" <?= old("usage_type") != 'multiple' ? 'checked' : ''; ?>>
                                    <label for="usage_type_1" class="custom-control-label"><?= trans("coupon_usage_type_1"); ?></label>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12 col-custom-option">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="usage_type" value="multiple" id="usage_type_2" class="custom-control-input" <?= old('usage_type') == 'multiple' ? 'checked' : ''; ?>>
                                    <label for="usage_type_2" class="custom-control-label"><?= trans("coupon_usage_type_2"); ?></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12 max-600">
                                <label><?= trans("expiry_date"); ?></label>
                                <div class='input-group date' id='datetimepicker'>
                                    <input type='text' class="form-control" name="expiry_date" value="<?= old("expiry_date"); ?>" placeholder="<?= trans("expiry_date"); ?>" required>
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><?= trans("products"); ?></label>
                        <div class="category-structure-list-container">
                            <?php if (!empty($categories) && !empty($categories[0])):
                                foreach ($categories as $category):
                                    if ($category->parent_id == 0):?>
                                        <ul class="category-structure-list">
                                            <li>
                                                <div class="category-title">
                                                    <input type='checkbox' name='category_id[]' value='<?= $category->id; ?>' id='cb_category_<?= $category->id; ?>' data-id='<?= $category->id; ?>' data-parent="<?= $category->parent_tree; ?>" class='category-checkbox' <?= in_array($category->id, $selectedCategories) ? 'checked' : ''; ?>>&nbsp;&nbsp;<label for="cb_category_<?= $category->id; ?>" class="lbl-cat">
                                                        <?= esc($category->name); ?></label>
                                                </div>
                                                <?php $products = getCouponProductsByCategory(user()->id, $category->id);
                                                if (!empty($products)):?>
                                                    <div class="items">
                                                        <?php foreach ($products as $product): ?>
                                                            <div class="item">
                                                                <input type='checkbox' name='product_id[]' id="cb_product_<?= $product->id; ?>" value='<?= $product->id; ?>' <?= in_array($product->id, $selectedProducts) ? 'checked' : ''; ?> data-parent="<?= $category->id; ?>">&nbsp;&nbsp;<label for="cb_product_<?= $product->id; ?>"><?= esc($product->title); ?></label>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </li>
                                            <?php $subCategories = getSubCategories($category->id);
                                            echo printSubCategories($subCategories, $categoryIds, $selectedCategories, $selectedProducts); ?>
                                        </ul>
                                    <?php endif;
                                endforeach;
                            endif; ?>
                        </div>
                    </div>
                    <div class="form-group text-right">
                        <button type="submit" name="submit" value="update" class="btn btn-md btn-success"><?= trans("add_coupon") ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css'); ?>">
<script src="<?= base_url('assets/vendor/bootstrap-datetimepicker/moment.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js'); ?>"></script>
<script>
    $(function () {
        $('#datetimepicker').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss'
        });
    });
    $(document).on('change', '.category-checkbox', function () {
        var id = $(this).attr('data-id');
        var is_all_checked = true;
        var is_parent_checked = true;
        if (!$(this).is(":checked")) {
            is_parent_checked = false;
        }
        $("input:checkbox").each(function () {
            var data = $(this).attr('data-parent');
            if (data != '' && data != undefined) {
                var array = data.split(',');
                if (jQuery.inArray(id, array) != -1) {
                    if (!$(this).is(":checked")) {
                        is_all_checked = false;
                    }
                }
            }
        });
        $("input:checkbox").each(function () {
            var data = $(this).attr('data-parent');
            if (data != '' && data != undefined) {
                var array = data.split(',');
                if (jQuery.inArray(id, array) != -1) {
                    if (is_all_checked == true) {
                        $(this).prop('checked', false);
                    } else {
                        $(this).prop('checked', true);
                    }
                    //uncheck if parent unchecked
                    if (is_parent_checked == false) {
                        $(this).prop('checked', false);
                    } else {
                        $(this).prop('checked', true);
                    }
                }
            }
        });
    });
</script>
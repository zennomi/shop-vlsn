<?php if (!empty($method)):
    $optionUniqueId = $method->id;
    $selectedOption = $method->method_type; ?>
    <div id="row_shipping_method_<?= $optionUniqueId; ?>" class="row">
        <div class="col-sm-12">
            <input type="hidden" name="option_unique_id[]" value="<?= $optionUniqueId; ?>">
            <input type="hidden" name="method_type_<?= $optionUniqueId; ?>" value="<?= $selectedOption; ?>">
            <input type="hidden" name="method_operation_<?= $optionUniqueId; ?>" value="edit">
            <?php if ($selectedOption == 'flat_rate'): ?>
                <div class="response-shipping-method">
                    <span class="title"><?= @parseSerializedNameArray($method->name_array, selectedLangId()); ?></span>
                    <div id="modalMethod<?= $optionUniqueId; ?>" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                                    <h4 class="modal-title"><?= trans($selectedOption); ?></h4>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group m-b-10">
                                        <label class="control-label"><?= trans("method_name"); ?></label>
                                        <?php foreach ($activeLanguages as $language): ?>
                                            <input type="text" name="method_name_<?= $optionUniqueId; ?>_lang_<?= $language->id; ?>" class="form-control form-input m-b-5" value="<?= @parseSerializedNameArray($method->name_array, $language->id); ?>" placeholder="<?= esc($language->name); ?>" maxlength="255">
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="form-group">
                                        <label><?= trans("cost_calculation_type"); ?></label>
                                        <select name="flat_rate_cost_calculation_type_<?= $optionUniqueId; ?>" class="form-control custom-select">
                                            <option value="each_product" <?= $method->flat_rate_cost_calculation_type == 'each_product' ? 'selected' : ''; ?>><?= trans("charge_shipping_for_each_product"); ?></option>
                                            <option value="each_different_product" <?= $method->flat_rate_cost_calculation_type == 'each_different_product' ? 'selected' : ''; ?>><?= trans("charge_shipping_for_each_different_product"); ?></option>
                                            <option value="cart_total" <?= $method->flat_rate_cost_calculation_type == 'cart_total' ? 'selected' : ''; ?>><?= trans("fixed_shipping_cost_for_cart_total"); ?></option>
                                        </select>
                                    </div>
                                    <div class="form-group m-b-10">
                                        <label class="control-label"><?= trans("cost"); ?></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><?= $defaultCurrency->symbol; ?></span>
                                            <input type="text" name="flat_rate_cost_<?= $optionUniqueId; ?>" class="form-control form-input price-input" value="<?= getPrice($method->flat_rate_cost, 'input'); ?>" placeholder="<?= $baseVars->inputInitialPrice; ?>" maxlength="19">
                                        </div>
                                    </div>
                                    <?php if (!empty($shippingClasses)): ?>
                                        <div class="form-group">
                                            <label><?= trans("shipping_class_costs"); ?></label>
                                            <?php foreach ($shippingClasses as $shippingClass):
                                                $classCost = getShippingClassCostByMethod($method->flat_rate_class_costs_array, $shippingClass->id);
                                                if (!empty($classCost)):
                                                    $classCost = getPrice($classCost, 'input');
                                                endif; ?>
                                                <div class="input-group m-b-5">
                                                    <span class="input-group-addon"><?= $defaultCurrency->symbol; ?></span>
                                                    <input type="text" name="flat_rate_cost_<?= $optionUniqueId; ?>_class_<?= $shippingClass->id; ?>" class="form-control form-input price-input" value="<?= $classCost; ?>" placeholder="<?= @parseSerializedNameArray($shippingClass->name_array, selectedLangId()); ?>" maxlength="19">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-12 col-xs-12">
                                                <label><?= trans("status"); ?></label>
                                            </div>
                                            <div class="col-md-6 col-sm-12 col-custom-option">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="status_<?= $optionUniqueId; ?>" value="1" id="status_<?= $optionUniqueId; ?>_1" class="custom-control-input" <?= $method->status == 1 ? 'checked' : ''; ?>>
                                                    <label for="status_<?= $optionUniqueId; ?>_1" class="custom-control-label"><?= trans("enable"); ?></label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12 col-custom-option">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="status_<?= $optionUniqueId; ?>" value="0" id="status_<?= $optionUniqueId; ?>_2" class="custom-control-input" <?= $method->status != 1 ? 'checked' : ''; ?>>
                                                    <label for="status_<?= $optionUniqueId; ?>_2" class="custom-control-label"><?= trans("disable"); ?></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= trans("close"); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn-group btn-group-option">
                        <a href="javascript:void(0)" class="btn btn-sm btn-default btn-edit" data-toggle="modal" data-target="#modalMethod<?= $optionUniqueId; ?>"><span data-toggle="tooltip" title="<?= trans('edit'); ?>"><i class="fa fa-edit"></i></span></a>
                        <a href="javascript:void(0)" class="btn btn-sm btn-default" data-toggle="tooltip" title="<?= trans('delete'); ?>" onclick='deleteShippingMethod("<?= $optionUniqueId; ?>","<?= trans("confirm_delete", true); ?>");'><i class="fa fa-trash-o"></i></a>
                    </div>
                </div>
            <?php elseif ($selectedOption == 'local_pickup'): ?>
                <div class="response-shipping-method">
                    <span class="title"><?= @parseSerializedNameArray($method->name_array, selectedLangId()); ?></span>
                    <div id="modalMethod<?= $optionUniqueId; ?>" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                                    <h4 class="modal-title"><?= trans($selectedOption); ?></h4>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group m-b-10">
                                        <label class="control-label"><?= trans("method_name"); ?></label>
                                        <?php foreach ($activeLanguages as $language): ?>
                                            <input type="text" name="method_name_<?= $optionUniqueId; ?>_lang_<?= $language->id; ?>" class="form-control form-input m-b-5" value="<?= @parseSerializedNameArray($method->name_array, $language->id); ?>" placeholder="<?= esc($language->name); ?>" maxlength="255">
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="form-group m-b-10">
                                        <label class="control-label"><?= trans("cost"); ?></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><?= $defaultCurrency->symbol; ?></span>
                                            <input type="text" name="local_pickup_cost_<?= $optionUniqueId; ?>" class="form-control form-input price-input" value="<?= getPrice($method->local_pickup_cost, 'input'); ?>" placeholder="<?= $baseVars->inputInitialPrice; ?>" maxlength="19">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-12 col-xs-12">
                                                <label><?= trans("status"); ?></label>
                                            </div>
                                            <div class="col-md-6 col-sm-12 col-custom-option">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="status_<?= $optionUniqueId; ?>" value="1" id="status_<?= $optionUniqueId; ?>_1" class="custom-control-input" <?= $method->status == 1 ? 'checked' : ''; ?>>
                                                    <label for="status_<?= $optionUniqueId; ?>_1" class="custom-control-label"><?= trans("enable"); ?></label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12 col-custom-option">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="status_<?= $optionUniqueId; ?>" value="0" id="status_<?= $optionUniqueId; ?>_2" class="custom-control-input" <?= $method->status != 1 ? 'checked' : ''; ?>>
                                                    <label for="status_<?= $optionUniqueId; ?>_2" class="custom-control-label"><?= trans("disable"); ?></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= trans("close"); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn-group btn-group-option">
                        <a href="javascript:void(0)" class="btn btn-sm btn-default btn-edit" data-toggle="modal" data-target="#modalMethod<?= $optionUniqueId; ?>"><span data-toggle="tooltip" title="<?= trans('edit'); ?>"><i class="fa fa-edit"></i></span></a>
                        <a href="javascript:void(0)" class="btn btn-sm btn-default" data-toggle="tooltip" title="<?= trans('delete'); ?>" onclick='deleteShippingMethod("<?= $optionUniqueId; ?>","<?= trans("confirm_delete", true); ?>");'><i class="fa fa-trash-o"></i></a>
                    </div>
                </div>
            <?php elseif ($selectedOption == "free_shipping"): ?>
                <div class="response-shipping-method">
                    <span class="title"><?= @parseSerializedNameArray($method->name_array, selectedLangId()); ?></span>
                    <div id="modalMethod<?= $optionUniqueId; ?>" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                                    <h4 class="modal-title"><?= trans($selectedOption); ?></h4>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group m-b-10">
                                        <label class="control-label"><?= trans("method_name"); ?></label>
                                        <?php foreach ($activeLanguages as $language): ?>
                                            <input type="text" name="method_name_<?= $optionUniqueId; ?>_lang_<?= $language->id; ?>" class="form-control form-input m-b-5" value="<?= @parseSerializedNameArray($method->name_array, $language->id); ?>" placeholder="<?= esc($language->name); ?>" maxlength="255">
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="form-group m-b-10">
                                        <label class="control-label"><?= trans("minimum_order_amount"); ?></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><?= $defaultCurrency->symbol; ?></span>
                                            <input type="text" name="free_shipping_min_amount_<?= $optionUniqueId; ?>" class="form-control form-input price-input" value="<?= getPrice($method->free_shipping_min_amount, 'input'); ?>" placeholder="<?= $baseVars->inputInitialPrice; ?>" maxlength="19">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-12 col-xs-12">
                                                <label><?= trans("status"); ?></label>
                                            </div>
                                            <div class="col-md-6 col-sm-12 col-custom-option">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="status_<?= $optionUniqueId; ?>" value="1" id="status_<?= $optionUniqueId; ?>_1" class="custom-control-input" <?= $method->status == 1 ? 'checked' : ''; ?>>
                                                    <label for="status_<?= $optionUniqueId; ?>_1" class="custom-control-label"><?= trans("enable"); ?></label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12 col-custom-option">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="status_<?= $optionUniqueId; ?>" value="0" id="status_<?= $optionUniqueId; ?>_2" class="custom-control-input" <?= $method->status != 1 ? 'checked' : ''; ?>>
                                                    <label for="status_<?= $optionUniqueId; ?>_2" class="custom-control-label"><?= trans("disable"); ?></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= trans("close"); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn-group btn-group-option">
                        <a href="javascript:void(0)" class="btn btn-sm btn-default btn-edit" data-toggle="modal" data-target="#modalMethod<?= $optionUniqueId; ?>"><span data-toggle="tooltip" title="<?= trans('edit'); ?>"><i class="fa fa-edit"></i></span></a>
                        <a href="javascript:void(0)" class="btn btn-sm btn-default" data-toggle="tooltip" title="<?= trans('delete'); ?>" onclick='deleteShippingMethod("<?= $optionUniqueId; ?>","<?= trans("confirm_delete", true); ?>");'><i class="fa fa-trash-o"></i></a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
<?php $fieldModel = new \App\Models\FieldModel();
if (!empty($customFields)):
    foreach ($customFields as $customField):
        if (!empty($customField)):
            $customFieldName = @parseSerializedNameArray($customField->name_array, selectedLangId());
            if ($customField->field_type == 'text'):
                $inputValue = $fieldModel->getProductCustomFieldInputValue($customField->id, $product->id); ?>
                <div class="col-sm-12 <?= $customField->row_width == 'half' ? "col-sm-6" : "col-sm-12"; ?> col-custom-field">
                    <label><?= esc($customFieldName); ?></label>
                    <input type="text" name="field_<?= $customField->id; ?>" class="form-control form-input" value="<?= esc($inputValue); ?>" placeholder="<?= esc($customFieldName); ?>" <?= $customField->is_required == 1 ? 'required' : ''; ?>>
                </div>
            <?php elseif ($customField->field_type == 'number'):
                $inputValue = $fieldModel->getProductCustomFieldInputValue($customField->id, $product->id); ?>
                <div class="col-sm-12 <?= $customField->row_width == 'half' ? "col-sm-6" : "col-sm-12"; ?> col-custom-field">
                    <label><?= esc($customFieldName); ?></label>
                    <input type="number" name="field_<?= $customField->id; ?>" class="form-control form-input" value="<?= esc($inputValue); ?>" placeholder="<?= esc($customFieldName); ?>" min="0" max="999999999" <?= $customField->is_required == 1 ? 'required' : ''; ?>>
                </div>
            <?php elseif ($customField->field_type == 'textarea'):
                $inputValue = $fieldModel->getProductCustomFieldInputValue($customField->id, $product->id); ?>
                <div class="col-sm-12 <?= $customField->row_width == 'half' ? "col-sm-6" : "col-sm-12"; ?> col-custom-field">
                    <label><?= esc($customFieldName); ?></label>
                    <textarea class="form-control form-input custom-field-input" name="field_<?= $customField->id; ?>" placeholder="<?= esc($customFieldName); ?>" <?= $customField->is_required == 1 ? 'required' : ''; ?>><?= @esc($inputValue); ?></textarea>
                </div>
            <?php elseif ($customField->field_type == 'date'):
                $inputValue = $fieldModel->getProductCustomFieldInputValue($customField->id, $product->id); ?>
                <div class="col-sm-12 <?= $customField->row_width == 'half' ? "col-sm-6" : "col-sm-12"; ?> col-custom-field">
                    <label><?= esc($customFieldName); ?></label>
                    <div class="input-group date input-group-datepicker" data-provide="datepicker">
                        <input type="text" name="field_<?= $customField->id; ?>" value="<?= esc($inputValue); ?>" class="datepicker form-control form-input" placeholder="<?= esc($customFieldName); ?>" <?= $customField->is_required == 1 ? 'required' : ''; ?>>
                        <div class="input-group-append input-group-addon cursor-pointer">
                            <span class="input-group-text input-group-text-date"><i class="icon-calendar"></i> </span>
                        </div>
                    </div>
                </div>
            <?php elseif ($customField->field_type == 'dropdown'): ?>
                <div class="col-sm-12 <?= $customField->row_width == 'half' ? "col-sm-6" : "col-sm-12"; ?> col-custom-field">
                    <label><?= esc($customFieldName); ?></label>
                    <select name="field_<?= $customField->id; ?>" class="form-control custom-select" <?= $customField->is_required == 1 ? 'required' : ''; ?>>
                        <option value=""><?= trans('select_option'); ?></option>
                        <?php $fieldOptions = $fieldModel->getFieldOptions($customField, selectedLangId());
                        $fieldValues = $fieldModel->getProductCustomFieldValues($customField->id, $product->id, selectedLangId());
                        $selectedOptionIds = getArrayColumnValues($fieldValues, 'selected_option_id');
                        if (!empty($fieldOptions)):
                            foreach ($fieldOptions as $fieldOption):?>
                                <option value="<?= $fieldOption->id; ?>" <?= isItemInArray($fieldOption->id, $selectedOptionIds) ? 'selected' : ''; ?>><?= getCustomFieldOptionName($fieldOption); ?></option>
                            <?php endforeach;
                        endif; ?>
                    </select>
                </div>
            <?php elseif ($customField->field_type == 'radio_button'): ?>
                <div class="col-sm-12 <?= $customField->row_width == "half" ? "col-sm-6" : "col-sm-12"; ?> col-custom-field">
                    <label><?= esc($customFieldName); ?></label>
                    <div class="row">
                        <?php $fieldOptions = $fieldModel->getFieldOptions($customField, selectedLangId());
                        $fieldValues = $fieldModel->getProductCustomFieldValues($customField->id, $product->id, selectedLangId());
                        $selectedOptionIds = getArrayColumnValues($fieldValues, 'selected_option_id');
                        if (!empty($fieldOptions)):
                            foreach ($fieldOptions as $fieldOption): ?>
                                <div class="col-sm-12 col-sm-3 col-custom-option">
                                    <div class="custom-control custom-radio custom-control-validate-input label_validate_field_<?= $customField->id; ?>">
                                        <input type="radio" class="custom-control-input" id="form_radio_<?= $fieldOption->id; ?>" name="field_<?= $customField->id; ?>"
                                               value="<?= $fieldOption->id; ?>" <?= isItemInArray($fieldOption->id, $selectedOptionIds) ? 'checked' : ''; ?> <?= $customField->is_required == 1 ? 'required' : ''; ?>>
                                        <label class="custom-control-label" for="form_radio_<?= $fieldOption->id; ?>"><?= getCustomFieldOptionName($fieldOption); ?></label>
                                    </div>
                                </div>
                            <?php endforeach;
                        endif; ?>
                    </div>
                </div>
            <?php elseif ($customField->field_type == "checkbox"): ?>
                <div id="checkbox_options_container_<?= $customField->id; ?>" class="col-sm-12 <?= $customField->row_width == "half" ? "col-sm-6" : "col-sm-12"; ?> col-custom-field checkbox-options-container" data-custom-field-id="<?= $customField->id; ?>">
                    <label><?= $customFieldName; ?></label>
                    <div class="row">
                        <?php $fieldOptions = $fieldModel->getFieldOptions($customField, selectedLangId());
                        $fieldValues = $fieldModel->getProductCustomFieldValues($customField->id, $product->id, selectedLangId());
                        $selectedOptionIds = getArrayColumnValues($fieldValues, 'selected_option_id');
                        if (!empty($fieldOptions)):
                            foreach ($fieldOptions as $fieldOption): ?>
                                <div class="col-sm-12 col-sm-3 col-custom-option">
                                    <div class="custom-control custom-checkbox custom-control-validate-input label_validate_field_<?= $customField->id; ?>">
                                        <input type="checkbox" class="custom-control-input <?= $customField->is_required == 1 ? 'required-checkbox' : ''; ?>" id="form_checkbox_<?= $fieldOption->id; ?>" name="field_<?= $customField->id; ?>[]"
                                               value="<?= $fieldOption->id; ?>" <?= isItemInArray($fieldOption->id, $selectedOptionIds) ? 'checked' : ''; ?> <?= $customField->is_required == 1 ? 'required' : ''; ?>>
                                        <label class="custom-control-label" for="form_checkbox_<?= $fieldOption->id; ?>"><?= getCustomFieldOptionName($fieldOption); ?></label>
                                    </div>
                                </div>
                            <?php endforeach;
                        endif; ?>
                    </div>
                </div>
            <?php endif;
        endif;
    endforeach;
endif; ?>
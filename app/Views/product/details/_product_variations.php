<?php $optionStock = $product->stock;
if (!empty($variation)):
    $variationLabel = getVariationLabel($variation->label_names, selectedLangId());
    if ($variation->variation_type == 'radio_button'): ?>
        <input type="hidden" class="input-product-variation" data-id="<?= $variation->id; ?>" data-type="radio_button">
        <div class="col-12 col-product-variation">
            <label class="label-product-variation"><?= esc($variationLabel); ?></label>
        </div>
        <div class="col-12 col-product-variation">
            <?php $variationOptions = getProductVariationOptions($variation->id);
            if (!empty($variationOptions)):
                foreach ($variationOptions as $option):
                    if ($option->is_default != 1):
                        $optionStock = $option->stock;
                    endif;
                    $optionName = getVariationOptionName($option->option_names, selectedLangId()); ?>
                    <div class="custom-control custom-control-variation custom-control-validate-input">
                        <input type="radio" name="variation<?= $variation->id; ?>" data-name="variation<?= $variation->id; ?>" value="<?= $option->id; ?>" id="radio<?= $option->id; ?>" class="custom-control-input" <?= $option->is_default == 1 ? 'checked' : ''; ?> onchange="selectProductVariationOption('<?= $variation->id; ?>', 'radio_button', $(this).val());" required>
                        <?php if ($variation->option_display_type == 'image'):
                            $optionImage = getVariationMainOptionImageUrl($option, $productImages); ?>
                            <label for="radio<?= $option->id; ?>" data-input-name="variation<?= $variation->id; ?>" class="custom-control-label custom-control-label-image label-variation<?= $variation->id; ?> <?= ($optionStock < 1) ? 'option-out-of-stock' : ''; ?>">
                                <span class="img-cnt"><img src="<?= $optionImage; ?>" class="img-variation-option" data-toggle="tooltip" data-placement="top" title="<?= esc($optionName); ?>" alt="<?= esc($optionName); ?>"></span>
                            </label>
                        <?php elseif ($variation->option_display_type == 'color'): ?>
                            <label for="radio<?= $option->id; ?>" data-input-name="variation<?= $variation->id; ?>" class="custom-control-label label-variation-color label-variation<?= $variation->id; ?> <?= ($optionStock < 1) ? 'option-out-of-stock' : ''; ?>" data-toggle="tooltip" data-placement="top" title="<?= esc($optionName); ?>">
                                <span class="variation-color-box" style="background-color: <?= $option->color; ?>"></span>
                            </label>
                        <?php else: ?>
                            <label for="radio<?= $option->id; ?>" data-input-name="variation<?= $variation->id; ?>" class="custom-control-label label-variation<?= $variation->id; ?> <?= ($optionStock < 1) ? 'option-out-of-stock' : ''; ?>">
                                <?= esc($optionName); ?>
                            </label>
                        <?php endif; ?>
                    </div>
                <?php endforeach;
            endif; ?>
        </div>
    <?php elseif ($variation->variation_type == 'dropdown'): ?>
        <input type="hidden" class="input-product-variation" data-id="<?= $variation->id; ?>" data-type="dropdown">
        <div class="col-12 col-lg-6 col-product-variation item-variation">
            <div class="form-group">
                <label class="control-label"><?= esc($variationLabel); ?></label>
                <select name="variation<?= $variation->id; ?>" id="variation_dropdown_<?= $variation->id; ?>" class="form-control custom-select" onchange="selectProductVariationOption('<?= $variation->id; ?>', 'dropdown', $(this).val());" required>
                    <option value=""><?= trans("select"); ?></option>
                    <?php if ($variation->parent_id == 0):
                        $variationOptions = getProductVariationOptions($variation->id);
                        if (!empty($variationOptions)):
                            foreach ($variationOptions as $option):
                                if ($option->is_default != 1):
                                    $optionStock = $option->stock;
                                endif;
                                $optionName = getVariationOptionName($option->option_names, selectedLangId()); ?>
                                <option value="<?= $option->id; ?>" <?= $optionStock < 1 ? 'disabled' : ''; ?> <?= $option->is_default == 1 ? 'selected' : ''; ?>><?= esc($optionName); ?></option>
                            <?php endforeach;
                        endif;
                    else: ?>
                        <option value=""><?= trans("select"); ?></option>
                        <?php $defaultOption = getVariationDefaultOption($variation->parent_id);
                        if (!empty($defaultOption)):
                            $subOptions = getVariationSubOptions($defaultOption->id);
                            if (!empty($subOptions)):
                                foreach ($subOptions as $subOption):
                                    $optionName = getVariationOptionName($subOption->option_names, selectedLangId()); ?>
                                    <option value="<?= $subOption->id; ?>"><?= esc($optionName); ?></option>
                                <?php endforeach;
                            endif;
                        endif;
                    endif; ?>
                </select>
            </div>
        </div>
    <?php elseif ($variation->variation_type == 'checkbox'): ?>
        <input type="hidden" class="input-product-variation" data-id="<?= $variation->id; ?>" data-type="checkbox">
        <div class="col-12 col-product-variation">
            <label class="label-product-variation"><?= esc($variationLabel); ?></label>
        </div>
        <div class="col-12 col-product-variation product-variation-checkbox">
            <?php $variationOptions = getProductVariationOptions($variation->id);
            if (!empty($variationOptions)):
                foreach ($variationOptions as $option):
                    if ($option->is_default != 1):
                        $optionStock = $option->stock;
                    endif;
                    $optionName = getVariationOptionName($option->option_names, selectedLangId()); ?>
                    <div class="custom-control custom-control-variation custom-control-validate-input">
                        <input type="checkbox" name="variation<?= $variation->id; ?>[]" value="<?= $option->id; ?>" id="checkbox<?= $option->id; ?>" class="custom-control-input" required>
                        <?php if ($variation->option_display_type == 'image'):
                            $optionImage = getVariationMainOptionImageUrl($option, $productImages); ?>
                            <label for="checkbox<?= $option->id; ?>" data-input-name="variation<?= $variation->id; ?>" class="custom-control-label custom-control-label-image label-variation<?= $variation->id; ?> <?= $optionStock < 1 ? 'option-out-of-stock' : ''; ?>">
                                <span class="img-cnt"><img src="<?= $optionImage; ?>" class="img-variation-option" data-toggle="tooltip" data-placement="top" title="<?= esc($optionName); ?>" alt="<?= esc($optionName); ?>"></span>
                            </label>
                        <?php elseif ($variation->option_display_type == 'color'): ?>
                            <label for="checkbox<?= $option->id; ?>" data-input-name="variation<?= $variation->id; ?>" class="custom-control-label label-variation-color label-variation<?= $variation->id; ?> <?= $optionStock < 1 ? 'option-out-of-stock' : ''; ?>" data-toggle="tooltip" data-placement="top" title="<?= esc($optionName); ?>">
                                <span class="variation-color-box" style="background-color: <?= $option->color; ?>"></span>
                            </label>
                        <?php else: ?>
                            <label for="checkbox<?= $option->id; ?>" data-input-name="variation<?= $variation->id; ?>" class="custom-control-label label-variation<?= $variation->id; ?> <?= $optionStock < 1 ? 'option-out-of-stock' : ''; ?>">
                                <?= esc($optionName); ?>
                            </label>
                        <?php endif; ?>
                    </div>
                <?php endforeach;
            endif; ?>
        </div>
    <?php elseif ($variation->variation_type == 'text'): ?>
        <div class="col-12 col-lg-6 col-product-variation item-variation">
            <div class="form-group m-b-20">
                <input type="text" name="variation<?= $variation->id; ?>" class="form-control form-input" placeholder="<?= esc($variationLabel); ?>" required>
            </div>
        </div>
    <?php elseif ($variation->variation_type == 'number'): ?>
        <div class="col-12 col-lg-6 col-product-variation item-variation">
            <div class="form-group m-b-20">
                <input type="number" name="variation<?= $variation->id; ?>" class="form-control form-input" placeholder="<?= esc($variationLabel); ?>" min="1" required>
            </div>
        </div>
    <?php endif;
endif; ?>
<div id="wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="nav-breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-products">
                        <li class="breadcrumb-item"><a href="<?= langBaseUrl(); ?>"><?= trans("home"); ?></a></li>
                        <?php if (!empty($parentCategoriesTree)):
                            foreach ($parentCategoriesTree as $item):?>
                                <li class="breadcrumb-item"><a href="<?= generateCategoryUrl($item); ?>"><?= getCategoryName($item); ?></a></li>
                            <?php endforeach;
                        endif; ?>
                        <li class="breadcrumb-item active"><?= esc($title); ?></li>
                    </ol>
                </nav>
            </div>
            <div class="col-12">
                <div class="product-details-container <?= (!empty($video) || !empty($audio)) && countItems($productImages) < 2 ? 'product-details-container-digital' : ''; ?>">
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-6">
                            <div id="product_slider_container">
                                <?= view("product/details/_preview"); ?>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6">
                            <div id="response_product_details" class="product-content-details">
                                <?= view("product/details/_product_details"); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="product-description post-text-responsive">
                            <?php $session = session();
                            $isReviewTabActive = false;
                            if (!empty($session->getFlashdata('review_added'))) {
                                $isReviewTabActive = true;
                            } ?>
                            <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link <?= $isReviewTabActive == true ? '' : 'active'; ?>" id="tab_description" data-toggle="tab" href="#tab_description_content" role="tab" aria-controls="tab_description" aria-selected="true"><?= trans("description"); ?></a>
                                </li>
                                <?php if (!empty($customFields)): ?>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab_additional_information" data-toggle="tab" href="#tab_additional_information_content" role="tab" aria-controls="tab_additional_information" aria-selected="false"><?= trans("additional_information"); ?></a>
                                    </li>
                                <?php endif;
                                if ($shippingStatus == 1 || $productLocationStatus == 1): ?>
                                    <li class="nav-item">
                                        <?php if ($shippingStatus == 1 && $productLocationStatus != 1): ?>
                                            <a class="nav-link" id="tab_shipping" data-toggle="tab" href="#tab_shipping_content" role="tab" aria-controls="tab_shipping" aria-selected="false"><?= trans("shipping"); ?></a>
                                        <?php elseif ($shippingStatus != 1 && $productLocationStatus == 1): ?>
                                            <a class="nav-link" id="tab_shipping" data-toggle="tab" href="#tab_shipping_content" role="tab" aria-controls="tab_shipping" aria-selected="false" onclick="loadProductShopLocationMap();"><?= trans("location"); ?></a>
                                        <?php else: ?>
                                            <a class="nav-link" id="tab_shipping" data-toggle="tab" href="#tab_shipping_content" role="tab" aria-controls="tab_shipping" aria-selected="false" onclick="loadProductShopLocationMap();"><?= trans("shipping_location"); ?></a>
                                        <?php endif; ?>
                                    </li>
                                <?php endif;
                                if ($generalSettings->reviews == 1): ?>
                                    <li class="nav-item">
                                        <a class="nav-link <?= $isReviewTabActive == true ? 'active' : ''; ?>" id="tab_reviews" data-toggle="tab" href="#tab_reviews_content" role="tab" aria-controls="tab_reviews" aria-selected="false"><?= trans("reviews"); ?>&nbsp;(<?= $reviewCount; ?>)</a>
                                    </li>
                                <?php endif;
                                if ($generalSettings->product_comments == 1): ?>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab_comments" data-toggle="tab" href="#tab_comments_content" role="tab" aria-controls="tab_comments" aria-selected="false"><?= trans("comments"); ?>&nbsp;(<?= $commentCount; ?>)</a>
                                    </li>
                                <?php endif;
                                if ($generalSettings->facebook_comment_status == 1): ?>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab_facebook_comments" data-toggle="tab" href="#tab_facebook_comments_content" role="tab" aria-controls="facebook_comments" aria-selected="false"><?= trans("facebook_comments"); ?></a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                            <div id="accordion" class="tab-content">
                                <div class="tab-pane fade <?= $isReviewTabActive == true ? '' : 'show active'; ?>" id="tab_description_content" role="tabpanel">
                                    <div class="card">
                                        <div class="card-header">
                                            <a class="card-link" data-toggle="collapse" href="#collapse_description_content">
                                                <?= trans("description"); ?><i class="icon-arrow-down"></i><i class="icon-arrow-up"></i>
                                            </a>
                                        </div>
                                        <div id="collapse_description_content" class="collapse-description-content collapse show" data-parent="#accordion">
                                            <div class="description">
                                                <?= !empty($productDetails->description) ? $productDetails->description : ''; ?>
                                            </div>
                                            <div class="row-custom text-right m-b-10">
                                                <?php if (authCheck()):
                                                    if ($product->user_id != user()->id):?>
                                                        <a href="javascript:void(0)" class="text-muted link-abuse-report" data-toggle="modal" data-target="#reportProductModal">
                                                            <?= trans("report_this_product"); ?>
                                                        </a>
                                                    <?php endif;
                                                else: ?>
                                                    <a href="javascript:void(0)" class="text-muted link-abuse-report" data-toggle="modal" data-target="#loginModal">
                                                        <?= trans("report_this_product"); ?>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if (!empty($customFields)): ?>
                                    <div class="tab-pane fade" id="tab_additional_information_content" role="tabpanel">
                                        <div class="card">
                                            <div class="card-header">
                                                <a class="card-link collapsed" data-toggle="collapse" href="#collapse_additional_information_content">
                                                    <?= trans("additional_information"); ?><i class="icon-arrow-down"></i><i class="icon-arrow-up"></i>
                                                </a>
                                            </div>
                                            <div id="collapse_additional_information_content" class="collapse-description-content collapse" data-parent="#accordion">
                                                <table class="table table-striped table-product-additional-information">
                                                    <tbody>
                                                    <?php foreach ($customFields as $customField):
                                                        $fieldValue = getCustomFieldProductValues($customField, $product->id, selectedLangId());
                                                        if (!empty($fieldValue)):?>
                                                            <tr>
                                                                <td class="td-left"><?= @parseSerializedNameArray($customField->name_array, selectedLangId()); ?></td>
                                                                <td class="td-right"><?= esc($fieldValue); ?></td>
                                                            </tr>
                                                        <?php endif;
                                                    endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif;
                                if ($shippingStatus == 1 || $productLocationStatus == 1): ?>
                                    <div class="tab-pane fade" id="tab_shipping_content" role="tabpanel">
                                        <div class="card">
                                            <div class="card-header">
                                                <?php if ($shippingStatus == 1 && $productLocationStatus != 1): ?>
                                                    <a class="card-link collapsed" data-toggle="collapse" href="#collapse_shipping_content"><?= trans("shipping"); ?><i class="icon-arrow-down"></i><i class="icon-arrow-up"></i></a>
                                                <?php elseif ($shippingStatus != 1 && $productLocationStatus == 1): ?>
                                                    <a class="card-link collapsed" data-toggle="collapse" href="#collapse_shipping_content" onclick="loadProductShopLocationMap();"><?= trans("location"); ?><i class="icon-arrow-down"></i><i class="icon-arrow-up"></i></a>
                                                <?php else: ?>
                                                    <a class="card-link collapsed" data-toggle="collapse" href="#collapse_shipping_content" onclick="loadProductShopLocationMap();"><?= trans("shipping_location"); ?><i class="icon-arrow-down"></i><i class="icon-arrow-up"></i></a>
                                                <?php endif; ?>
                                            </div>
                                            <div id="collapse_shipping_content" class="collapse-description-content collapse" data-parent="#accordion">
                                                <table class="table table-product-shipping">
                                                    <tbody>
                                                    <?php if ($shippingStatus == 1): ?>
                                                        <tr>
                                                            <td class="td-left"><?= trans("shipping_cost"); ?></td>
                                                            <td class="td-right">
                                                                <div class="form-group">
                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <label class="control-label"><?= trans("select_your_location"); ?></label>
                                                                        </div>
                                                                        <div class="col-12 col-md-4 m-b-sm-15">
                                                                            <select id="select_countries_product" name="country_id" class="select2 form-control" data-placeholder="<?= trans("country"); ?>" onchange="getStates(this.value, 'product'); $('#product_shipping_cost_container').empty();">
                                                                                <option></option>
                                                                                <?php if (!empty($activeCountries)):
                                                                                    foreach ($activeCountries as $item): ?>
                                                                                        <option value="<?= $item->id; ?>"><?= esc($item->name); ?></option>
                                                                                    <?php endforeach;
                                                                                endif; ?>
                                                                            </select>
                                                                        </div>
                                                                        <div id="get_states_container_product" class="col-12 col-md-4">
                                                                            <select id="select_states_product" name="state_id" class="select2 form-control" data-placeholder="<?= trans("state"); ?>" onchange="getProductShippingCost(this.value, '<?= $product->id; ?>');">
                                                                                <option></option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div id="product_shipping_cost_container" class="product-shipping-methods"></div>
                                                                <div class="row-custom">
                                                                    <div class="product-shipping-loader">
                                                                        <div class="spinner">
                                                                            <div class="bounce1"></div>
                                                                            <div class="bounce2"></div>
                                                                            <div class="bounce3"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <?php if (!empty($deliveryTime)): ?>
                                                            <tr>
                                                                <td class="td-left"><?= trans("delivery_time"); ?></td>
                                                                <td class="td-right"><span><?= @parseSerializedOptionArray($deliveryTime->option_array, selectedLangId()); ?></span></td>
                                                            </tr>
                                                        <?php endif;
                                                    endif;
                                                    if ($productLocationStatus == 1): ?>
                                                        <tr>
                                                            <td class="td-left"><?= trans("shop_location"); ?></td>
                                                            <td class="td-right"><span id="span_shop_location_address"><?= getLocation($user); ?></span></td>
                                                        </tr>
                                                    <?php endif; ?>
                                                    </tbody>
                                                </table>
                                                <?php if ($productLocationStatus == 1): ?>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="product-location-map">
                                                                <iframe id="iframe_shop_location_address" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif;
                                if ($generalSettings->reviews == 1): ?>
                                    <div class="tab-pane fade <?= $isReviewTabActive == true ? 'show active' : ''; ?>" id="tab_reviews_content" role="tabpanel">
                                        <div class="card">
                                            <div class="card-header">
                                                <a class="card-link collapsed" data-toggle="collapse" href="#collapse_reviews_content">
                                                    <?= trans("reviews"); ?><i class="icon-arrow-down"></i><i class="icon-arrow-up"></i>
                                                </a>
                                            </div>
                                            <div id="collapse_reviews_content" class="collapse-description-content collapse" data-parent="#accordion">
                                                <div id="review-result">
                                                    <?= view('product/details/_reviews'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif;
                                if ($generalSettings->product_comments == 1): ?>
                                    <div class="tab-pane fade" id="tab_comments_content" role="tabpanel">
                                        <div class="card">
                                            <div class="card-header">
                                                <a class="card-link collapsed" data-toggle="collapse" href="#collapse_comments_content">
                                                    <?= trans("comments"); ?><i class="icon-arrow-down"></i><i class="icon-arrow-up"></i>
                                                </a>
                                            </div>
                                            <div id="collapse_comments_content" class="collapse-description-content collapse" data-parent="#accordion">
                                                <input type="hidden" value="<?= $commentLimit; ?>" id="product_comment_limit">
                                                <div class="comments-container">
                                                    <div class="row">
                                                        <div class="col-12 col-md-6">
                                                            <?= view('product/details/_comments'); ?>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <div class="col-comments-inner">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="row-custom row-comment-label">
                                                                            <label class="label-comment"><?= trans("add_a_comment"); ?></label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <form id="form_add_comment">
                                                                            <input type="hidden" name="product_id" value="<?= $product->id; ?>">
                                                                            <?php if (!authCheck()): ?>
                                                                                <div class="form-row">
                                                                                    <div class="form-group col-md-6">
                                                                                        <input type="text" name="name" id="comment_name" class="form-control form-input" placeholder="<?= trans("name"); ?>">
                                                                                    </div>
                                                                                    <div class="form-group col-md-6">
                                                                                        <input type="email" name="email" id="comment_email" class="form-control form-input" placeholder="<?= trans("email_address"); ?>">
                                                                                    </div>
                                                                                </div>
                                                                            <?php endif; ?>
                                                                            <div class="form-group">
                                                                                <textarea name="comment" id="comment_text" class="form-control form-input form-textarea" placeholder="<?= trans("comment"); ?>"></textarea>
                                                                            </div>
                                                                            <?php if (!authCheck()): ?>
                                                                                <div class="form-group">
                                                                                    <?php reCaptcha('generate'); ?>
                                                                                </div>
                                                                            <?php endif; ?>
                                                                            <div class="form-group">
                                                                                <button type="submit" class="btn btn-md btn-custom"><?= trans("submit"); ?></button>
                                                                            </div>
                                                                        </form>
                                                                        <div id="message-comment-result" class="message-comment-result"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif;
                                if ($generalSettings->facebook_comment_status == 1): ?>
                                    <div class="tab-pane fade" id="tab_facebook_comments_content" role="tabpanel">
                                        <div class="card">
                                            <div class="card-header">
                                                <a class="card-link collapsed" data-toggle="collapse" href="#collapse_facebook_comments_content">
                                                    <?= trans("facebook_comments"); ?><i class="icon-arrow-down"></i><i class="icon-arrow-up"></i>
                                                </a>
                                            </div>
                                            <div id="collapse_facebook_comments_content" class="collapse-description-content collapse" data-parent="#accordion">
                                                <div class="fb-comments" data-href="<?= current_url(); ?>" data-width="100%" data-numposts="5" data-colorscheme="light"></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?= view('partials/_ad_spaces', ['adSpace' => 'product_1', 'class' => 'mb-4']); ?>
            <?php if (!empty($userProducts) && $generalSettings->multi_vendor_system == 1): ?>
                <div class="col-12 section section-related-products m-t-30">
                    <h3 class="title"><?= trans("more_from"); ?>&nbsp;<a href="<?= generateProfileUrl($user->slug); ?>"><?= esc(getUsername($user)); ?></a></h3>
                    <div class="row row-product">
                        <?php $count = 0;
                        foreach ($userProducts as $item):
                            if ($count < 5):?>
                                <div class="col-6 col-sm-4 col-md-3 col-mds-5 col-product">
                                    <?= view('product/_product_item', ['product' => $item]); ?>
                                </div>
                            <?php endif;
                            $count++;
                        endforeach; ?>
                    </div>
                    <?php if (countItems($userProducts) > 5): ?>
                        <div class="row-custom text-center">
                            <a href="<?= generateProfileUrl($product->user_slug); ?>" class="link-see-more"><span><?= trans("view_all"); ?>&nbsp;</span><i class="icon-arrow-right"></i></a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif;
            if (!empty($relatedProducts)): ?>
                <div class="col-12 section section-related-products">
                    <h3 class="title"><?= trans("you_may_also_like"); ?></h3>
                    <div class="row row-product">
                        <?php foreach ($relatedProducts as $item): ?>
                            <div class="col-6 col-sm-4 col-md-3 col-mds-5 col-product">
                                <?= view('product/_product_item', ['product' => $item]); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            <?= view('partials/_ad_spaces', ['adSpace' => 'product_2', 'class' => 'mb-4']); ?>
        </div>
    </div>
</div>

<?= view('partials/_modal_send_message', ['subject' => esc($title), 'productId' => $product->id]); ?>

<?php if (authCheck() && $product->user_id != user()->id): ?>
    <div class="modal fade" id="reportProductModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content modal-custom modal-report-abuse">
                <form id="form_report_product" method="post">
                    <input type="hidden" name="id" value="<?= $product->id; ?>">
                    <div class="modal-header">
                        <h5 class="modal-title"><?= trans("report_this_product"); ?></h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true"><i class="icon-close"></i> </span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div id="response_form_report_product" class="col-12"></div>
                            <div class="col-12">
                                <div class="form-group m-0">
                                    <label class="control-label"><?= trans("description"); ?></label>
                                    <textarea name="description" class="form-control form-textarea" placeholder="<?= trans("abuse_report_exp"); ?>" minlength="5" maxlength="10000" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer text-right">
                        <button type="submit" class="btn btn-md btn-custom"><?= trans("submit"); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif;
if ($generalSettings->facebook_comment_status == 1):
    echo $generalSettings->facebook_comment;
endif; ?>

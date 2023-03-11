<?php
/*
 * --------------------------------------------------------------------
 * GET
 * --------------------------------------------------------------------
 */

$routes->post('login-post', 'AuthController::loginPost');
$routes->get('logout', 'CommonController::logout');
$routes->get('cron/update-sitemap', 'HomeController::cronUpdateSitemap');
$routes->get('unsubscribe', 'HomeController::unSubscribe');
$routes->get('connect-with-facebook', 'AuthController::connectWithFacebook');
$routes->get('facebook-callback', 'AuthController::facebookCallback');
$routes->get('connect-with-google', 'AuthController::connectWithGoogle');
$routes->get('connect-with-vk', 'AuthController::connectWithVk');

/*
 * --------------------------------------------------------------------
 * POST
 * --------------------------------------------------------------------
 */

//home
$routes->post('contact-post', 'HomeController::contactPost');
$routes->post('set-selected-currency-post', 'HomeController::setSelectedCurrency');
$routes->post('add-review-post', 'HomeController::addReviewPost');
$routes->post('submit-request-post', 'SupportController::submitRequestPost');
$routes->post('reply-ticket-post', 'SupportController::replyTicketPost');
$routes->post('close-ticket-post', 'SupportController::closeTicketPost');
$routes->post('download-attachment-post', 'SupportController::downloadAttachmentPost');
//auth
$routes->post('forgot-password-post', 'AuthController::forgotPasswordPost');
$routes->post('reset-password-post', 'AuthController::resetPasswordPost');
$routes->post('register-post', 'AuthController::registerPost');
//bidding
$routes->post('submit-quote-post', 'DashboardController::submitQuotePost');
$routes->post('request-quote-post', 'OrderController::requestQuotePost');
$routes->post('accept-quote-post', 'OrderController::acceptQuote');
$routes->post('reject-quote-post', 'OrderController::rejectQuote');
//cart
$routes->post('add-to-cart', 'CartController::addToCart');
$routes->post('add-to-cart-quote', 'CartController::addToCartQuote');
$routes->post('update-cart-product-quantity', 'CartController::updateCartProductQuantity');
$routes->post('payment-method-post', 'CartController::paymentMethodPost');
$routes->post('shipping-post', 'CartController::shippingPost');
$routes->post('bank-transfer-payment-post', 'CartController::bankTransferPaymentPost');
$routes->post('cash-on-delivery-payment-post', 'CartController::cashOnDeliveryPaymentPost');
$routes->post('paypal-payment-post', 'CartController::paypalPaymentPost');
$routes->post('paystack-payment-post', 'CartController::paystackPaymentPost');
$routes->post('razorpay-payment-post', 'CartController::razorpayPaymentPost');
$routes->get('flutterwave-payment-post', 'CartController::flutterwavePaymentPost');
$routes->post('stripe-payment-post', 'CartController::stripePaymentPost');
$routes->get('iyzico-payment-post', 'CartController::iyzicoPaymentPost');
$routes->post('midtrans-payment-post', 'CartController::midtransPaymentPost');
$routes->get('mercado-pago-payment-post', 'CartController::mercadoPagoPaymentPost');
$routes->post('coupon-code-post', 'CartController::couponCodePost');
//order
$routes->post('submit-refund-request', 'OrderController::submitRefundRequest');
$routes->post('add-refund-message', 'OrderController::addRefundMessage');
$routes->post('bank-transfer-payment-report-post', 'OrderController::bankTransferPaymentReportPost');
//earnings
$routes->post('withdraw-money-post', 'DashboardController::withdrawMoneyPost');
$routes->post('set-payout-account-post', 'DashboardController::setPayoutAccountPost');
//message
$routes->post('send-message-post', 'HomeController::sendMessagePost');
//file
$routes->post('upload-audio-post', 'FileController::uploadAudio');
$routes->post('load-audio-preview-post', 'FileController::loadAudioPreview');
$routes->post('upload-digital-file-post', 'FileController::uploadDigitalFile');
$routes->post('download-digital-file-post', 'FileController::downloadDigitalFile');
$routes->post('upload-file-manager-images-post', 'FileController::uploadFileManagerImagePost');
$routes->post('upload-image-post', 'FileController::uploadImage');
$routes->post('get-uploaded-image-post', 'FileController::getUploadedImage');
$routes->post('upload-image-session-post', 'FileController::uploadImageSession');
$routes->post('get-sess-uploaded-image-post', 'FileController::getSessUploadedImage');
$routes->post('upload-video-post', 'FileController::uploadVideo');
$routes->post('load-video-preview-post', 'FileController::loadVideoPreview');
$routes->post('download-purchased-digital-file-post', 'FileController::downloadPurchasedDigitalFile');
$routes->post('download-free-digital-file-post', 'FileController::downloadFreeDigitalFile');
//product
$routes->post('add-product-post', 'DashboardController::addProductPost');
$routes->post('edit-product-post', 'DashboardController::editProductPost');
$routes->post('edit-product-details-post', 'DashboardController::editProductDetailsPost');
$routes->post('start-selling-post', 'HomeController::startSellingPost');
$routes->post('renew-membership-plan-post', 'HomeController::renewMembershipPlanPost');
$routes->post('add-remove-wishlist-post', 'AjaxController::addRemoveWishlist');
//variations
$routes->post('add-variation-post', 'VariationController::addVariationPost');
$routes->post('edit-variation', 'VariationController::editVariation');
$routes->post('edit-variation-post', 'VariationController::editVariationPost');
$routes->post('delete-variation-post', 'VariationController::deleteVariationPost');
$routes->post('add-variation-option', 'VariationController::addVariationOption');
$routes->post('add-variation-option-post', 'VariationController::addVariationOptionPost');
$routes->post('view-variation-options', 'VariationController::viewVariationOptions');
$routes->post('edit-variation-option', 'VariationController::editVariationOption');
$routes->post('edit-variation-option-post', 'VariationController::editVariationOptionPost');
$routes->post('delete-variation-option-post', 'VariationController::deleteVariationOptionPost');
$routes->post('select-variation-post', 'VariationController::selectVariationPost');
$routes->post('upload-variation-image-session', 'VariationController::uploadVariationImageSession');
$routes->post('get-uploaded-variation-image-session', 'VariationController::getSessUploadedVariationImage');
$routes->post('delete-variation-image-session-post', 'VariationController::deleteVariationImageSessionPost');
$routes->post('set-variation-image-main-session', 'VariationController::setVariationImageMainSession');
$routes->post('set-variation-image-main', 'VariationController::setVariationImageMain');
$routes->post('upload-variation-image', 'VariationController::uploadVariationImage');
$routes->post('get-uploaded-variation-image', 'VariationController::getUploadedVariationImage');
$routes->post('delete-variation-image-post', 'VariationController::deleteVariationImagePost');
$routes->post('select-variation-option-post', 'AjaxController::selectProductVariationOption');
$routes->post('get-sub-variation-options', 'AjaxController::getSubVariationOptions');
//profile
$routes->post('social-media-post', 'ProfileController::socialMediaPost');
$routes->post('edit-profile-post', 'ProfileController::editProfilePost');
$routes->post('cover-image-post', 'ProfileController::coverImagePost');
$routes->post('follow-unfollow-user-post', 'ProfileController::followUnfollowUser');
$routes->post('change-password-post', 'ProfileController::changePasswordPost');
$routes->post('add-shipping-address-post', 'ProfileController::addShippingAddressPost');
$routes->post('edit-shipping-address-post', 'ProfileController::editShippingAddressPost');
//shop & shipping settings
$routes->post('shop-settings-post', 'DashboardController::shopSettingsPost');
$routes->post('add-shipping-zone-post', 'DashboardController::addShippingZonePost');
$routes->post('edit-shipping-zone-post', 'DashboardController::editShippingZonePost');
$routes->post('add-shipping-class-post', 'DashboardController::addShippingClassPost');
$routes->post('edit-shipping-class-post', 'DashboardController::editShippingClassPost');
$routes->post('add-shipping-delivery-time-post', 'DashboardController::addShippingDeliveryTimePost');
$routes->post('edit-shipping-delivery-time-post', 'DashboardController::editShippingDeliveryTimePost');
//order dash
$routes->post('update-order-product-status-post', 'DashboardController::updateOrderProductStatusPost');
//promote
$routes->post('promote-product-post', 'DashboardController::promoteProductPost');
//coupon
$routes->post('add-coupon-post', 'DashboardController::addCouponPost');
$routes->post('edit-coupon-post', 'DashboardController::editCouponPost');
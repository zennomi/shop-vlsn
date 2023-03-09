<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();
$languages = Globals::$languages;
$generalSettings = Globals::$generalSettings;
$csrt = Globals::$customRoutes;
$rtAdmin = $csrt->admin;

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('HomeController');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override(function () {
    header('HTTP/1.0 404 Not Found');
    $data['title'] = trans("page_not_found");
    $data['description'] = trans("page_not_found") . ' - ' . Globals::$generalSettings->application_name;
    $data['keywords'] = trans("page_not_found") . ',' . Globals::$generalSettings->application_name;
    $data['isPage404'] = true;
    echo view('partials/_header', $data);
    echo view('errors/html/error_404');
    echo view('partials/_footer', $data);
});
$routes->setAutoRoute(true);
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'HomeController::index');

/*
 * --------------------------------------------------------------------
 * Static Routes
 * --------------------------------------------------------------------
 */

include_once 'RoutesStatic.php';

/*
 * --------------------------------------------------------------------
 * Admin Routes
 * --------------------------------------------------------------------
 */

$routes->get($rtAdmin, 'AdminController::index');
$routes->get($rtAdmin . '/login', 'CommonController::adminLogin');
$routes->post($rtAdmin . '/login-post', 'CommonController::adminLoginpost');
$routes->get('confirm-account', 'AuthController::confirmAccount');
//navigation
$routes->get($rtAdmin . '/navigation', 'AdminController::navigation');
$routes->get($rtAdmin . '/homepage-manager', 'AdminController::homepageManager');
$routes->get($rtAdmin . '/edit-banner/(:num)', 'AdminController::editIndexBanner/$1');
//slider
$routes->get($rtAdmin . '/slider', 'AdminController::slider');
$routes->get($rtAdmin . '/edit-slider-item/(:num)', 'AdminController::editSliderItem/$1');
//page
$routes->get($rtAdmin . '/add-page', 'AdminController::addPage');
$routes->get($rtAdmin . '/edit-page/(:num)', 'AdminController::editPage/$1');
$routes->get($rtAdmin . '/pages', 'AdminController::pages');
//order
$routes->get($rtAdmin . '/orders', 'OrderAdminController::orders');
$routes->get($rtAdmin . '/order-details/(:num)', 'OrderAdminController::orderDetails/$1');
$routes->get($rtAdmin . '/transactions', 'OrderAdminController::transactions');
$routes->get($rtAdmin . '/order-bank-transfers', 'OrderAdminController::orderBankTransfers');
$routes->get($rtAdmin . '/digital-sales', 'OrderAdminController::digitalSales');
//product
$routes->get($rtAdmin . '/products', 'ProductController::products');
$routes->get($rtAdmin . '/product-details/(:num)', 'ProductController::productDetails/$1');
$routes->get($rtAdmin . '/featured-products', 'ProductController::featuredProducts');
$routes->get($rtAdmin . '/featured-products-transactions', 'ProductController::featuredProductsTransactions');
$routes->get($rtAdmin . '/featured-products-pricing', 'ProductController::featuredProductsPricing');
//bidding
$routes->get($rtAdmin . '/quote-requests', 'ProductController::quoteRequests');
//category
$routes->get($rtAdmin . '/add-category', 'CategoryController::addCategory');
$routes->get($rtAdmin . '/categories', 'CategoryController::categories');
$routes->get($rtAdmin . '/edit-category/(:num)', 'CategoryController::editCategory/$1');
$routes->get($rtAdmin . '/bulk-category-upload', 'CategoryController::bulkCategoryUpload');
//custom fields
$routes->get($rtAdmin . '/add-custom-field', 'CategoryController::addCustomField');
$routes->get($rtAdmin . '/custom-fields', 'CategoryController::customFields');
$routes->get($rtAdmin . '/edit-custom-field/(:num)', 'CategoryController::editCustomField/$1');
$routes->get($rtAdmin . '/custom-field-options/(:num)', 'CategoryController::customFieldOptions/$1');
//earnings
$routes->get($rtAdmin . '/earnings', 'EarningsController::earnings');
$routes->get($rtAdmin . '/payout-requests', 'EarningsController::payoutRequests');
$routes->get($rtAdmin . '/payout-settings', 'EarningsController::payoutSettings');
$routes->get($rtAdmin . '/add-payout', 'EarningsController::addPayout');
$routes->get($rtAdmin . '/seller-balances', 'EarningsController::sellerBalances');
//blog
$routes->get($rtAdmin . '/blog-add-post', 'BlogController::addPost');
$routes->get($rtAdmin . '/blog-posts', 'BlogController::posts');
$routes->get($rtAdmin . '/edit-blog-post/(:num)', 'BlogController::editPost/$1');
$routes->get($rtAdmin . '/blog-categories', 'BlogController::categories');
$routes->get($rtAdmin . '/edit-blog-category/(:num)', 'BlogController::editCategory/$1');
//comments & reviews
$routes->get($rtAdmin . '/pending-product-comments', 'ProductController::pendingComments');
$routes->get($rtAdmin . '/product-comments', 'ProductController::comments');
$routes->get($rtAdmin . '/pending-blog-comments', 'BlogController::pendingComments');
$routes->get($rtAdmin . '/blog-comments', 'BlogController::comments');
$routes->get($rtAdmin . '/reviews', 'ProductController::reviews');
$routes->get($rtAdmin . '/contact-messages', 'AdminController::contactMessages');
//abuse reports
$routes->get($rtAdmin . '/abuse-reports', 'AdminController::abuseReports');
//ad spaces
$routes->get($rtAdmin . '/ad-spaces', 'AdminController::adSpaces');
//seo tools
$routes->get($rtAdmin . '/seo-tools', 'AdminController::seoTools');
//location
$routes->get($rtAdmin . '/location-settings', 'AdminController::locationSettings');
$routes->get($rtAdmin . '/countries', 'AdminController::countries');
$routes->get($rtAdmin . '/states', 'AdminController::states');
$routes->get($rtAdmin . '/add-country', 'AdminController::addCountry');
$routes->get($rtAdmin . '/edit-country/(:num)', 'AdminController::editCountry/$1');
$routes->get($rtAdmin . '/add-state', 'AdminController::addState');
$routes->get($rtAdmin . '/edit-state/(:num)', 'AdminController::editState/$1');
$routes->get($rtAdmin . '/cities', 'AdminController::cities');
$routes->get($rtAdmin . '/add-city', 'AdminController::addCity');
$routes->get($rtAdmin . '/edit-city/(:num)', 'AdminController::editCity/$1');
//membership
$routes->get($rtAdmin . '/users', 'MembershipController::users');
$routes->get($rtAdmin . '/shop-opening-requests', 'MembershipController::shopOpeningRequests');
$routes->get($rtAdmin . '/add-user', 'MembershipController::addUser');
$routes->get($rtAdmin . '/edit-user/(:num)', 'MembershipController::editUser/$1');
$routes->get($rtAdmin . '/membership-plans', 'MembershipController::membershipPlans');
$routes->get($rtAdmin . '/transactions-membership', 'MembershipController::transactionsMembership');
$routes->get($rtAdmin . '/edit-plan/(:num)', 'MembershipController::editPlan/$1');
$routes->get($rtAdmin . '/roles-permissions', 'MembershipController::rolesPermissions');
$routes->get($rtAdmin . '/add-role', 'MembershipController::addRole');
$routes->get($rtAdmin . '/edit-role/(:num)', 'MembershipController::editRole/$1');
//support
$routes->get($rtAdmin . '/knowledge-base', 'SupportAdminController::knowledgeBase');
$routes->get($rtAdmin . '/knowledge-base/add-content', 'SupportAdminController::addContent');
$routes->get($rtAdmin . '/knowledge-base/edit-content/(:num)', 'SupportAdminController::editContent/$1');
$routes->get($rtAdmin . '/knowledge-base-categories', 'SupportAdminController::categories');
$routes->get($rtAdmin . '/knowledge-base/add-category', 'SupportAdminController::addCategory');
$routes->get($rtAdmin . '/knowledge-base/edit-category/(:num)', 'SupportAdminController::editCategory/$1');
$routes->get($rtAdmin . '/support-tickets', 'SupportAdminController::supportTickets');
$routes->get($rtAdmin . '/support-ticket/(:num)', 'SupportAdminController::supportTicket/$1');
//refund
$routes->get($rtAdmin . '/refund-requests', 'OrderAdminController::refundRequests');
$routes->get($rtAdmin . '/refund-requests/(:num)', 'OrderAdminController::refund/$1');
//languages
$routes->get($rtAdmin . '/language-settings', 'LanguageController::languageSettings');
$routes->get($rtAdmin . '/edit-language/(:num)', 'LanguageController::editLanguage/$1');
$routes->get($rtAdmin . '/edit-translations/(:num)', 'LanguageController::editTranslations/$1');
$routes->get($rtAdmin . '/search-phrases', 'LanguageController::searchPhrases');
//newsletter
$routes->get($rtAdmin . '/newsletter', 'AdminController::newsletter');
//currency
$routes->get($rtAdmin . '/currency-settings', 'AdminController::currencySettings');
$routes->get($rtAdmin . '/add-currency', 'AdminController::addCurrency');
$routes->get($rtAdmin . '/edit-currency/(:num)', 'AdminController::editCurrency/$1');
//settings
$routes->get($rtAdmin . '/general-settings', 'AdminController::generalSettings');
$routes->get($rtAdmin . '/email-settings', 'AdminController::emailSettings');
$routes->get($rtAdmin . '/social-login', 'AdminController::socialLoginSettings');
$routes->get($rtAdmin . '/payment-settings', 'AdminController::paymentSettings');
$routes->get($rtAdmin . '/visual-settings', 'AdminController::visualSettings');
$routes->get($rtAdmin . '/system-settings', 'AdminController::systemSettings');
$routes->get($rtAdmin . '/preferences', 'AdminController::preferences');
$routes->get($rtAdmin . '/product-settings', 'AdminController::productSettings');
$routes->get($rtAdmin . '/font-settings', 'AdminController::fontSettings');
$routes->get($rtAdmin . '/edit-font/(:num)', 'AdminController::editFont/$1');
$routes->get($rtAdmin . '/route-settings', 'AdminController::routeSettings');
$routes->get($rtAdmin . '/cache-system', 'AdminController::cacheSystem');
$routes->get($rtAdmin . '/storage', 'AdminController::storage');
/*
 * --------------------------------------------------------------------
 * Dynamic Routes
 * --------------------------------------------------------------------
 */

if (!empty($languages)) {
    foreach ($languages as $language) {
        $key = '';
        if ($generalSettings->site_lang != $language->id) {
            $key = $language->short_form . '/';
            $routes->get($language->short_form, 'HomeController::index');
        }
        //auth
        $routes->get($key . $csrt->register, 'AuthController::register');
        $routes->get($key . $csrt->register_success, 'AuthController::registerSuccess');
        $routes->get($key . $csrt->forgot_password, 'AuthController::forgotPassword');
        $routes->get($key . $csrt->reset_password, 'AuthController::resetPassword');
        //profile
        $routes->get($key . $csrt->profile . '/(:any)', 'ProfileController::profile/$1');
        $routes->get($key . $csrt->wishlist . '/(:any)', 'ProfileController::wishlist/$1');
        $routes->get($key . $csrt->wishlist, 'HomeController::guestWishlist/$1');
        $routes->get($key . $csrt->followers . '/(:any)', 'ProfileController::followers/$1');
        $routes->get($key . $csrt->following . '/(:any)', 'ProfileController::following/$1');
        $routes->get($key . $csrt->reviews . '/(:any)', 'ProfileController::reviews/$1');
        //settings
        $routes->get($key . $csrt->settings, 'ProfileController::editProfile');
        $routes->get($key . $csrt->settings . '/' . $csrt->edit_profile, 'ProfileController::editProfile');
        $routes->get($key . $csrt->settings . '/' . $csrt->social_media, 'ProfileController::socialMedia');
        $routes->get($key . $csrt->settings . '/' . $csrt->change_password, 'ProfileController::changePassword');
        $routes->get($key . $csrt->settings . '/' . $csrt->shipping_address, 'ProfileController::shippingAddress');
        $routes->get($key . $csrt->settings . '/' . $csrt->change_password, 'ProfileController::changePassword');
        $routes->get($key . $csrt->settings . '/' . $csrt->change_password, 'ProfileController::changePassword');
        //product
        $routes->get($key . $csrt->select_membership_plan, 'HomeController::renewMembershipPlan');
        $routes->get($key . $csrt->start_selling . '/' . $csrt->select_membership_plan, 'HomeController::selectMembershipPlan');
        $routes->get($key . $csrt->start_selling, 'HomeController::startSelling');
        $routes->get($key . $csrt->search, 'HomeController::search');
        $routes->get($key . $csrt->products, 'HomeController::products');
        $routes->get($key . $csrt->downloads, 'ProfileController::downloads');
        //blog
        $routes->get($key . $csrt->blog, 'HomeController::blog');
        $routes->get($key . $csrt->blog . '/' . $csrt->tag . '/(:any)', 'HomeController::tag/$1');
        $routes->get($key . $csrt->blog . '/(:any)/(:any)', 'HomeController::post/$1/$2');
        $routes->get($key . $csrt->blog . '/(:any)', 'HomeController::blogCategory/$1');
        //shops
        $routes->get($key . $csrt->shops, 'HomeController::shops');
        //contact
        $routes->get($key . $csrt->contact, 'HomeController::contact');
        //messages
        $routes->get($key . $csrt->messages, 'HomeController::conversationMessages');
        //rss feeds
        $routes->get($key . $csrt->rss_feeds, 'RssController::rssFeeds');
        $routes->get($key . 'rss/' . $csrt->latest_products, 'RssController::latestProducts');
        $routes->get($key . 'rss/' . $csrt->featured_products, 'RssController::featuredProducts');
        $routes->get($key . 'rss/' . $csrt->category . '/(:any)', 'RssController::rssByCategory/$1');
        $routes->get($key . 'rss/' . $csrt->seller . '/(:any)', 'RssController::rssBySeller/$1');
        //cart
        $routes->get($key . $csrt->cart, 'CartController::cart');
        $routes->get($key . $csrt->cart . '/' . $csrt->shipping, 'CartController::shipping');
        $routes->get($key . $csrt->cart . '/' . $csrt->payment_method, 'CartController::paymentMethod');
        $routes->get($key . $csrt->cart . '/' . $csrt->payment, 'CartController::payment');
        //orders
        $routes->get($key . $csrt->orders, 'OrderController::orders');
        $routes->get($key . $csrt->order_details . '/(:num)', 'OrderController::order/$1');
        $routes->get($key . $csrt->order_completed . '/(:num)', 'CartController::orderCompleted/$1');
        $routes->get($key . $csrt->promote_payment_completed, 'CartController::promotePaymentCompleted');
        $routes->get($key . $csrt->membership_payment_completed, 'CartController::membershipPaymentCompleted');
        $routes->get($key . 'invoice/(:num)', 'HomeController::invoice/$1');
        $routes->get($key . 'invoice-promotion/(:num)', 'HomeController::invoicePromotion/$1');
        $routes->get($key . 'invoice-membership/(:num)', 'HomeController::invoiceMembership/$1');
        //refund
        $routes->get($key . $csrt->refund_requests, 'OrderController::refundRequests');
        $routes->get($key . $csrt->refund_requests . '/(:num)', 'OrderController::refund/$1');
        //bidding
        $routes->get($key . $csrt->quote_requests, 'OrderController::quoteRequests');
        //terms & conditions
        $routes->get($key . $csrt->terms_conditions, 'HomeController::termsConditions');
        //dashboard
        $routes->get($key . $csrt->dashboard, 'DashboardController::index');
        $routes->get($key . $csrt->dashboard . '/' . $csrt->add_product, 'DashboardController::addProduct');
        $routes->get($key . $csrt->dashboard . '/' . $csrt->product . '/' . $csrt->product_details . '/(:num)', 'DashboardController::editProductDetails/$1');
        $routes->get($key . $csrt->dashboard . '/' . $csrt->edit_product . '/(:num)', 'DashboardController::editProduct/$1');
        $routes->get($key . $csrt->dashboard . '/' . $csrt->products, 'DashboardController::products');
        $routes->get($key . $csrt->dashboard . '/' . $csrt->bulk_product_upload, 'DashboardController::bulkProductUpload');
        $routes->get($key . $csrt->dashboard . '/' . $csrt->sales, 'DashboardController::sales');
        $routes->get($key . $csrt->dashboard . '/' . $csrt->sale . '/(:num)', 'DashboardController::sale/$1');
        $routes->get($key . $csrt->dashboard . '/' . $csrt->earnings, 'DashboardController::earnings');
        $routes->get($key . $csrt->dashboard . '/' . $csrt->withdraw_money, 'DashboardController::withdrawMoney');
        $routes->get($key . $csrt->dashboard . '/' . $csrt->payouts, 'DashboardController::payouts');
        $routes->get($key . $csrt->dashboard . '/' . $csrt->set_payout_account, 'DashboardController::setPayoutAccount');
        $routes->get($key . $csrt->dashboard . '/' . $csrt->quote_requests, 'DashboardController::quoteRequests');
        $routes->get($key . $csrt->dashboard . '/' . $csrt->payment_history, 'DashboardController::paymentHistory');
        $routes->get($key . $csrt->dashboard . '/' . $csrt->comments, 'DashboardController::comments');
        $routes->get($key . $csrt->dashboard . '/' . $csrt->reviews, 'DashboardController::reviews');
        $routes->get($key . $csrt->dashboard . '/' . $csrt->shop_settings, 'DashboardController::shopSettings');
        $routes->get($key . $csrt->dashboard . '/' . $csrt->shipping_settings, 'DashboardController::shippingSettings');
        $routes->get($key . $csrt->dashboard . '/' . $csrt->add_shipping_zone, 'DashboardController::addShippingZone');
        $routes->get($key . $csrt->dashboard . '/' . $csrt->edit_shipping_zone . '/(:num)', 'DashboardController::editShippingZone/$1');
        $routes->get($key . $csrt->dashboard . '/' . $csrt->coupons, 'DashboardController::coupons');
        $routes->get($key . $csrt->dashboard . '/' . $csrt->add_coupon, 'DashboardController::addCoupon');
        $routes->get($key . $csrt->dashboard . '/' . $csrt->edit_coupon . '/(:num)', 'DashboardController::editCoupon/$1');
        $routes->get($key . $csrt->dashboard . '/' . $csrt->refund_requests, 'DashboardController::refundRequests');
        $routes->get($key . $csrt->dashboard . '/' . $csrt->refund_requests . '/(:num)', 'DashboardController::refund/$1');
        //help center
        $routes->get($key . $csrt->help_center, 'SupportController::helpCenter');
        $routes->get($key . $csrt->help_center . '/' . $csrt->tickets, 'SupportController::tickets');
        $routes->get($key . $csrt->help_center . '/' . $csrt->submit_request, 'SupportController::submitRequest');
        $routes->get($key . $csrt->help_center . '/' . $csrt->ticket . '/(:num)', 'SupportController::ticket/$1');
        $routes->get($key . $csrt->help_center . '/' . $csrt->search, 'SupportController::search');
        $routes->get($key . $csrt->help_center . '/' . $csrt->ticket . '/(:num)', 'SupportController::ticket/$1');
        $routes->get($key . $csrt->help_center . '/(:any)/(:any)', 'SupportController::article/$1/$2');
        $routes->get($key . $csrt->help_center . '/(:any)', 'SupportController::category/$1');

        if ($generalSettings->site_lang != $language->id) {
            $routes->get($key . '(:any)/(:any)', 'HomeController::subCategory/$1/$2');
            $routes->get($key . '(:any)', 'HomeController::any/$1');
        }
    }
}
$enableAny = true;
$uri = $_SERVER['REQUEST_URI'];
$controllers = ['Admin', 'Ajax', 'Auth', 'Blog', 'Cart', 'Category', 'Common', 'Dashboard', 'Earnings', 'File', 'Home', 'Language',
    'Membership', 'OrderAdmin', 'Order', 'Product', 'Profile', 'Rss', 'SupportAdmin', 'Support', 'Variation'];
foreach ($controllers as $controller) {
    $controller = '/' . $controller . 'Controller/';
    if (strpos($uri, $controller) !== false) {
        $enableAny = false;
    }
}
if ($enableAny) {
    $routes->get('(:any)/(:any)', 'HomeController::subCategory/$1/$2');
    $routes->get('(:any)', 'HomeController::any/$1');
}

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= esc($title); ?> - <?= esc($generalSettings->application_name); ?></title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="shortcut icon" type="image/png" href="<?= getFavicon(); ?>"/>
    <?= csrf_meta(); ?>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <link rel="stylesheet" href="<?= base_url('assets/admin/vendor/font-awesome/css/font-awesome.min.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/admin/vendor/bootstrap/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/admin/vendor/icheck/square/purple.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/admin/vendor/icheck/square/blue.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/admin/vendor/datatables/dataTables.bootstrap.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/admin/vendor/datatables/jquery.dataTables_themeroller.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/admin/vendor/tagsinput/jquery.tagsinput.min.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/admin/vendor/pace/pace.min.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/admin/vendor/magnific-popup/magnific-popup.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/admin/css/plugins-2.3.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/admin/css/AdminLTE.min.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/admin/css/_all-skins.min.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/admin/css/main-2.3.min.css'); ?>">
    <script>var directionality = 'ltr';</script>
    <?php if ($activeLang->text_direction == 'rtl'): ?>
        <link href="<?= base_url('assets/admin/css/rtl.css'); ?>" rel="stylesheet"/>
        <script>directionality = 'rtl';</script>
    <?php endif; ?>
    <script src="<?= base_url('assets/admin/js/jquery.min.js'); ?>"></script>
    <script>
        var MdsConfig = {
            baseURL: '<?= base_url(); ?>',
            csrfTokenName: '<?= csrf_token() ?>',
            sysLangId: '<?= $activeLang->id; ?>',
            directionality: <?= $baseVars->rtl ? 'true' : 'false'; ?>,
            textOk: "<?= trans("ok", true); ?>",
            textCancel: "<?= trans("cancel", true); ?>",
            textNone: "<?= trans("none", true); ?>",
            textProcessing: "<?= trans("processing", true); ?>",
            textSelectImage: "<?= trans("select_image", true); ?>",
        }
    </script>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    <header class="main-header">
        <div class="main-header-inner">
            <nav class="navbar navbar-static-top">
                <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button"><i class="fa fa-bars" aria-hidden="true"></i></a>
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <li><a class="btn btn-sm btn-success pull-left btn-site-prev" target="_blank" href="<?= base_url(); ?>"><i class="fa fa-eye"></i> <?= trans("view_site"); ?></a></li>
                        <li class="dropdown user-menu">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">
                                <i class="fa fa-globe"></i>&nbsp;
                                <?= esc($activeLang->name); ?>
                                <span class="fa fa-caret-down"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <?php if (!empty($activeLanguages)):
                                    foreach ($activeLanguages as $language): ?>
                                        <li>
                                            <form action="<?= base_url('AdminController/setActiveLanguagePost'); ?>" method="post">
                                                <?= csrf_field(); ?>
                                                <button type="submit" value="<?= $language->id; ?>" name="lang_id" class="control-panel-lang-btn"><?= characterLimiter($language->name, 20, '...'); ?></button>
                                            </form>
                                        </li>
                                    <?php endforeach;
                                endif; ?>
                            </ul>
                        </li>
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <img src="<?= getUserAvatar(user()); ?>" class="user-image" alt="">
                                <span class="hidden-xs"><?= esc(getUsername(user())); ?> <i class="fa fa-caret-down"></i> </span>
                            </a>
                            <ul class="dropdown-menu  pull-right" role="menu" aria-labelledby="user-options">
                                <?php if (isVendor()): ?>
                                    <li><a href="<?= dashboardUrl(); ?>"><i class="fa fa-th-large"></i> <?= trans("dashboard"); ?></a></li>
                                <?php endif; ?>
                                <li><a href="<?= generateProfileUrl(user()->slug); ?>"><i class="fa fa-user"></i> <?= trans("profile"); ?></a></li>
                                <li><a href="<?= generateUrl('settings'); ?>"><i class="fa fa-cog"></i> <?= trans("update_profile"); ?></a></li>
                                <li><a href="<?= generateUrl('settings', 'change_password'); ?>"><i class="fa fa-lock"></i> <?= trans("change_password"); ?></a></li>
                                <li class="divider"></li>
                                <li><a href="<?= generateUrl('logout'); ?>"><i class="fa fa-sign-out"></i> <?= trans("logout"); ?></a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>
    <aside class="main-sidebar" style="background-color: #343B4A;">
        <section class="sidebar sidebar-scrollbar">
            <a href="<?= adminUrl(); ?>" class="logo">
                <span class="logo-mini"></span>
                <span class="logo-lg"><b><?= esc($generalSettings->application_name); ?></b> <?= trans("panel"); ?></span>
            </a>
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="<?= getUserAvatar(user()); ?>" class="img-circle" alt="">
                </div>
                <div class="pull-left info">
                    <p><?= esc(getUsername(user())); ?></p>
                    <a href="#"><i class="fa fa-circle text-success"></i> <?= trans("online"); ?></a>
                </div>
            </div>
            <ul class="sidebar-menu" data-widget="tree">
                <li class="header"><?= trans("navigation"); ?></li>
                <li class="nav-home">
                    <a href="<?= adminUrl(); ?>"><i class="fa fa-home"></i> <span><?= trans("home"); ?></span></a>
                </li>
                <?php if (hasPermission('navigation')): ?>
                    <li class="nav-navigation">
                        <a href="<?= adminUrl('navigation'); ?>"><i class="fa fa-th"></i><span><?= trans("navigation"); ?></span></a>
                    </li>
                <?php endif;
                if (hasPermission('slider')):?>
                    <li class="nav-slider">
                        <a href="<?= adminUrl('slider'); ?>"><i class="fa fa-sliders"></i><span><?= trans("slider"); ?></span></a>
                    </li>
                <?php endif;
                if (hasPermission('homepage_manager')):?>
                    <li class="nav-homepage-manager">
                        <a href="<?= adminUrl('homepage-manager'); ?>"><i class="fa fa-clone"></i><span><?= trans("homepage_manager"); ?></span></a>
                    </li>
                <?php endif;
                if (hasPermission('orders')):?>
                    <li class="header"><?= trans("orders"); ?></li>
                    <li class="treeview<?php isAdminNavActive(['orders', 'transactions', 'order-bank-transfers', 'order-details']); ?>">
                        <a href="#"><i class="fa fa-shopping-cart"></i><span><?= trans("orders"); ?></span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                        <ul class="treeview-menu">
                            <li class="nav-orders"><a href="<?= adminUrl('orders'); ?>"> <?= trans("orders"); ?></a></li>
                            <li class="nav-transactions"><a href="<?= adminUrl('transactions'); ?>"> <?= trans("transactions"); ?></a></li>
                            <li class="nav-order-bank-transfers"><a href="<?= adminUrl('order-bank-transfers'); ?>"> <?= trans("bank_transfer_notifications"); ?></a></li>
                        </ul>
                    </li>
                <?php endif;
                if (hasPermission('digital_sales')):?>
                    <li class="nav-digital-sales">
                        <a href="<?= adminUrl('digital-sales'); ?>"><i class="fa fa-shopping-bag"></i><span><?= trans("digital_sales"); ?></span></a>
                    </li>
                <?php endif;
                if (hasPermission('earnings')):?>
                    <li class="treeview<?php isAdminNavActive(['earnings', 'seller-balances', 'update-seller-balance']); ?>">
                        <a href="#"><i class="fa fa-money" aria-hidden="true"></i><span><?= trans("earnings"); ?></span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                        <ul class="treeview-menu">
                            <li class="nav-earnings"><a href="<?= adminUrl('earnings'); ?>"> <?= trans("earnings"); ?></a></li>
                            <li class="nav-seller-balances"><a href="<?= adminUrl('seller-balances'); ?>"> <?= trans("seller_balances"); ?></a></li>
                        </ul>
                    </li>
                <?php endif;
                if (hasPermission('payouts')):?>
                    <li class="treeview<?php isAdminNavActive(['add-payout', 'payout-requests', 'completed-payouts', 'payout-settings']); ?>">
                        <a href="#"><i class="fa fa-credit-card" aria-hidden="true"></i><span><?= trans("payouts"); ?></span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                        <ul class="treeview-menu">
                            <li class="nav-add-payout"><a href="<?= adminUrl('add-payout'); ?>"> <?= trans("add_payout"); ?></a></li>
                            <li class="nav-payout-requests"><a href="<?= adminUrl('payout-requests'); ?>"> <?= trans("payout_requests"); ?></a></li>
                            <li class="nav-payout-settings"><a href="<?= adminUrl('payout-settings'); ?>"> <?= trans("payout_settings"); ?></a></li>
                        </ul>
                    </li>
                <?php endif;
                if (hasPermission('refund_requests')):?>
                    <li class="nav-refund-requests">
                        <a href="<?= adminUrl('refund-requests'); ?>"><i class="fa fa-flag"></i><span><?= trans("refund_requests"); ?></span></a>
                    </li>
                <?php endif;
                if (hasPermission('products')):?>
                    <li class="header"><?= trans("products"); ?></li>
                    <li class="treeview<?php isAdminNavActive(['products']); ?>">
                        <a href="#"><i class="fa fa-shopping-basket angle-left" aria-hidden="true"></i><span><?= trans("products"); ?></span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                        <ul class="treeview-menu">
                            <li class="<?= inputGet('list') == 'all' || empty(inputGet('list')) ? 'active' : ''; ?>"><a href="<?= adminUrl('products?list=all'); ?>"> <?= trans("products"); ?></a></li>
                            <li class="<?= inputGet('list') == 'special' ? 'active' : ''; ?>"><a href="<?= adminUrl('products?list=special'); ?>"> <?= trans("special_offers"); ?></a></li>
                            <li class="<?= inputGet('list') == 'pending' ? 'active' : ''; ?>"><a href="<?= adminUrl('products?list=pending'); ?>"> <?= trans("pending_products"); ?></a></li>
                            <li class="<?= inputGet('list') == 'hidden' ? 'active' : ''; ?>"><a href="<?= adminUrl('products?list=hidden'); ?>"> <?= trans("hidden_products"); ?></a></li>
                            <?php if ($generalSettings->membership_plans_system == 1): ?>
                                <li class="<?= inputGet('list') == 'expired' ? 'active' : ''; ?>"><a href="<?= adminUrl('products?list=expired'); ?>"> <?= trans("expired_products"); ?></a></li>
                            <?php endif; ?>
                            <li class="<?= inputGet('list') == 'sold' ? 'active' : ''; ?>"><a href="<?= adminUrl('products?list=sold'); ?>"> <?= trans("sold_products"); ?></a></li>
                            <li class="<?= inputGet('list') == 'drafts' ? 'active' : ''; ?>"><a href="<?= adminUrl('products?list=drafts'); ?>"> <?= trans("drafts"); ?></a></li>
                            <li class="<?= inputGet('list') == 'deleted' ? 'active' : ''; ?>"><a href="<?= adminUrl('products?list=deleted'); ?>"> <?= trans("deleted_products"); ?></a></li>
                            <li><a href="<?= generateDashUrl('add_product'); ?>" target="_blank"> <?= trans("add_product"); ?></a></li>
                            <li><a href="<?= generateDashUrl('bulk_product_upload'); ?>"> <?= trans("bulk_product_upload"); ?></a></li>
                        </ul>
                    </li>
                <?php endif;
                if (hasPermission('featured_products')):?>
                    <li class="treeview<?php isAdminNavActive(['featured-products', 'featured-products-pricing', 'featured-products-transactions']); ?>">
                        <a href="#"><i class="fa fa-dollar" aria-hidden="true"></i><span><?= trans("featured_products"); ?></span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                        <ul class="treeview-menu">
                            <li class="nav-featured-products"><a href="<?= adminUrl('featured-products'); ?>"> <?= trans("products"); ?></a></li>
                            <li class="nav-featured-products-pricing"><a href="<?= adminUrl('featured-products-pricing'); ?>"> <?= trans("pricing"); ?></a></li>
                            <li class="nav-featured-products-transactions"><a href="<?= adminUrl('featured-products-transactions'); ?>"> <?= trans("transactions"); ?></a></li>
                        </ul>
                    </li>
                <?php endif;
                if (hasPermission('quote_requests')):?>
                    <li class="nav-quote-requests">
                        <a href="<?= adminUrl('quote-requests'); ?>"><i class="fa fa-tag"></i> <span><?= trans("quote_requests"); ?></span></a>
                    </li>
                <?php endif;
                if (hasPermission('categories')):?>
                    <li class="treeview<?php isAdminNavActive(['categories', 'add-category', 'update-category', 'bulk-category-upload']); ?>">
                        <a href="#"><i class="fa fa-folder-open"></i><span><?= trans("categories"); ?></span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                        <ul class="treeview-menu">
                            <li class="nav-categories"><a href="<?= adminUrl('categories'); ?>"> <?= trans("categories"); ?></a></li>
                            <?php if (isAdmin()): ?>
                                <li class="nav-bulk-category-upload"><a href="<?= adminUrl('bulk-category-upload'); ?>"> <?= trans("bulk_category_upload"); ?></a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif;
                if (hasPermission('custom_fields')):?>
                    <li class="nav-custom-fields">
                        <a href="<?= adminUrl('custom-fields'); ?>"><i class="fa fa-plus-square-o"></i> <span><?= trans("custom_fields"); ?></span></a>
                    </li>
                <?php endif;
                if (hasPermission('pages') || hasPermission('blog') || hasPermission('location')):?>
                    <li class="header"><?= trans("content"); ?></li>
                    <?php if (hasPermission('pages')): ?>
                        <li class="nav-pages">
                            <a href="<?= adminUrl('pages'); ?>"><i class="fa fa-file"></i><span><?= trans("pages"); ?></span></a>
                        </li>
                    <?php endif;
                    if (hasPermission('blog')):?>
                        <li class="treeview<?php isAdminNavActive(['blog-add-post', 'blog-posts', 'blog-categories', 'edit-blog-post', 'edit-blog-category']); ?>">
                            <a href="#"><i class="fa fa-file-text"></i><span><?= trans("blog"); ?></span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                            <ul class="treeview-menu">
                                <li class="nav-blog-posts"><a href="<?= adminUrl('blog-posts'); ?>"> <?= trans("posts"); ?></a></li>
                                <li class="nav-blog-categories"><a href="<?= adminUrl('blog-categories'); ?>"> <?= trans("categories"); ?></a></li>
                            </ul>
                        </li>
                    <?php endif;
                    if (hasPermission('location')):?>
                        <li class="treeview<?php isAdminNavActive(['countries', 'states', 'cities', 'add-country', 'add-state', 'add-city', 'update-country', 'update-state', 'update-city']); ?>">
                            <a href="#"><i class="fa fa-map-marker"></i><span><?= trans("location"); ?></span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                            <ul class="treeview-menu">
                                <li class="nav-countries"><a href="<?= adminUrl('countries'); ?>"> <?= trans("countries"); ?></a></li>
                                <li class="nav-states"><a href="<?= adminUrl('states'); ?>"> <?= trans("states"); ?></a></li>
                                <li class="nav-cities"><a href="<?= adminUrl('cities'); ?>"> <?= trans("cities"); ?></a></li>
                            </ul>
                        </li>
                    <?php endif;
                endif;
                if (hasPermission('membership')):?>
                    <li class="header"><?= trans("membership"); ?></li>
                    <li class="nav-users">
                        <a href="<?= adminUrl('users'); ?>"><i class="fa fa-users"></i><span><?= trans("users"); ?></span></a>
                    </li>
                    <li class="treeview<?php isAdminNavActive(['membership-plans', 'transactions-membership']); ?>">
                        <a href="#"><i class="fa fa-adjust"></i><span><?= trans("membership_plans"); ?></span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                        <ul class="treeview-menu">
                            <li class="nav-membership-plans"><a href="<?= adminUrl('membership-plans'); ?>"> <?= trans("membership_plans"); ?></a></li>
                            <li class="nav-transactions-membership"><a href="<?= adminUrl('transactions-membership'); ?>"> <?= trans("transactions"); ?></a></li>
                        </ul>
                    </li>
                    <li class="nav-shop-opening-requests">
                        <a href="<?= adminUrl('shop-opening-requests'); ?>"><i class="fa fa-question-circle"></i><span><?= trans("shop_opening_requests"); ?></span></a>
                    </li>
                    <li class="nav-roles-permissions">
                        <a href="<?= adminUrl('roles-permissions'); ?>"><i class="fa fa-key"></i><span><?= trans("roles_permissions"); ?></span></a>
                    </li>
                <?php endif; ?>
                <li class="header hide li-mt"><?= trans("management_tools"); ?></li>
                <?php $showMtTools = false;
                if (hasPermission('help_center')):
                    $showMtTools = true; ?>
                    <li class="treeview<?php isAdminNavActive(['knowledge-base', 'knowledge-base-categories', 'support-tickets']); ?>">
                        <a href="#"><i class="fa fa-support"></i><span><?= trans("help_center"); ?></span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                        <ul class="treeview-menu">
                            <li class="nav-knowledge-base"><a href="<?= adminUrl('knowledge-base'); ?>"> <?= trans("knowledge_base"); ?></a></li>
                            <li class="nav-support-tickets"><a href="<?= adminUrl('support-tickets'); ?>"> <?= trans("support_tickets"); ?></a></li>
                        </ul>
                    </li>
                <?php endif;
                if (hasPermission('storage')):
                    $showMtTools = true; ?>
                    <li class="nav-storage">
                        <a href="<?= adminUrl('storage'); ?>"><i class="fa fa-cloud-upload"></i><span><?= trans("storage"); ?></span></a>
                    </li>
                <?php endif;
                if (hasPermission('cache_system')):
                    $showMtTools = true; ?>
                    <li class="nav-cache-system">
                        <a href="<?= adminUrl('cache-system'); ?>"><i class="fa fa-database"></i><span><?= trans("cache_system"); ?></span></a>
                    </li>
                <?php endif;
                if (hasPermission('seo_tools')):
                    $showMtTools = true; ?>
                    <li class="nav-seo-tools">
                        <a href="<?= adminUrl('seo-tools'); ?>"><i class="fa fa-wrench"></i> <span><?= trans("seo_tools"); ?></span></a>
                    </li>
                <?php endif;
                if (hasPermission('ad_spaces')):
                    $showMtTools = true; ?>
                    <li class="nav-ad-spaces">
                        <a href="<?= adminUrl('ad-spaces'); ?>"><i class="fa fa-dollar"></i> <span><?= trans("ad_spaces"); ?></span></a>
                    </li>
                <?php endif;
                if (hasPermission('contact_messages')):
                    $showMtTools = true; ?>
                    <li class="nav-contact-messages">
                        <a href="<?= adminUrl('contact-messages'); ?>"><i class="fa fa-paper-plane" aria-hidden="true"></i><span><?= trans("contact_messages"); ?></span></a>
                    </li>
                <?php endif;
                if (hasPermission('reviews')):
                    $showMtTools = true; ?>
                    <li class="nav-reviews">
                        <a href="<?= adminUrl('reviews'); ?>"><i class="fa fa-star"></i><span><?= trans("reviews"); ?></span></a>
                    </li>
                <?php endif;
                if (hasPermission('comments')):
                    $showMtTools = true; ?>
                    <li class="treeview<?php isAdminNavActive(['pending-product-comments', 'pending-blog-comments', 'product-comments', 'blog-comments']); ?>">
                        <a href="#"><i class="fa fa-comments"></i><span><?= trans("comments"); ?></span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                        <ul class="treeview-menu">
                            <?php if ($generalSettings->comment_approval_system == 1): ?>
                                <li class="nav-pending-product-comments"><a href="<?= adminUrl('pending-product-comments'); ?>"> <?= trans("product_comments"); ?></a></li>
                                <li class="nav-pending-blog-comments"><a href="<?= adminUrl('pending-blog-comments'); ?>"> <?= trans("blog_comments"); ?></a></li>
                            <?php else: ?>
                                <li class="nav-product-comments"><a href="<?= adminUrl('product-comments'); ?>"> <?= trans("product_comments"); ?></a></li>
                                <li class="nav-blog-comments"><a href="<?= adminUrl('blog-comments'); ?>"> <?= trans("blog_comments"); ?></a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif;
                if (hasPermission('abuse_reports')):
                    $showMtTools = true; ?>
                    <li class="nav-abuse-reports">
                        <a href="<?= adminUrl('abuse-reports'); ?>"><i class="fa fa-warning" aria-hidden="true"></i><span><?= trans("abuse_reports"); ?></span></a>
                    </li>
                <?php endif;
                if (hasPermission('newsletter')):
                    $showMtTools = true; ?>
                    <li class="nav-newsletter">
                        <a href="<?= adminUrl('newsletter'); ?>"><i class="fa fa-envelope-o" aria-hidden="true"></i><span><?= trans("newsletter"); ?></span></a>
                    </li>
                <?php endif;
                if (hasPermission('preferences')):?>
                    <li class="header text-uppercase"><?= trans("settings"); ?></li>
                    <?php if (hasPermission('preferences')): ?>
                        <li class="nav-preferences">
                            <a href="<?= adminUrl('preferences'); ?>"><i class="fa fa-check-square-o"></i><span><?= trans("preferences"); ?></span></a>
                        </li>
                    <?php endif;
                    if (hasPermission('general_settings')):?>
                        <li class="treeview<?php isAdminNavActive(['general-settings', 'language-settings', 'social-login', 'update-language', 'edit-translations', 'email-settings', 'visual-settings', 'font-settings']); ?>">
                            <a href="#"><i class="fa fa-cog"></i><span><?= trans("general_settings"); ?></span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                            <ul class="treeview-menu">
                                <li class="nav-general-settings"><a href="<?= adminUrl('general-settings'); ?>"> <?= trans("general_settings"); ?></a></li>
                                <li class="nav-language-settings"><a href="<?= adminUrl('language-settings'); ?>"> <?= trans("language_settings"); ?></a></li>
                                <li class="nav-email-settings"><a href="<?= adminUrl('email-settings'); ?>"> <?= trans("email_settings"); ?></a></li>
                                <li class="nav-social-login"><a href="<?= adminUrl('social-login'); ?>"> <?= trans("social_login"); ?></a></li>
                                <li class="nav-visual-settings"><a href="<?= adminUrl('visual-settings'); ?>"> <?= trans("visual_settings"); ?></a></li>
                                <li class="nav-font-settings"><a href="<?= adminUrl('font-settings'); ?>"> <?= trans("font_settings"); ?></a></li>
                            </ul>
                        </li>
                    <?php endif;
                    if (hasPermission('product_settings')):?>
                        <li class="nav-product-settings">
                            <a href="<?= adminUrl('product-settings'); ?>"><i class="fa fa-list-ul"></i> <span><?= trans("product_settings"); ?></span></a>
                        </li>
                    <?php endif;
                    if (hasPermission('payment_settings')):?>
                        <li class="treeview<?php isAdminNavActive(['payment-settings', 'currency-settings']); ?>">
                            <a href="#"><i class="fa fa-credit-card-alt"></i><span><?= trans("payment_settings"); ?></span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                            <ul class="treeview-menu">
                                <li class="nav-payment-settings"><a href="<?= adminUrl('payment-settings'); ?>"> <?= trans("payment_settings"); ?></a></li>
                                <li class="nav-currency-settings"><a href="<?= adminUrl('currency-settings'); ?>"> <?= trans("currency_settings"); ?></a></li>
                            </ul>
                        </li>
                    <?php endif;
                    if (hasPermission('system_settings')):?>
                        <li class="treeview<?php isAdminNavActive(['system-settings', 'route-settings']); ?>">
                            <a href="#"><i class="fa fa-cogs"></i><span><?= trans("system_settings"); ?></span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                            <ul class="treeview-menu">
                                <li class="nav-system-settings"><a href="<?= adminUrl('system-settings'); ?>"> <?= trans("system_settings"); ?></a></li>
                                <li class="nav-route-settings"><a href="<?= adminUrl('route-settings'); ?>"> <?= trans("route_settings"); ?></a></li>
                            </ul>
                        </li>
                    <?php endif;
                endif;
                if (isSuperAdmin()): ?>
                    <li>
                        <div class="database-backup">
                            <form action="<?= base_url('AdminController/downloadDatabaseBackup'); ?>" method="post">
                                <?= csrf_field(); ?>
                                <button type="submit" class="btn btn-block"><i class="fa fa-download"></i>&nbsp;&nbsp;<?= trans("download_database_backup"); ?></button>
                            </form>
                        </div>
                    </li>
                <?php endif; ?>
                <li class="header">&nbsp;</li>
            </ul>
        </section>
    </aside>
    <?php
    $segment2 = $segment = getSegmentValue(2);
    $segment3 = $segment = getSegmentValue(3);
    $uriString = $segment2;
    if (!empty($segment3)) {
        $uriString .= '-' . $segment3;
    } ?>
    <style>
        <?php if(!empty($uriString)):
        echo '.nav-'.$uriString.' > a{color: #fff !important;}';
        else:
        echo '.nav-home > a{color: #fff !important;}';
        endif;
       if ($showMtTools):
        echo '.li-mt {display: block !important;}';
        endif; ?>
    </style>
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-sm-12">
                    <?= view('admin/includes/_messages'); ?>
                </div>
            </div>
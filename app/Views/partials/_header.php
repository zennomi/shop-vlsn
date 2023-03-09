<!DOCTYPE html>
<html lang="<?= $activeLang->short_form; ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title><?= esc($title); ?> - <?= esc($baseSettings->site_title); ?></title>
<meta name="description" content="<?= esc($description); ?>"/>
<meta name="keywords" content="<?= esc($keywords); ?>"/>
<meta name="author" content="<?= esc($generalSettings->application_name); ?>"/>
<link rel="shortcut icon" type="image/png" href="<?= getFavicon(); ?>"/>
<meta property="og:locale" content="en-US"/>
<meta property="og:site_name" content="<?= esc($generalSettings->application_name); ?>"/>
<?php if (isset($showOGTags)): ?>
<meta property="og:type" content="<?= !empty($ogType) ? $ogType : 'website'; ?>"/>
<meta property="og:title" content="<?= !empty($ogTitle) ? $ogTitle : 'index'; ?>"/>
<meta property="og:description" content="<?= $ogDescription; ?>"/>
<meta property="og:url" content="<?= $ogURL; ?>"/>
<meta property="og:image" content="<?= $ogImage; ?>"/>
<meta property="og:image:width" content="<?= !empty($ogWidth) ? $ogWidth : 250; ?>"/>
<meta property="og:image:height" content="<?= !empty($ogHeight) ? $ogHeight : 250; ?>"/>
<meta property="article:author" content="<?= !empty($ogAuthor) ? $ogAuthor : ''; ?>"/>
<meta property="fb:app_id" content="<?= esc($generalSettings->facebook_app_id); ?>"/>
<?php if (!empty($ogTags)):foreach ($ogTags as $tag): ?>
<meta property="article:tag" content="<?= esc($tag->tag); ?>"/>
<?php endforeach; endif; ?>
<meta property="article:published_time" content="<?= !empty($ogPublishedTime) ? $ogPublishedTime : ''; ?>"/>
<meta property="article:modified_time" content="<?= !empty($ogModifiedTime) ? $ogModifiedTime : ''; ?>"/>
<meta name="twitter:card" content="summary_large_image"/>
<meta name="twitter:site" content="@<?= esc($generalSettings->application_name); ?>"/>
<meta name="twitter:creator" content="@<?= esc($ogCreator); ?>"/>
<meta name="twitter:title" content="<?= esc($ogTitle); ?>"/>
<meta name="twitter:description" content="<?= esc($ogDescription); ?>"/>
<meta name="twitter:image" content="<?= $ogImage; ?>"/>
<?php else: ?>
<meta property="og:image" content="<?= getLogo(); ?>"/>
<meta property="og:image:width" content="160"/>
<meta property="og:image:height" content="60"/>
<meta property="og:type" content="website"/>
<meta property="og:title" content="<?= esc($title); ?> - <?= esc($baseSettings->site_title); ?>"/>
<meta property="og:description" content="<?= esc($description); ?>"/>
<meta property="og:url" content="<?= base_url(); ?>"/>
<meta property="fb:app_id" content="<?= esc($generalSettings->facebook_app_id); ?>"/>
<meta name="twitter:card" content="summary_large_image"/>
<meta name="twitter:site" content="@<?= esc($generalSettings->application_name); ?>"/>
<meta name="twitter:title" content="<?= esc($title); ?> - <?= esc($baseSettings->site_title); ?>"/>
<meta name="twitter:description" content="<?= esc($description); ?>"/>
<?php endif;
if ($generalSettings->pwa_status == 1): ?>
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="apple-mobile-web-app-title" content="<?= esc($generalSettings->application_name); ?>">
<meta name="msapplication-TileImage" content="<?= base_url('assets/img/pwa/144x144.png'); ?>">
<meta name="msapplication-TileColor" content="#2F3BA2">
<link rel="manifest" href="<?= base_url('manifest.json'); ?>">
<link rel="apple-touch-icon" href="<?= base_url('assets/img/pwa/144x144.png'); ?>">
<?php endif; ?>
<link rel="canonical" href="<?= getCurrentUrl(); ?>"/>
<?= csrf_meta(); ?>
<?php if ($generalSettings->multilingual_system == 1):
foreach ($activeLanguages as $language):?>
<link rel="alternate" href="<?= convertUrlByLanguage($language); ?>" hreflang="<?= esc($language->language_code); ?>"/>
<?php endforeach; endif; ?>
<link rel="stylesheet" href="<?= base_url('assets/vendor/font-icons/css/mds-icons.min.css'); ?>"/>
<?= view('partials/_fonts'); ?>
<link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css'); ?>"/>
<link rel="stylesheet" href="<?= base_url('assets/css/style-2.3.min.css'); ?>"/>
<link rel="stylesheet" href="<?= base_url('assets/css/plugins-2.3.css'); ?>"/>
<?= view('partials/_css_js_header');
if ($baseVars->rtl == true): ?>
<link rel="stylesheet" href="<?= base_url('assets/css/rtl-2.3.min.css'); ?>">
<?php endif; ?>
<?= $generalSettings->google_adsense_code; ?>
<?= $generalSettings->custom_header_codes; ?>

</head>
<body>
<header id="header">
<?= view('partials/_top_bar'); ?>
<div class="main-menu">
<div class="container-fluid">
<div class="row">
<div class="nav-top">
<div class="container">
<div class="row align-items-center">
<div class="col-md-8 nav-top-left">
<div class="row-align-items-center">
<div class="logo">
<a href="<?= langBaseUrl(); ?>"><img src="<?= getLogo(); ?>" alt="logo"></a>
</div>
<div class="top-search-bar<?= $generalSettings->multi_vendor_system != 1 ? ' top-search-bar-single-vendor' : ''; ?>">
<form action="<?= generateUrl('search'); ?>" method="get" id="form_validate_search" class="form_search_main">
<?= csrf_field(); ?>
<div class="left">
<div class="dropdown search-select">
<button type="button" class="btn dropdown-toggle" data-toggle="dropdown"><?= trans("all_categories"); ?></button>
<i class="icon-arrow-down search-select-caret"></i>
<input type="hidden" name="search_category_input" id="input_search_category" value="all">
<div class="dropdown-menu search-categories">
<a class="dropdown-item" data-value="all" href="javascript:void(0)"><?= trans("all_categories"); ?></a>
<?php if (!empty($parentCategories)):
foreach ($parentCategories as $searchCat):?>
<a class="dropdown-item" data-value="<?= $searchCat->id; ?>" href="javascript:void(0)"><?= esc($searchCat->name); ?></a>
<?php endforeach;
endif; ?>
</div>
</div>
</div>
<div class="right">
<input type="text" name="search" maxlength="300" pattern=".*\S+.*" id="input_search" class="form-control input-search" placeholder="<?= trans("search_exp"); ?>" required autocomplete="off">
<button class="btn btn-default btn-search"><i class="icon-search"></i></button>
<div id="response_search_results" class="search-results-ajax"></div>
</div>
</form>
</div>
</div>
</div>
<div class="col-md-4 nav-top-right">
<ul class="nav align-items-center">
<?php if (isSaleActive()): ?>
<li class="nav-item nav-item-cart li-main-nav-right">
<a href="<?= generateUrl('cart'); ?>">
<i class="icon-cart"></i>
<span class="label-nav-icon"><?= trans("cart"); ?></span>
<?php $cartProductCount = getCartProductCount(); ?>
<span class="notification span_cart_product_count <?= $cartProductCount <= 0 ? 'visibility-hidden' : ''; ?>"><?= $cartProductCount; ?></span>
</a>
</li>
<?php endif;
if (authCheck()): ?>
<li class="nav-item li-main-nav-right">
<a href="<?= generateUrl('wishlist') . '/' . user()->slug; ?>">
<i class="icon-heart-o"></i>
<span class="label-nav-icon"><?= trans("wishlist"); ?></span>
</a>
</li>
<?php if ($generalSettings->multi_vendor_system == 1): ?>
<li class="nav-item m-r-0"><a href="<?= generateDashUrl("add_product"); ?>" class="btn btn-md btn-custom btn-sell-now m-r-0"><?= trans("sell_now"); ?></a></li>
<?php endif;
else: ?>
<li class="nav-item li-main-nav-right"><a href="<?= generateUrl('wishlist'); ?>"><i class="icon-heart-o"></i><span class="label-nav-icon"><?= trans("wishlist"); ?></span></a></li>
<?php if ($generalSettings->multi_vendor_system == 1): ?>
<li class="nav-item m-r-0"><a href="javascript:void(0)" class="btn btn-md btn-custom btn-sell-now m-r-0" data-toggle="modal" data-target="#loginModal"><?= trans("sell_now"); ?></a></li>
<?php endif;
endif; ?>
</ul>
</div>
</div>
</div>
</div>
<div class="nav-main">
<?= view("partials/_nav_main"); ?>
</div>
</div>
</div>
</div>
<div class="mobile-nav-container">
<div class="nav-mobile-header">
<div class="container-fluid">
<div class="row">
<div class="nav-mobile-header-container">
<div class="d-flex justify-content-between">
<div class="flex-item item-menu-icon justify-content-start">
<a href="javascript:void(0)" class="btn-open-mobile-nav"><i class="icon-menu"></i></a>
</div>
<div class="flex-item justify-content-center">
<div class="mobile-logo">
<a href="<?= langBaseUrl(); ?>"><img src="<?= getLogo(); ?>" alt="logo" class="logo"></a>
</div>
</div>
<div class="flex-item justify-content-end">
<a class="a-search-icon"><i class="icon-search"></i></a>
<?php if (isSaleActive()): ?>
<a href="<?= generateUrl('cart'); ?>" class="a-mobile-cart"><i class="icon-cart"></i><span class="notification span_cart_product_count"><?= getCartProductCount(); ?></span></a>
<?php endif; ?>
</div>
</div>
</div>
</div>
<div class="row">
<div class="top-search-bar mobile-search-form <?= $generalSettings->multi_vendor_system != 1 ? ' top-search-bar-single-vendor' : ''; ?>">
<form action="<?= generateUrl('search'); ?>" method="get" id="form_validate_search_mobile">
<?= csrf_field(); ?>
<div class="left">
<div class="dropdown search-select">
<button type="button" class="btn dropdown-toggle" data-toggle="dropdown"><?= trans("all_categories"); ?></button>
<i class="icon-arrow-down search-select-caret"></i>
<input type="hidden" name="search_category_input" id="input_search_category_mobile" value="all">
<div class="dropdown-menu search-categories">
<a class="dropdown-item" data-value="all" href="javascript:void(0)"><?= trans("all_categories"); ?></a>
<?php if (!empty($parentCategories)):
foreach ($parentCategories as $searchCat):?>
<a class="dropdown-item" data-value="<?= $searchCat->id; ?>" href="javascript:void(0)"><?= esc($searchCat->name); ?></a>
<?php endforeach;
endif; ?>
</div>
</div>
</div>
<div class="right">
<input type="text" id="input_search_mobile" name="search" maxlength="300" pattern=".*\S+.*" class="form-control input-search" placeholder="<?= trans("search"); ?>" required autocomplete="off">
<button class="btn btn-default btn-search"><i class="icon-search"></i></button>
<div id="response_search_results_mobile" class="search-results-ajax"></div>
</div>
</form>
</div>
</div>
</div>
</div>
</div>
</header>
<div id="overlay_bg" class="overlay-bg"></div>
<?= view("partials/_nav_mobile"); ?>
<input type="hidden" class="search_type_input" name="search_type" value="product">
<?php if (!authCheck()): ?>
<div class="modal fade" id="loginModal" role="dialog">
<div class="modal-dialog modal-dialog-centered login-modal" role="document">
<div class="modal-content">
<div class="auth-box">
<button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
<h4 class="title"><?= trans("login"); ?></h4>
<form id="form_login" novalidate="novalidate">
<div class="social-login">
<?= view('auth/_social_login', ["orText" => trans("login_with_email")]); ?>
</div>
<div id="result-login" class="font-size-13"></div>
<div id="confirmation-result-login" class="font-size-13"></div>
<div class="form-group">
<input type="email" name="email" class="form-control auth-form-input" placeholder="<?= trans("email_address"); ?>" maxlength="255" required>
</div>
<div class="form-group">
<input type="password" name="password" class="form-control auth-form-input" placeholder="<?= trans("password"); ?>" minlength="4" maxlength="255" required>
</div>
<div class="form-group text-right">
<a href="<?= generateUrl("forgot_password"); ?>" class="link-forgot-password"><?= trans("forgot_password"); ?></a>
</div>
<div class="form-group">
<button type="submit" class="btn btn-md btn-custom btn-block"><?= trans("login"); ?></button>
</div>
<p class="p-social-media m-0 m-t-5"><?= trans("dont_have_account"); ?>&nbsp;<a href="<?= generateUrl("register"); ?>" class="link"><?= trans("register"); ?></a></p>
</form>
</div>
</div>
</div>
</div>
<?php endif;
if ($generalSettings->location_search_header == 1): ?>
<div class="modal fade" id="locationModal" role="dialog">
<div class="modal-dialog modal-dialog-centered login-modal location-modal" role="document">
<div class="modal-content">
<div class="auth-box">
<button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
<h4 class="title"><?= trans("select_location"); ?></h4>
<p class="location-modal-description"><?= transWithField("location_explanation", $generalSettings->application_name); ?></p>
<div class="form-group m-b-20">
<div class="input-group input-group-location">
<i class="icon-map-marker"></i>
<input type="text" id="input_location" class="form-control form-input" value="<?= esc($baseVars->defaultLocationInput); ?>" placeholder="<?= trans("enter_location") ?>" autocomplete="off">
<a href="javascript:void(0)" class="btn-reset-location-input<?= empty($baseVars->defaultLocationInput->country_id) ? ' hidden' : ''; ?>"><i class="icon-close"></i></a>
</div>
<div class="search-results-ajax">
<div class="search-results-location">
<div id="response_search_location"></div>
</div>
</div>
<div id="location_id_inputs">
<input type="hidden" name="country" value="<?= $baseVars->defaultLocation->country_id; ?>" class="input-location-filter">
<input type="hidden" name="state" value="<?= $baseVars->defaultLocation->state_id; ?>" class="input-location-filter">
<input type="hidden" name="city" value="<?= $baseVars->defaultLocation->city_id; ?>" class="input-location-filter">
</div>
</div>
<div class="form-group">
<button type="button" id="btn_submit_location" class="btn btn-md btn-custom btn-block"><?= trans("update_location"); ?></button>
</div>
</div>
</div>
</div>
</div>
<?php endif;
if ($generalSettings->newsletter_status == 1 && $generalSettings->newsletter_popup == 1): ?>
<div id="modal_newsletter" class="modal fade modal-center modal-newsletter" role="dialog">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">
<div class="modal-body">
<div class="row">
<div class="col-6 col-left">
<img src="<?= !empty($generalSettings->newsletter_image) ? base_url($generalSettings->newsletter_image) : base_url('assets/img/newsletter_bg.jpg'); ?>" class="newsletter-img">
</div>
<div class="col-6 col-right">
<div class="newsletter-form">
<button type="button" class="close" data-dismiss="modal"><i class="icon-close" aria-hidden="true"></i></button>
<h4 class="modal-title"><?= trans("join_newsletter"); ?></h4>
<p class="modal-desc"><?= trans("newsletter_desc"); ?></p>
<form id="form_newsletter_modal" class="form-newsletter" data-form-type="modal">
<div class="form-group">
<div class="modal-newsletter-inputs">
<input type="email" name="email" class="form-control form-input newsletter-input" placeholder="<?= trans('enter_email') ?>">
<button type="submit" id="btn_modal_newsletter" class="btn"><?= trans("subscribe"); ?></button>
</div>
</div>
<input type="text" name="url">
<div id="modal_newsletter_response" class="text-center modal-newsletter-response">
<div class="form-group text-center m-b-0 text-close">
<button type="button" class="text-close" data-dismiss="modal"><?= trans("no_thanks"); ?></button>
</div>
</div>
</form>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<?php endif; ?>
<div id="menu-overlay"></div>
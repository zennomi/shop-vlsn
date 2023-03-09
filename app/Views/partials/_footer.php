<footer id="footer">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="footer-top">
                    <div class="row">
                        <div class="col-12 col-md-3 footer-widget">
                            <div class="row-custom">
                                <div class="footer-logo">
                                    <a href="<?= langBaseUrl(); ?>"><img src="<?= getLogo(); ?>" alt="logo"></a>
                                </div>
                            </div>
                            <div class="row-custom">
                                <div class="footer-about">
                                    <?= $baseSettings->about_footer; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-3 footer-widget">
                            <div class="nav-footer">
                                <div class="row-custom">
                                    <h4 class="footer-title"><?= trans("footer_quick_links"); ?></h4>
                                </div>
                                <div class="row-custom">
                                    <ul>
                                        <li><a href="<?= langBaseUrl(); ?>"><?= trans("home"); ?></a></li>
                                        <?php if (!empty($menuLinks)):
                                            foreach ($menuLinks as $menuLink):
                                                if ($menuLink->location == 'quick_links'):
                                                    $itemLink = generateMenuItemUrl($menuLink);
                                                    if (!empty($menuLink->page_default_name)):
                                                        $itemLink = generateUrl($menuLink->page_default_name);
                                                    endif; ?>
                                                    <li><a href="<?= $itemLink; ?>"><?= esc($menuLink->title); ?></a></li>
                                                <?php endif;
                                            endforeach;
                                        endif; ?>
                                        <li><a href="<?= generateUrl('help_center'); ?>"><?= trans("help_center"); ?></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-3 footer-widget">
                            <div class="nav-footer">
                                <div class="row-custom">
                                    <h4 class="footer-title"><?= trans("footer_information"); ?></h4>
                                </div>
                                <div class="row-custom">
                                    <ul>
                                        <?php if (!empty($menuLinks)):
                                            foreach ($menuLinks as $menuLink):
                                                if ($menuLink->location == 'information'):
                                                    $itemLink = generateMenuItemUrl($menuLink);
                                                    if (!empty($menuLink->page_default_name)):
                                                        $itemLink = generateUrl($menuLink->page_default_name);
                                                    endif; ?>
                                                    <li><a href="<?= $itemLink; ?>"><?= esc($menuLink->title); ?></a></li>
                                                <?php endif;
                                            endforeach;
                                        endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-3 footer-widget">
                            <div class="row">
                                <div class="col-12">
                                    <h4 class="footer-title"><?= trans("follow_us"); ?></h4>
                                    <div class="footer-social-links">
                                        <?= view('partials/_social_links', ['showRSS' => true]); ?>
                                    </div>
                                </div>
                            </div>
                            <?php if ($generalSettings->newsletter_status == 1): ?>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="newsletter">
                                            <div class="widget-newsletter">
                                                <h4 class="footer-title"><?= trans("newsletter"); ?></h4>
                                                <form id="form_newsletter_footer" class="form-newsletter">
                                                    <div class="newsletter">
                                                        <input type="email" name="email" class="newsletter-input" maxlength="199" placeholder="<?= trans("enter_email"); ?>" required>
                                                        <button type="submit" name="submit" value="form" class="newsletter-button"><?= trans("subscribe"); ?></button>
                                                    </div>
                                                    <input type="text" name="url">
                                                    <div id="form_newsletter_response"></div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="footer-bottom">
                <div class="container">
                    <div class="copyright">
                        <?= esc($baseSettings->copyright); ?>
                    </div>
                    <?php $envPaymentIcons = getenv('PAYMENT_ICONS');
                    if (!empty($envPaymentIcons)):
                        $paymentIconsArray = explode(',', $envPaymentIcons ?? '');
                        if (!empty($paymentIconsArray) && countItems($paymentIconsArray) > 0):?>
                            <div class="footer-payment-icons">
                                <?php foreach ($paymentIconsArray as $icon):
                                    if (file_exists(FCPATH . 'assets/img/payment/' . $icon . '.svg')):?>
                                        <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="<?= base_url('assets/img/payment/' . $icon . '.svg'); ?>" alt="<?= $icon; ?>" class="lazyload">
                                    <?php endif;
                                endforeach; ?>
                            </div>
                        <?php
                        endif;
                    endif; ?>
                </div>
            </div>
        </div>
    </div>
</footer>
<?php if (empty(helperGetCookie('cks_warning')) && $baseSettings->cookies_warning): ?>
    <div class="cookies-warning">
        <button type="button" aria-label="close" class="close" onclick="hideCookiesWarning();"><i class="icon-close"></i></button>
        <div class="text">
            <?= $baseSettings->cookies_warning_text; ?>
        </div>
        <button type="button" class="btn btn-md btn-block" aria-label="close" onclick="hideCookiesWarning();"><?= trans("accept_cookies"); ?></button>
    </div>
<?php endif; ?>
<a href="javascript:void(0)" class="scrollup"><i class="icon-arrow-up"></i></a>
<script src="<?= base_url('assets/js/jquery-3.5.1.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/plugins-2.3.js'); ?>"></script>
<script src="<?= base_url('assets/js/script-2.3.min.js'); ?>"></script>
<script>$('<input>').attr({type: 'hidden', name: 'sys_lang_id', value: '<?=selectedLangId(); ?>'}).appendTo('form[method="post"]');</script>
<script><?php if (!empty($indexCategories)):foreach ($indexCategories as $category):?>if ($('#category_products_slider_<?= $category->id; ?>').length != 0) {
        $('#category_products_slider_<?= $category->id; ?>').slick({autoplay: false, autoplaySpeed: 4900, infinite: true, speed: 200, swipeToSlide: true, rtl: MdsConfig.rtl, cssEase: 'linear', prevArrow: $('#category-products-slider-nav-<?= $category->id; ?> .prev'), nextArrow: $('#category-products-slider-nav-<?= $category->id; ?> .next'), slidesToShow: 5, slidesToScroll: 1, responsive: [{breakpoint: 992, settings: {slidesToShow: 4, slidesToScroll: 1}}, {breakpoint: 768, settings: {slidesToShow: 3, slidesToScroll: 1}}, {breakpoint: 576, settings: {slidesToShow: 2, slidesToScroll: 1}}]});
    }
    <?php endforeach;
    endif; ?>
    <?php if ($generalSettings->pwa_status == 1): ?>if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('<?= base_url('pwa-sw.js');?>').then(function (registration) {
            }, function (err) {
                console.log('ServiceWorker registration failed: ', err);
            }).catch(function (err) {
                console.log(err);
            });
        });
    } else {
        console.log('service worker is not supported');
    }
    <?php endif; ?>
</script>
<?php if (!empty($video) || !empty($audio)): ?>
    <script src="<?= base_url('assets/vendor/plyr/plyr.min.js'); ?>"></script>
    <script src="<?= base_url('assets/vendor/plyr/plyr.polyfilled.min.js'); ?>"></script>
    <script>const player = new Plyr('#player');
        $(document).ajaxStop(function () {
            const player = new Plyr('#player');
        });
        const audio_player = new Plyr('#audio_player');
        $(document).ajaxStop(function () {
            const player = new Plyr('#audio_player');
        });
        $(document).ready(function () {
            setTimeout(function () {
                $(".product-video-preview").css("opacity", "1");
            }, 300);
            setTimeout(function () {
                $(".product-audio-preview").css("opacity", "1");
            }, 300);
        });</script>
<?php endif;
if (!empty($loadSupportEditor)):
    echo view('support/_editor');
endif; ?>
<?php if (checkNewsletterModal()): ?>
    <script>$(window).on('load', function () {
            $('#modal_newsletter').modal('show');
        });</script>
<?php endif; ?>
<?= $generalSettings->google_analytics; ?>
<?= $generalSettings->custom_footer_codes; ?>
</body>
</html>
<?php if (!empty($isPage404)): exit(); endif; ?>
<div id="wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="nav-breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= langBaseUrl(); ?>"><?= trans("home"); ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= trans("contact"); ?></li>
                    </ol>
                </nav>
                <h1 class="page-title"><?= trans("contact"); ?></h1>
            </div>
            <div class="col-12">
                <div class="page-contact">
                    <div class="row contact-text">
                        <div class="col-12">
                            <?= $baseSettings->contact_text; ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <h2 class="contact-leave-message"><?= trans("leave_message"); ?></h2>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-12 order-1 order-lg-0">
                            <?= view('partials/_messages'); ?>
                            <form action="<?= base_url('contact-post'); ?>" method="post" id="form_validate" class="validate_terms">
                                <?= csrf_field(); ?>
                                <input type="text" name="contact_url" class="ctd">
                                <div class="form-group">
                                    <input type="text" class="form-control form-input" name="name" placeholder="<?= trans("name"); ?>" maxlength="199" minlength="1" pattern=".*\S+.*" value="<?= old('name'); ?>" required>
                                </div>
                                <div class="form-group">
                                    <input type="email" class="form-control form-input" name="email" maxlength="199" placeholder="<?= trans("email_address"); ?>" value="<?= old('email'); ?>" required>
                                </div>
                                <div class="form-group">
                                    <textarea class="form-control form-input form-textarea" name="message" placeholder="<?= trans("message"); ?>" maxlength="4970" minlength="5" required><?= old('message'); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox custom-control-validate-input">
                                        <input type="checkbox" class="custom-control-input" name="terms" id="checkbox_terms" required>
                                        <label for="checkbox_terms" class="custom-control-label"><?= trans("terms_conditions_exp"); ?>&nbsp;
                                            <?php $pageTerms = getPageByDefaultName('terms_conditions', selectedLangId());
                                            if (!empty($pageTerms)): ?>
                                                <a href="<?= generateUrl($pageTerms->page_default_name); ?>" class="link-terms" target="_blank"><strong><?= esc($pageTerms->title); ?></strong></a>
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?php reCaptcha('generate'); ?>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-md btn-custom">
                                        <?= trans("submit"); ?>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="col-md-6 col-12 order-0 order-lg-1 contact-right">
                            <?php if ($baseSettings->contact_phone): ?>
                                <div class="col-12 contact-item">
                                    <i class="icon-phone" aria-hidden="true"></i>
                                    <?= esc($baseSettings->contact_phone); ?>
                                </div>
                            <?php endif;
                            if ($baseSettings->contact_email): ?>
                                <div class="col-12 contact-item">
                                    <i class="icon-envelope" aria-hidden="true"></i>
                                    <?= esc($baseSettings->contact_email); ?>
                                </div>
                            <?php endif;
                            if ($baseSettings->contact_address): ?>
                                <div class="col-12 contact-item">
                                    <i class="icon-map-marker" aria-hidden="true"></i>
                                    <?= esc($baseSettings->contact_address); ?>
                                </div>
                            <?php endif; ?>
                            <div class="col-sm-12 contact-social">
                                <?= view('partials/_social_links', ['showRss' => null]); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if (!empty($baseSettings->contact_address)): ?>
        <div class="container-fluid">
            <div class="row">
                <div class="contact-map-container">
                    <iframe id="contact_iframe" src="https://maps.google.com/maps?width=100%&height=600&hl=en&q=<?= $baseSettings->contact_address; ?>&ie=UTF8&t=&z=8&iwloc=B&output=embed&disableDefaultUI=true" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<script>
    var iframe = document.getElementById("contact_iframe");
    iframe.src = iframe.src;
</script>
<style>
    #footer {
        margin-top: 0;
    }
</style>



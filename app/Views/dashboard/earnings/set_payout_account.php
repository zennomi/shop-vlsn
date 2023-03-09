<div class="row">
    <div class="col-sm-12">
        <?= view('dashboard/includes/_messages'); ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-10">
        <div class="box">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= esc($title); ?></h3>
                </div>
            </div>
            <div class="box-body">
                <?php $activeTab = inputGet('tab');
                if (empty($activeTab) || ($activeTab != 'paypay' && $activeTab != 'bitcoin' && $activeTab != 'iban' && $activeTab != 'swift')) {
                    $activeTab = 'paypal';
                }
                $showAllTabs = false; ?>
                <ul class="nav nav-tabs nav-payout-accounts">
                    <?php if ($paymentSettings->payout_paypal_enabled): $showAllTabs = true; ?>
                        <li class="<?= $activeTab == 'paypal' ? 'active' : ''; ?>">
                            <a data-toggle="pill" href="#tab_paypal"><?= trans("paypal"); ?></a>
                        </li>
                    <?php endif;
                    if ($paymentSettings->payout_bitcoin_enabled): $showAllTabs = true; ?>
                        <li class="<?= $activeTab == 'bitcoin' ? 'active' : ''; ?>">
                            <a data-toggle="pill" href="#tab_bitcoin"><?= trans("bitcoin") ?></a>
                        </li>
                    <?php endif;
                    if ($paymentSettings->payout_iban_enabled): $showAllTabs = true; ?>
                        <li class="<?= $activeTab == 'iban' ? 'active' : ''; ?>">
                            <a data-toggle="pill" href="#tab_iban"><?= trans("iban"); ?></a>
                        </li>
                    <?php endif;
                    if ($paymentSettings->payout_swift_enabled): $showAllTabs = true; ?>
                        <li class="<?= $activeTab == 'swift' ? 'active' : ''; ?>">
                            <a data-toggle="pill" href="#tab_swift"><?= trans("swift"); ?></a>
                        </li>
                    <?php endif; ?>
                </ul>
                <?php $activeTab_content = 'paypal';
                if ($showAllTabs): ?>
                    <div class="tab-content">
                        <div class="tab-pane <?= $activeTab == 'paypal' ? 'active' : 'fade'; ?>" id="tab_paypal">
                            <form action="<?= base_url('set-payout-account-post'); ?>" method="post">
                                <?= csrf_field(); ?>
                                <div class="form-group">
                                    <label><?= trans("paypal_email_address"); ?>*</label>
                                    <input type="email" name="payout_paypal_email" class="form-control form-input" value="<?= esc($userPayout->payout_paypal_email); ?>" required>
                                </div>
                                <div class="form-group">
                                    <button type="submit" name="submit" value="paypal" class="btn btn-md btn-success"><?= trans("save_changes"); ?></button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane <?= $activeTab == 'bitcoin' ? 'active' : 'fade'; ?>" id="tab_bitcoin">
                            <form action="<?= base_url('set-payout-account-post'); ?>" method="post">
                                <?= csrf_field(); ?>
                                <div class="form-group">
                                    <label><?= trans("btc_address"); ?>*</label>
                                    <input type="text" name="payout_bitcoin_address" class="form-control form-input" value="<?= esc($userPayout->payout_bitcoin_address); ?>" required>
                                </div>
                                <div class="form-group">
                                    <button type="submit" name="submit" value="bitcoin" class="btn btn-md btn-success"><?= trans("save_changes"); ?></button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane <?= $activeTab == 'iban' ? 'active' : 'fade'; ?>" id="tab_iban">
                            <form action="<?= base_url('set-payout-account-post'); ?>" method="post">
                                <?= csrf_field(); ?>
                                <div class="form-group">
                                    <label><?= trans("full_name"); ?>*</label>
                                    <input type="text" name="iban_full_name" class="form-control form-input" value="<?= esc($userPayout->iban_full_name); ?>" required>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-6 m-b-sm-15">
                                            <label><?= trans("country"); ?>*</label>
                                            <select name="iban_country_id" class="form-control custom-select" required>
                                                <option value="" selected><?= trans("select_country"); ?></option>
                                                <?php foreach ($activeCountries as $item): ?>
                                                    <option value="<?= $item->id; ?>" <?= $userPayout->iban_country_id == $item->id ? 'selected' : ''; ?>><?= esc($item->name); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            <label><?= trans("bank_name"); ?>*</label>
                                            <input type="text" name="iban_bank_name" class="form-control form-input" value="<?= esc($userPayout->iban_bank_name); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label><?= trans("iban_long"); ?>(<?= trans("iban"); ?>)*</label>
                                    <input type="text" name="iban_number" class="form-control form-input" value="<?= esc($userPayout->iban_number); ?>" required>
                                </div>
                                <div class="form-group">
                                    <button type="submit" name="submit" value="iban" class="btn btn-md btn-success"><?= trans("save_changes"); ?></button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane <?= $activeTab == 'swift' ? 'active' : 'fade'; ?>" id="tab_swift">
                            <form action="<?= base_url('set-payout-account-post'); ?>" method="post">
                                <?= csrf_field(); ?>
                                <div class="form-group">
                                    <label><?= trans("full_name"); ?>*</label>
                                    <input type="text" name="swift_full_name" class="form-control form-input" value="<?= esc($userPayout->swift_full_name); ?>" required>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-6 m-b-sm-15">
                                            <label><?= trans("country"); ?>*</label>
                                            <select name="swift_country_id" class="form-control custom-select" required>
                                                <option value="" selected><?= trans("select_country"); ?></option>
                                                <?php foreach ($activeCountries as $item): ?>
                                                    <option value="<?= $item->id; ?>" <?= $userPayout->swift_country_id == $item->id ? 'selected' : ''; ?>><?= esc($item->name); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            <label><?= trans("state"); ?>*</label>
                                            <input type="text" name="swift_state" class="form-control form-input" value="<?= esc($userPayout->swift_state); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-6 m-b-sm-15">
                                            <label><?= trans("city"); ?>*</label>
                                            <input type="text" name="swift_city" class="form-control form-input" value="<?= esc($userPayout->swift_city); ?>" required>
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            <label><?= trans("postcode"); ?>*</label>
                                            <input type="text" name="swift_postcode" class="form-control form-input" value="<?= esc($userPayout->swift_postcode); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label><?= trans("address"); ?>*</label>
                                    <input type="text" name="swift_address" class="form-control form-input" value="<?= esc($userPayout->swift_address); ?>" required>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-6 m-b-sm-15">
                                            <label><?= trans("bank_account_holder_name"); ?>*</label>
                                            <input type="text" name="swift_bank_account_holder_name" class="form-control form-input" value="<?= esc($userPayout->swift_bank_account_holder_name); ?>" required>
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            <label><?= trans("bank_name"); ?>*</label>
                                            <input type="text" name="swift_bank_name" class="form-control form-input" value="<?= esc($userPayout->swift_bank_name); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-6 m-b-sm-15">
                                            <label><?= trans("bank_branch_country"); ?>*</label>
                                            <select name="swift_bank_branch_country_id" class="form-control custom-select" required>
                                                <option value="" selected><?= trans("select_country"); ?></option>
                                                <?php foreach ($activeCountries as $item): ?>
                                                    <option value="<?= $item->id; ?>" <?= $userPayout->swift_bank_branch_country_id == $item->id ? 'selected' : ''; ?>><?= esc($item->name); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            <label><?= trans("bank_branch_city"); ?>*</label>
                                            <input type="text" name="swift_bank_branch_city" class="form-control form-input" value="<?= esc($userPayout->swift_bank_branch_city); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label><?= trans("swift_iban"); ?>*</label>
                                    <input type="text" name="swift_iban" class="form-control form-input" value="<?= esc($userPayout->swift_iban); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label><?= trans("swift_code"); ?>*</label>
                                    <input type="text" name="swift_code" class="form-control form-input" value="<?= esc($userPayout->swift_code); ?>" required>
                                </div>
                                <div class="form-group">
                                    <button type="submit" name="submit" value="swift" class="btn btn-md btn-success"><?= trans("save_changes"); ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
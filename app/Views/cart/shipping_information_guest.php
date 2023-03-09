<div id="wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="shopping-cart shopping-cart-shipping">
                    <div class="row">
                        <div class="col-sm-12 col-lg-8">
                            <div class="left">
                                <h1 class="cart-section-title"><?= trans("checkout"); ?></h1>
                                <div class="tab-checkout tab-checkout-open m-t-0">
                                    <p class="font-600 text-center m-b-30">
                                        <?= trans("checking_out_as_guest"); ?>.&nbsp;<?= trans("have_account"); ?>&nbsp;
                                        <a href="javascript:void(0)" class="link" data-toggle="modal" data-target="#loginModal">
                                            <strong class="link-underlined"><?= trans("login"); ?></strong>
                                        </a>
                                    </p>
                                    <h2 class="title">1.&nbsp;&nbsp;<?= trans("shipping_information"); ?></h2>
                                    <form action="<?= base_url('shipping-post'); ?>" method="post" id="form-guest-shipping" class="validate-form">
                                        <?= csrf_field(); ?>
                                        <?php $mdsCartShipping = helperGetSession('mds_cart_shipping');
                                        $showBillingForm = 0;
                                        if (empty($mdsCartShipping)) {
                                            $showBillingForm = 0;
                                        } else {
                                            if (empty($mdsCartShipping->use_same_address_for_billing)) {
                                                $showBillingForm = 1;
                                            }
                                        }
                                        $shippingAddress = array();
                                        $billingAddress = array();
                                        if (!empty($mdsCartShipping)):
                                            if (!empty($mdsCartShipping->guest_shipping_address)):
                                                $shippingAddress = $mdsCartShipping->guest_shipping_address;
                                            endif;
                                            if (!empty($mdsCartShipping->guest_billing_address)):
                                                $billingAddress = $mdsCartShipping->guest_billing_address;
                                            endif;
                                        endif; ?>
                                        <div class="row">
                                            <div class="col-12 cart-form-shipping-address">
                                                <p class="text-shipping-address"><?= trans("shipping_address") ?></p>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-12 col-md-6 m-b-sm-15">
                                                            <label><?= trans("first_name"); ?></label>
                                                            <input type="text" name="shipping_first_name" class="form-control form-input" value="<?= !empty($shippingAddress['first_name']) ? esc($shippingAddress['first_name']) : ''; ?>" maxlength="250" required>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <label><?= trans("last_name"); ?></label>
                                                            <input type="text" name="shipping_last_name" class="form-control form-input" value="<?= !empty($shippingAddress['last_name']) ? esc($shippingAddress['last_name']) : ''; ?>" maxlength="250" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-12 col-md-6 m-b-sm-15">
                                                            <label><?= trans("email"); ?></label>
                                                            <input type="email" name="shipping_email" class="form-control form-input" value="<?= !empty($shippingAddress['email']) ? esc($shippingAddress['email']) : ''; ?>" maxlength="250" required>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <label><?= trans("phone_number"); ?></label>
                                                            <input type="text" name="shipping_phone_number" class="form-control form-input" value="<?= !empty($shippingAddress['phone_number']) ? esc($shippingAddress['phone_number']) : ''; ?>" maxlength="100" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label><?= trans("address"); ?></label>
                                                    <input type="text" name="shipping_address" class="form-control form-input" value="<?= !empty($shippingAddress['address']) ? esc($shippingAddress['address']) : ''; ?>" maxlength="250" required>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-12 col-md-6 m-b-sm-15">
                                                            <label class="control-label"><?= trans("country"); ?></label>
                                                            <select id="select_countries_guest_address" name="shipping_country_id" class="select2 select2-req form-control" data-placeholder="<?= trans("country"); ?>" onchange="getStates(this.value,'guest_address'); $('#cart_shipping_methods_container').empty();" required>
                                                                <option></option>
                                                                <?php if (!empty($activeCountries)):
                                                                    foreach ($activeCountries as $item): ?>
                                                                        <option value="<?= $item->id; ?>" <?= !empty($shippingAddress['country_id']) && $shippingAddress['country_id'] == $item->id ? 'selected' : ''; ?>><?= esc($item->name); ?></option>
                                                                    <?php endforeach;
                                                                endif; ?>
                                                            </select>
                                                        </div>
                                                        <div id="get_states_container_guest_address" class="col-12 col-md-6">
                                                            <label class="control-label"><?= trans("state"); ?></label>
                                                            <select id="select_states_guest_address" name="shipping_state_id" class="select2 select2-req form-control" data-placeholder="<?= trans("state"); ?>" onchange="getShippingMethodsByLocation(this.value);" required>
                                                                <?php if (!empty($shippingAddress['country_id'])):
                                                                    $states = getStatesByCountry($shippingAddress['country_id']);
                                                                endif;
                                                                if (!empty($states)):
                                                                    foreach ($states as $item): ?>
                                                                        <option value="<?= $item->id; ?>" <?= !empty($shippingAddress['state_id']) && $shippingAddress['state_id'] == $item->id ? 'selected' : ''; ?>><?= esc($item->name); ?></option>
                                                                    <?php endforeach;
                                                                endif; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-12 col-md-6 m-b-sm-15">
                                                            <label class="control-label"><?= trans("city"); ?></label>
                                                            <input type="text" name="shipping_city" class="form-control form-input" value="<?= !empty($shippingAddress['city']) ? esc($shippingAddress['city']) : ''; ?>" maxlength="250" required>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <label class="control-label"><?= trans("zip_code"); ?></label>
                                                            <input type="text" name="shipping_zip_code" class="form-control form-input" value="<?= !empty($shippingAddress['zip_code']) ? esc($shippingAddress['zip_code']) : ''; ?>" maxlength="90" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 cart-form-billing-address" <?= $showBillingForm == 1 ? 'style="display: block;"' : ''; ?>>
                                                <p class="text-shipping-address"><?= trans("billing_address") ?></p>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-12 col-md-6 m-b-sm-15">
                                                            <label><?= trans("first_name"); ?></label>
                                                            <input type="text" name="billing_first_name" class="form-control form-input" value="<?= !empty($billingAddress['first_name']) ? esc($billingAddress['first_name']) : ''; ?>" maxlength="250" required>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <label><?= trans("last_name"); ?></label>
                                                            <input type="text" name="billing_last_name" class="form-control form-input" value="<?= !empty($billingAddress['last_name']) ? esc($billingAddress['last_name']) : ''; ?>" maxlength="250" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-12 col-md-6 m-b-sm-15">
                                                            <label><?= trans("email"); ?></label>
                                                            <input type="email" name="billing_email" class="form-control form-input" value="<?= !empty($billingAddress['email']) ? esc($billingAddress['email']) : ''; ?>" maxlength="250" required>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <label><?= trans("phone_number"); ?></label>
                                                            <input type="text" name="billing_phone_number" class="form-control form-input" value="<?= !empty($billingAddress['phone_number']) ? esc($billingAddress['phone_number']) : ''; ?>" maxlength="100" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label><?= trans("address"); ?></label>
                                                    <input type="text" name="billing_address" class="form-control form-input" value="<?= !empty($billingAddress['address']) ? esc($billingAddress['address']) : ''; ?>" maxlength="250" required>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-12 col-md-6 m-b-sm-15">
                                                            <label class="control-label"><?= trans("country"); ?></label>
                                                            <select id="select_countries_guest_billing" name="billing_country_id" class="select2 form-control <?= $showBillingForm == 1 ? 'select2-req' : ''; ?>" data-placeholder="<?= trans("country"); ?>" onchange="getStates(this.value,'guest_billing');" required>
                                                                <option></option>
                                                                <?php if (!empty($activeCountries)):
                                                                    foreach ($activeCountries as $item): ?>
                                                                        <option value="<?= $item->id; ?>" <?= !empty($billingAddress['country_id']) && $billingAddress['country_id'] == $item->id ? 'selected' : ''; ?>><?= esc($item->name); ?></option>
                                                                    <?php endforeach;
                                                                endif; ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <label class="control-label"><?= trans("state"); ?></label>
                                                            <div id="get_states_container_guest_billing">
                                                                <select id="select_states_guest_billing" name="billing_state_id" class="select2 form-control <?= $showBillingForm == 1 ? 'select2-req' : ''; ?>" data-placeholder="<?= trans("state"); ?>" required>
                                                                    <?php if (!empty($billingAddress['country_id'])):
                                                                        $states = getStatesByCountry($billingAddress['country_id']);
                                                                    endif;
                                                                    if (!empty($states)):
                                                                        foreach ($states as $item): ?>
                                                                            <option value="<?= $item->id; ?>" <?= !empty($billingAddress['state_id']) && $billingAddress['state_id'] == $item->id ? 'selected' : ''; ?>><?= esc($item->name); ?></option>
                                                                        <?php endforeach;
                                                                    endif; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-12 col-md-6 m-b-sm-15">
                                                            <label class="control-label"><?= trans("city"); ?></label>
                                                            <input type="text" name="billing_city" class="form-control form-input" value="<?= !empty($billingAddress['city']) ? esc($billingAddress['city']) : ''; ?>" maxlength="250" required>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <label class="control-label"><?= trans("zip_code"); ?></label>
                                                            <input type="text" name="billing_zip_code" class="form-control form-input" value="<?= !empty($billingAddress['zip_code']) ? esc($billingAddress['zip_code']) : ''; ?>" maxlength="90" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" name="use_same_address_for_billing" value="1" id="use_same_address_for_billing" <?= $showBillingForm == 0 ? 'checked' : ''; ?>>
                                                        <label for="use_same_address_for_billing" class="custom-control-label"><?= trans("use_same_address_for_billing"); ?></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div id="cart_shipping_methods_container" class="shipping-methods-container">
                                                    <?php if (!empty($shippingAddress) && !empty($shippingAddress['state_id'])):
                                                        echo view("cart/_shipping_methods");
                                                    endif; ?>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="cart-shipping-loader">
                                                            <div class="spinner">
                                                                <div class="bounce1"></div>
                                                                <div class="bounce2"></div>
                                                                <div class="bounce3"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-checkout tab-checkout-closed-bordered">
                                    <h2 class="title">2.&nbsp;&nbsp;<?= trans("payment_method"); ?></h2>
                                </div>
                                <div class="tab-checkout tab-checkout-closed-bordered border-top-0">
                                    <h2 class="title">3.&nbsp;&nbsp;<?= trans("payment"); ?></h2>
                                </div>
                            </div>
                        </div>
                        <?php if ($mdsPaymentType == 'promote'):
                            echo view('cart/_order_summary_promote');
                        else:
                            echo view('cart/_order_summary');
                        endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
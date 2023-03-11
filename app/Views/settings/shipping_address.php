<div id="wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="nav-breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= langBaseUrl(); ?>"><?= trans("home"); ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= $title; ?></li>
                    </ol>
                </nav>
                <h1 class="page-title"><?= trans("settings"); ?></h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-3">
                <div class="row-custom">
                    <?= view("settings/_tabs"); ?>
                </div>
            </div>
            <div class="col-sm-12 col-md-9">
                <div class="row-custom">
                    <div class="profile-tab-content">
                        <div class="row">
                            <div class="col-12">
                                <?= view('partials/_messages'); ?>
                            </div>
                        </div>
                        <div class="row">
                            <?php if (!empty($shippingAddresses)):
                                foreach ($shippingAddresses as $address):
                                    $country = getCountry($address->country_id);
                                    $state = getState($address->state_id); ?>
                                    <div class="col-12 col-md-6 m-b-30">
                                        <div class="shipping-address-box">
                                            <strong class="m-b-10"><?= esc($address->title); ?></strong>
                                            <p><?= esc($address->first_name); ?>&nbsp;<?= esc($address->last_name); ?></p>
                                            <p><?= esc($address->address); ?>&nbsp;<?= esc($address->zip_code); ?>&nbsp;
                                                <?php if (!empty($address->city)):
                                                    echo esc($address->city) . "/";
                                                endif;
                                                if (!empty($state->name)):
                                                    echo esc($state->name) . "/";
                                                endif;
                                                if (!empty($country->name)):
                                                    echo esc($country->name);
                                                endif; ?>
                                            </p>
                                            <p><?= esc($address->email); ?>&nbsp;<?= esc($address->phone_number); ?></p>
                                            <div class="profile-actions-shipping">
                                                <a href="javascript:void(0)" data-toggle="modal" data-target="#modalAddress<?= $address->id; ?>">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="#777777" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                                    </svg>
                                                    &nbsp;<?= trans("edit"); ?>
                                                </a>
                                                &nbsp;&nbsp;&nbsp;
                                                <a href="javascript:void(0)" onclick='deleteShippingAddress("<?= $address->id; ?>","<?= trans("confirm_delete", true); ?>");'>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="#777777" class="bi bi-trash3" viewBox="0 0 16 16">
                                                        <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
                                                    </svg>
                                                    &nbsp;<?= trans("delete"); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach;
                            else: ?>
                                <div class="col-12">
                                    <p class="text-muted"><?= trans("not_added_shipping_address"); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <a href="javascript:void(0)" class="text-info link-add-new-shipping-option font-600" data-toggle="modal" data-target="#modalAddAddress">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                            </svg>
                            <?= trans("add_new_address"); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modalAddAddress" class="modal fade modal-custom" role="dialog">
    <div class="modal-dialog modal-dialog-shipping-address">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                <h4 class="modal-title"><?= trans("add_new_address"); ?></h4>
            </div>
            <form action="<?= base_url('add-shipping-address-post'); ?>" method="post" id="form_add_shipping_address" class="validate-form">
                <?= csrf_field(); ?>
                <input type="hidden" name="back_url" value="<?= getCurrentUrl(); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label"><?= trans("address_title"); ?></label>
                        <input type="text" name="title" class="form-control form-input" placeholder="<?= trans("address_title"); ?>" maxlength="250" required>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-12 col-md-6 m-b-sm-15">
                                <label class="control-label"><?= trans("first_name"); ?></label>
                                <input type="text" name="first_name" class="form-control form-input" placeholder="<?= trans("first_name"); ?>" maxlength="250" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="control-label"><?= trans("last_name"); ?></label>
                                <input type="text" name="last_name" class="form-control form-input" placeholder="<?= trans("last_name"); ?>" maxlength="250" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-12 col-md-6 m-b-sm-15">
                                <label class="control-label"><?= trans("email"); ?></label>
                                <input type="email" name="email" class="form-control form-input" placeholder="<?= trans("email"); ?>" maxlength="250" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="control-label"><?= trans("phone_number"); ?></label>
                                <input type="text" name="phone_number" class="form-control form-input" placeholder="<?= trans("phone_number"); ?>" maxlength="100" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans("address"); ?></label>
                        <input type="text" name="address" class="form-control form-input" placeholder="<?= trans("address"); ?>" maxlength="490" required>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-12 col-md-6 m-b-sm-15">
                                <label class="control-label"><?= trans("country"); ?></label>
                                <select id="select_countries_new_address" name="country_id" class="select2 select2-req form-control" data-placeholder="<?= trans("country"); ?>" onchange="getStates(this.value,'new_address');" required>
                                    <option></option>
                                    <?php if (!empty($activeCountries)):
                                        foreach ($activeCountries as $item): ?>
                                            <option value="<?= $item->id; ?>" class="option"><?= esc($item->name); ?></option>
                                        <?php endforeach;
                                    endif; ?>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="control-label"><?= trans("state"); ?></label>
                                <div id="get_states_container_new_address">
                                    <select id="select_states_new_address" name="state_id" class="select2 select2-req form-control" data-placeholder="<?= trans("state"); ?>" data-id="select_states_new_address" required>
                                        <option></option>
                                        <?php if (!empty($states)):
                                            foreach ($states as $item): ?>
                                                <option value="<?= $item->id; ?>" class="option"><?= esc($item->name); ?></option>
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
                                <input type="text" name="city" class="form-control form-input" placeholder="<?= trans("city"); ?>" maxlength="250" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="control-label"><?= trans("zip_code"); ?></label>
                                <input type="text" name="zip_code" class="form-control form-input" placeholder="<?= trans("zip_code"); ?>" maxlength="90" required>
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

<?php if (!empty($shippingAddresses)):
    foreach ($shippingAddresses as $address):?>
        <div id="modalAddress<?= $address->id; ?>" class="modal fade modal-custom" role="dialog">
            <div class="modal-dialog modal-dialog-shipping-address">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                        <h4 class="modal-title"><?= trans("edit_address"); ?></h4>
                    </div>
                    <form action="<?= base_url('edit-shipping-address-post'); ?>" method="post" id="form_edit_shipping_address_<?= $address->id; ?>" class="validate-form">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="id" value="<?= $address->id; ?>">
                        <input type="hidden" name="back_url" value="<?= getCurrentUrl(); ?>">
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="control-label"><?= trans("address_title"); ?></label>
                                <input type="text" name="title" class="form-control form-input" value="<?= esc($address->title); ?>" placeholder="<?= trans("address_title"); ?>" maxlength="250" required>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-12 col-md-6 m-b-sm-15">
                                        <label class="control-label"><?= trans("first_name"); ?></label>
                                        <input type="text" name="first_name" class="form-control form-input" value="<?= esc($address->first_name); ?>" placeholder="<?= trans("first_name"); ?>" maxlength="250" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="control-label"><?= trans("last_name"); ?></label>
                                        <input type="text" name="last_name" class="form-control form-input" value="<?= esc($address->last_name); ?>" placeholder="<?= trans("last_name"); ?>" maxlength="250" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-12 col-md-6 m-b-sm-15">
                                        <label class="control-label"><?= trans("email"); ?></label>
                                        <input type="email" name="email" class="form-control form-input" value="<?= esc($address->email); ?>" placeholder="<?= trans("email"); ?>" maxlength="250" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="control-label"><?= trans("phone_number"); ?></label>
                                        <input type="text" name="phone_number" class="form-control form-input" value="<?= esc($address->phone_number); ?>" placeholder="<?= trans("phone_number"); ?>" maxlength="100" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?= trans("address"); ?></label>
                                <input type="text" name="address" class="form-control form-input" value="<?= esc($address->address); ?>" placeholder="<?= trans("address"); ?>" maxlength="490" required>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-12 col-md-6 m-b-sm-15">
                                        <label class="control-label"><?= trans("country"); ?></label>
                                        <select id="select_countries_address_<?= $address->id; ?>" name="country_id" class="select2 form-control" onchange="getStates(this.value,'address_<?= $address->id; ?>');" required>
                                            <?php if (!empty($activeCountries)):
                                                foreach ($activeCountries as $item): ?>
                                                    <option value="<?= $item->id; ?>" <?= $item->id == $address->country_id ? 'selected' : ''; ?>><?= esc($item->name); ?></option>
                                                <?php endforeach;
                                            endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="control-label"><?= trans("state"); ?></label>
                                        <div id="get_states_container_address_<?= $address->id; ?>">
                                            <select id="select_states_address_<?= $address->id; ?>" name="state_id" class="select2 form-control" required>
                                                <?php $states = getStatesByCountry($address->country_id);
                                                if (!empty($states)):
                                                    foreach ($states as $item): ?>
                                                        <option value="<?= $item->id; ?>" <?= $item->id == $address->state_id ? 'selected' : ''; ?>><?= esc($item->name); ?></option>
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
                                        <input type="text" name="city" class="form-control form-input" value="<?= esc($address->city); ?>" placeholder="<?= trans("city"); ?>" maxlength="250" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="control-label"><?= trans("zip_code"); ?></label>
                                        <input type="text" name="zip_code" class="form-control form-input" value="<?= esc($address->zip_code); ?>" placeholder="<?= trans("zip_code"); ?>" maxlength="90" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer text-right">
                            <button type="submit" class="btn btn-md btn-custom" onclick="checkStateSelected('select_states_address_<?= $address->id; ?>');"><?= trans("save_changes"); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach;
endif; ?>
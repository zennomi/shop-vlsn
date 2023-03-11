<div class="row">
    <div class="col-sm-8">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans('edit_user'); ?></h3>
                </div>
            </div>
            <form action="<?= base_url('MembershipController/editUserPost'); ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <input type="hidden" name="id" value="<?= esc($user->id); ?>">
                <div class="box-body">
                    <?php $role = getRoleById($user->role_id);
                    if (!empty($role)):
                        $roleName = @parseSerializedNameArray($role->role_name, selectedLangId(), true);
                        if (!empty($roleName)):?>
                            <div class="form-group">
                                <label class="label label-success"><?= esc($roleName); ?></label>
                            </div>
                        <?php endif;
                    endif; ?>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12 col-profile">
                                <img src="<?= getUserAvatar($user); ?>" alt="avatar" class="thumbnail img-responsive img-update" style="max-width: 200px;">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-profile">
                                <p>
                                    <a class="btn btn-success btn-sm btn-file-upload">
                                        <?= trans('select_image'); ?>
                                        <input name="file" size="40" accept=".png, .jpg, .jpeg" onchange="$('#upload-file-info').html($(this).val().replace(/.*[\/\\]/, ''));" type="file">
                                    </a>
                                </p>
                                <p class='label label-info' id="upload-file-info"></p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><?= trans('email'); ?></label>
                        <input type="email" class="form-control form-input" name="email" placeholder="<?= trans('email'); ?>" value="<?= esc($user->email); ?>">
                    </div>
                    <div class="form-group">
                        <label><?= trans('shop_name'); ?>&nbsp;(<?= trans("username"); ?>)</label>
                        <input type="text" class="form-control form-input" name="username" placeholder="<?= trans('shop_name'); ?>" value="<?= esc(getUsername($user)); ?>">
                    </div>
                    <div class="form-group">
                        <label><?= trans('slug'); ?></label>
                        <input type="text" class="form-control form-input" name="slug" placeholder="<?= trans('slug'); ?>" value="<?= esc($user->slug); ?>">
                    </div>
                    <div class="form-group">
                        <label><?= trans('first_name'); ?></label>
                        <input type="text" class="form-control form-input" name="first_name" placeholder="<?= trans('first_name'); ?>" value="<?= esc($user->first_name); ?>">
                    </div>
                    <div class="form-group">
                        <label><?= trans('last_name'); ?></label>
                        <input type="text" class="form-control form-input" name="last_name" placeholder="<?= trans('last_name'); ?>" value="<?= esc($user->last_name); ?>">
                    </div>
                    <div class="form-group">
                        <label><?= trans('phone_number'); ?></label>
                        <input type="text" class="form-control form-input" name="phone_number" placeholder="<?= trans('phone_number'); ?>" value="<?= esc($user->phone_number); ?>">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('shop_description'); ?></label>
                        <textarea class="form-control text-area" name="about_me" placeholder="<?= trans('shop_description'); ?>"><?= esc($user->about_me); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('location'); ?></label>
                        <?= view('partials/_location', ['countries' => $countries, 'countryId' => $user->country_id, 'stateId' => $user->state_id, 'cityId' => $user->city_id, 'map' => false, 'isLocationOptional' => true]); ?>
                        <div class="row">
                            <div class="col-12 col-sm-6 m-b-sm-15">
                                <input type="text" name="address" class="form-control form-input" value="<?= esc($user->address); ?>" placeholder="<?= trans("address") ?>">
                            </div>
                            <div class="col-12 col-sm-3">
                                <input type="text" name="zip_code" class="form-control form-input" value="<?= esc($user->zip_code); ?>" placeholder="<?= trans("zip_code") ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('personal_website_url'); ?></label>
                        <input type="text" class="form-control form-input" name="personal_website_url" placeholder="<?= trans('personal_website_url'); ?>" value="<?= esc($user->personal_website_url); ?>">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('facebook_url'); ?></label>
                        <input type="text" class="form-control form-input" name="facebook_url" placeholder="<?= trans('facebook_url'); ?>" value="<?= esc($user->facebook_url); ?>">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('twitter_url'); ?></label>
                        <input type="text" class="form-control form-input" name="twitter_url" placeholder="<?= trans('twitter_url'); ?>" value="<?= esc($user->twitter_url); ?>">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('instagram_url'); ?></label>
                        <input type="text" class="form-control form-input" name="instagram_url" placeholder="<?= trans('instagram_url'); ?>" value="<?= esc($user->instagram_url); ?>">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('pinterest_url'); ?></label>
                        <input type="text" class="form-control form-input" name="pinterest_url" placeholder="<?= trans('pinterest_url'); ?>" value="<?= esc($user->pinterest_url); ?>">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('linkedin_url'); ?></label>
                        <input type="text" class="form-control form-input" name="linkedin_url" placeholder="<?= trans('linkedin_url'); ?>" value="<?= esc($user->linkedin_url); ?>">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('vk_url'); ?></label>
                        <input type="text" class="form-control form-input" name="vk_url" placeholder="<?= trans('vk_url'); ?>" value="<?= esc($user->vk_url); ?>">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('whatsapp_url'); ?></label>
                        <input type="text" class="form-control form-input" name="whatsapp_url" placeholder="<?= trans('whatsapp_url'); ?>" value="<?= esc($user->whatsapp_url); ?>">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('telegram_url'); ?></label>
                        <input type="text" class="form-control form-input" name="telegram_url" placeholder="<?= trans('telegram_url'); ?>" value="<?= esc($user->telegram_url); ?>">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('youtube_url'); ?></label>
                        <input type="text" class="form-control form-input" name="youtube_url" placeholder="<?= trans('youtube_url'); ?>" value="<?= esc($user->youtube_url); ?>">
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function getStates(val, map) {
        $('#select_states').children('option').remove();
        $('#select_cities').children('option').remove();
        $('#get_states_container').hide();
        $('#get_cities_container').hide();
        var data = {
            'country_id': val
        };
        $.ajax({
            type: 'POST',
            url: MdsConfig.baseURL + '/AjaxController/getStates',
            data: setAjaxData(data),
            success: function (response) {
                var obj = JSON.parse(response);
                if (obj.result == 1) {
                    document.getElementById("select_states").innerHTML = obj.content;
                    $('#get_states_container').show();
                } else {
                    document.getElementById("select_states").innerHTML = '';
                    $('#get_states_container').hide();
                }
            }
        });
    }

    function getCities(val, map) {
        var data = {
            "state_id": val
        };
        $.ajax({
            type: 'POST',
            url: MdsConfig.baseURL + '/AjaxController/getCities',
            data: setAjaxData(data),
            success: function (response) {
                var obj = JSON.parse(response);
                if (obj.result == 1) {
                    document.getElementById("select_cities").innerHTML = obj.content;
                    $('#get_cities_container').show();
                } else {
                    document.getElementById("select_cities").innerHTML = '';
                    $('#get_cities_container').hide();
                }
            }
        });
    }
</script>
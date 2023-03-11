<div class="row">
    <div class="col-lg-6 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans("add_user"); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('users'); ?>" class="btn btn-success btn-add-new">
                        <i class="fa fa-list-ul"></i>&nbsp;&nbsp;<?= trans('users'); ?>
                    </a>
                </div>
            </div>
            <form action="<?= base_url('MembershipController/addUserPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label"><?= trans("first_name"); ?></label>
                        <input type="text" name="first_name" class="form-control auth-form-input" placeholder="<?= trans("first_name"); ?>" value="<?= old("first_name"); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans("last_name"); ?></label>
                        <input type="text" name="last_name" class="form-control auth-form-input" placeholder="<?= trans("last_name"); ?>" value="<?= old("last_name"); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans("email_address"); ?></label>
                        <input type="email" name="email" class="form-control auth-form-input" placeholder="<?= trans("email_address"); ?>" value="<?= old("email"); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans("password"); ?></label>
                        <input type="password" name="password" class="form-control auth-form-input" placeholder="<?= trans("password"); ?>" value="<?= old("password"); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans("role"); ?></label>
                        <select name="role_id" class="form-control" required>
                            <option value=""><?= trans("select"); ?></option>
                            <?php if (!empty($roles)):
                                foreach ($roles as $item):
                                    $roleName = @parseSerializedNameArray($item->role_name, selectedLangId(), true); ?>
                                    <option value="<?= $item->id; ?>"><?= esc($roleName); ?></option>
                                <?php endforeach;
                            endif; ?>
                        </select>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('add_user'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
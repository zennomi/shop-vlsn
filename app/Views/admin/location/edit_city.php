<div class="row">
    <div class="col-lg-6 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans("update_city"); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('cities'); ?>" class="btn btn-success btn-add-new">
                        <i class="fa fa-list-ul"></i>&nbsp;&nbsp;<?= trans('cities'); ?>
                    </a>
                </div>
            </div>
            <form action="<?= base_url('AdminController/editCityPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <input type="hidden" name="id" value="<?= $city->id; ?>">
                <div class="box-body">
                    <div class="form-group">
                        <label><?= trans('country'); ?></label>
                        <select name="country_id" class="form-control" onchange="getStatesByCountry($(this).val());" required>
                            <option value=""><?= trans("select"); ?></option>
                            <?php if (!empty($countries)):
                                foreach ($countries as $item): ?>
                                    <option value="<?= $item->id; ?>" <?= $city->country_id == $item->id ? 'selected' : ''; ?>><?= esc($item->name); ?></option>
                                <?php endforeach;
                            endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><?= trans('state'); ?></label>
                        <select name="state_id" id="select_states" class="form-control" required>
                            <option value=""><?= trans("select"); ?></option>
                            <?php if (!empty($states)):
                                foreach ($states as $item): ?>
                                    <option value="<?= $item->id; ?>" <?= $city->state_id == $item->id ? 'selected' : ''; ?>><?= esc($item->name); ?></option>
                                <?php endforeach;
                            endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><?= trans("name"); ?></label>
                        <input type="text" class="form-control" name="name" value="<?= $city->name; ?>" placeholder="<?= trans("name"); ?>" maxlength="200" required>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
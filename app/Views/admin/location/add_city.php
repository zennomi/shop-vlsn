<div class="row">
    <div class="col-lg-8 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans("add_city"); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('cities'); ?>" class="btn btn-success btn-add-new">
                        <i class="fa fa-list-ul"></i>&nbsp;&nbsp;<?= trans('cities'); ?>
                    </a>
                </div>
            </div>
            <form action="<?= base_url('AdminController/addCityPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label><?= trans('country'); ?></label>
                        <select name="country_id" class="form-control" onchange="getStatesByCountry($(this).val());" required>
                            <option value=""><?= trans("select"); ?></option>
                            <?php if (!empty($countries)):
                                foreach ($countries as $item): ?>
                                    <option value="<?= $item->id; ?>"><?= esc($item->name); ?></option>
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
                                    <option value="<?= $item->id; ?>"><?= esc($item->name); ?></option>
                                <?php endforeach;
                            endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><?= trans("name"); ?></label>
                        <input type="text" class="form-control" name="name" placeholder="<?= trans("name"); ?>" maxlength="200" required>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('add_city'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
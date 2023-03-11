<div class="row">
    <div class="col-lg-8 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans("add_country"); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('countries'); ?>" class="btn btn-success btn-add-new">
                        <i class="fa fa-list-ul"></i>&nbsp;&nbsp;<?= trans('countries'); ?>
                    </a>
                </div>
            </div>
            <form action="<?= base_url('AdminController/addCountryPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label><?= trans("name"); ?></label>
                        <input type="text" class="form-control" name="name" placeholder="<?= trans("name"); ?>" maxlength="200" required>
                    </div>
                    <div class="form-group">
                        <label><?= trans("continent"); ?></label>
                        <select name="continent_code" class="form-control">
                            <?php $continents = getContinents();
                            if (!empty($continents)):
                                foreach ($continents as $key => $value):?>
                                    <option value="<?= $key; ?>"><?= $value; ?></option>
                                <?php endforeach;
                            endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4 col-xs-12">
                                <label><?= trans('status'); ?></label>
                            </div>
                            <div class="col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="status" value="1" id="status_1" class="square-purple" checked>
                                <label for="status_1" class="option-label"><?= trans('active'); ?></label>
                            </div>
                            <div class="col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="status" value="0" id="status_2" class="square-purple">
                                <label for="status_2" class="option-label"><?= trans('inactive'); ?></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('add_country'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
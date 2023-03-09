<div class="row">
    <div class="col-lg-8 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans("update_state"); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('states'); ?>" class="btn btn-success btn-add-new">
                        <i class="fa fa-list-ul"></i>&nbsp;&nbsp;<?= trans('states'); ?>
                    </a>
                </div>
            </div>
            <form action="<?= base_url('AdminController/editStatePost'); ?>" method="post">
                <?= csrf_field(); ?>
                <input type="hidden" name="id" value="<?= $state->id; ?>">
                <div class="box-body">
                    <div class="form-group">
                        <label><?= trans('country'); ?></label>
                        <select name="country_id" class="form-control" required>
                            <option value=""><?= trans("select"); ?></option>
                            <?php if (!empty($countries)):
                                foreach ($countries as $item): ?>
                                    <option value="<?= $item->id; ?>" <?= $state->country_id == $item->id ? 'selected' : ''; ?>>
                                        <?= esc($item->name); ?>
                                    </option>
                                <?php endforeach;
                            endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><?= trans("name"); ?></label>
                        <input type="text" class="form-control" name="name" value="<?= esc($state->name); ?>" placeholder="<?= trans("name"); ?>" maxlength="200" required>
                    </div>

                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('update_state'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
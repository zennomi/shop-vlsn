<div class="row">
    <div class="col-lg-6 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('storage'); ?></h3>
            </div>
            <form action="<?= base_url('AdminController/storagePost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4 col-xs-12">
                                <label><?= trans('storage'); ?></label>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="storage" value="local" id="storage_1" class="square-purple" <?= $storageSettings->storage == 'local' ? 'checked' : ''; ?>>
                                <label for="storage_1" class="option-label"><?= trans('local_storage'); ?></label>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="storage" value="aws_s3" id="storage_2" class="square-purple" <?= $storageSettings->storage == 'aws_s3' ? 'checked' : ''; ?>>
                                <label for="storage_2" class="option-label"><?= trans('aws_storage'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer" style="padding-left: 0; padding-right: 0;">
                        <button type="submit" name="action" value="save" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-6 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('aws_storage'); ?></h3>
            </div>
            <form action="<?= base_url('AdminController/awsS3Post'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label"><?= trans('aws_key'); ?></label>
                        <input type="text" class="form-control" name="aws_key" placeholder="<?= trans('aws_key'); ?>" value="<?= esc($storageSettings->aws_key); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('aws_secret'); ?></label>
                        <input type="text" class="form-control" name="aws_secret" placeholder="<?= trans('aws_secret'); ?>" value="<?= esc($storageSettings->aws_secret); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('bucket_name'); ?></label>
                        <input type="text" class="form-control" name="aws_bucket" placeholder="<?= trans('bucket_name'); ?>" value="<?= esc($storageSettings->aws_bucket); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('region'); ?></label>
                        <input type="text" class="form-control" name="aws_region" placeholder="E.g: us-east-1" value="<?= esc($storageSettings->aws_region); ?>" required>
                    </div>
                    <div class="box-footer" style="padding-left: 0; padding-right: 0;">
                        <button type="submit" name="action" value="save" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
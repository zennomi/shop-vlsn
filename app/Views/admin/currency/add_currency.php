<div class="row">
    <div class="col-lg-8 col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans("add_currency"); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('currency-settings'); ?>" class="btn btn-success btn-add-new">
                        <i class="fa fa-list-ul"></i>&nbsp;&nbsp;<?= trans("currencies"); ?>
                    </a>
                </div>
            </div>
            <form action="<?= base_url('AdminController/addCurrencyPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label><?= trans("currency_name"); ?></label>
                        <input type="text" class="form-control" name="name" placeholder="Ex: US Dollar" maxlength="200" required>
                    </div>
                    <div class="form-group">
                        <label><?= trans("currency_code"); ?></label>
                        <input type="text" class="form-control" name="code" placeholder="Ex: USD" maxlength="99" required>
                    </div>
                    <div class="form-group">
                        <label><?= trans("currency_symbol"); ?></label>
                        <input type="text" class="form-control" name="symbol" placeholder="Ex: $" maxlength="99" required>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12 m-b-5">
                                <label><?= trans('currency_format'); ?> (Thousands Seperator)</label>
                            </div>
                            <div class="col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="currency_format" value="us" id="currency_format_1" class="square-purple" checked>
                                <label for="currency_format_1" class="option-label">1<strong>,</strong>234<strong>,</strong>567<strong>.</strong>89</label>
                            </div>
                            <div class="col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="currency_format" value="european" id="currency_format_2" class="square-purple">
                                <label for="currency_format_2" class="option-label">1<strong>.</strong>234<strong>.</strong>567<strong>,</strong>89</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12 m-b-5">
                                <label><?= trans('currency_symbol_format'); ?></label>
                            </div>
                            <div class="col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="symbol_direction" value="left" id="symbol_direction_1" class="square-purple" checked>
                                <label for="symbol_direction_1" class="option-label">$100 (<?= trans("left"); ?>)</label>
                            </div>
                            <div class="col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="symbol_direction" value="right" id="symbol_direction_2" class="square-purple">
                                <label for="symbol_direction_2" class="option-label">100$ (<?= trans("right"); ?>)</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12 m-b-5">
                                <label><?= trans('add_space_between_money_currency'); ?></label>
                            </div>
                            <div class="col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="space_money_symbol" value="1" id="space_money_symbol_1" class="square-purple">
                                <label for="space_money_symbol_1" class="option-label"><?= trans("yes"); ?></label>
                            </div>
                            <div class="col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="space_money_symbol" value="0" id="space_money_symbol_2" class="square-purple" checked>
                                <label for="space_money_symbol_2" class="option-label"><?= trans("no"); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12 m-b-5">
                                <label><?= trans('status'); ?></label>
                            </div>
                            <div class="col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="status" value="1" id="status_1" class="square-purple">
                                <label for="status_1" class="option-label"><?= trans("active"); ?></label>
                            </div>
                            <div class="col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="status" value="0" id="status_2" class="square-purple" checked>
                                <label for="status_2" class="option-label"><?= trans("inactive"); ?></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('add_currency'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
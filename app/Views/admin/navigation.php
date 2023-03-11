<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('navigation'); ?></h3>
            </div>
            <form action="<?= base_url('AdminController/navigationPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label><?= trans('menu_limit'); ?>&nbsp;(<?= trans("number_of_links_in_menu"); ?>)</label>
                        <input type="number" class="form-control" name="menu_limit" placeholder="<?= trans('menu_limit'); ?>" value="<?= $generalSettings->menu_limit; ?>" min="1" max="100" style="max-width: 400px;" required>
                    </div>
                    <div class="form-group">
                        <label><?= trans('navigation_template'); ?></label>
                    </div>
                    <div class="row nav-template-items">
                        <div class="col-md-6">
                            <div class="nav-template-item <?= ($generalSettings->selected_navigation == 1) ? 'active' : ''; ?>" data-nav-id="1">
                                <img src="<?= base_url('assets/admin/img/nav_1.jpg'); ?>" alt="" class="img-responsive">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="nav-template-item <?= ($generalSettings->selected_navigation == 2) ? 'active' : ''; ?>" data-nav-id="2">
                                <img src="<?= base_url('assets/admin/img/nav_2.jpg'); ?>" alt="" class="img-responsive">
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="input_navigation" name="navigation" value="<?= $generalSettings->selected_navigation; ?>">
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(".nav-template-item").click(function () {
        $(".nav-template-item").removeClass("active");
        $(this).addClass("active");
        var id = $(this).attr("data-nav-id");
        $("#input_navigation").val(id);
    });
</script>

</section>
</div>
<footer class="main-footer">
    <div class="pull-right hidden-xs">
        <b style="font-weight: 600;">Version</b> 2.3.1
    </div>
    <strong style="font-weight: 600;"><?= esc($baseSettings->copyright); ?></strong>
</footer>
</div>
<style>.item-table-filter {
        min-width: 110px;
        max-width: 160px;
    }</style>
<script src="<?= base_url('assets/admin/js/jquery-ui.min.js'); ?>"></script>
<script>
    $.widget.bridge('uibutton', $.ui.button);
    var thousands_separator = '<?= getThousandsSeparator();?>';
</script>

<script src="<?= base_url('assets/admin/vendor/bootstrap/js/bootstrap.min.js'); ?>"></script>
<script src="<?= base_url('assets/admin/vendor/datatables/jquery.dataTables.min.js'); ?>"></script>
<script src="<?= base_url('assets/admin/vendor/datatables/dataTables.bootstrap.min.js'); ?>"></script>
<script src="<?= base_url('assets/admin/js/adminlte.min.js'); ?>"></script>
<script src="<?= base_url('assets/admin/vendor/icheck/icheck.min.js'); ?>"></script>
<script src="<?= base_url('assets/admin/vendor/pace/pace.min.js'); ?>"></script>
<script src="<?= base_url('assets/admin/vendor/tagsinput/jquery.tagsinput.min.js'); ?>"></script>
<script src="<?= base_url('assets/admin/js/plugins-2.3.js'); ?>"></script>
<script src="<?= base_url('assets/admin/vendor/magnific-popup/jquery.magnific-popup.min.js'); ?>"></script>
<script src="<?= base_url('assets/admin/js/main-2.3.js'); ?>"></script>
<script src="<?= base_url('assets/vendor/tinymce/tinymce.min.js'); ?>"></script>
<script>$('<input>').attr({type: 'hidden', name: 'back_url', value: '<?= getCurrentUrl(); ?>'}).appendTo('form[method="post"]');</script>
<script>
    function initTinyMCE(selector, minHeight, toolbar) {
        var menuBar = 'file edit view insert format tools table help';
        if (selector == '.tinyMCEsmall' || selector == '.tinyMCEticket') {
            menuBar = false;
        }
        if (toolbar==null){
            toolbar = 'fullscreen code preview | undo redo | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | numlist bullist | forecolor backcolor removeformat | image media link';
        }
        tinymce.init({
            selector: selector,
            height: minHeight,
            min_height: minHeight,
            valid_elements: '*[*]',
            relative_urls: false,
            remove_script_host: false,
            directionality: MdsConfig.directionality,
            language: '<?= $activeLang->text_editor_lang; ?>',
            menubar: menuBar,
            plugins: 'advlist autolink lists link image charmap preview searchreplace visualblocks code codesample fullscreen insertdatetime media table',
            toolbar: toolbar,
            content_css: ['<?= base_url('assets/vendor/tinymce/editor_content.css'); ?>'],
        });
    }

    if ($('.tinyMCE').length > 0) {
        initTinyMCE('.tinyMCE', 400, null);
    }
    if ($('.tinyMCEsmall').length > 0) {
        initTinyMCE('.tinyMCEsmall', 300, null);
    }
    if ($('.tinyMCEticket').length > 0) {
        var toolbar = 'fullscreen code preview | bold italic | alignleft aligncenter alignright alignjustify | numlist bullist | forecolor backcolor removeformat | table image media link | outdent indent';
        initTinyMCE('.tinyMCEticket', 400, toolbar);
    }
    $(document).ready(function () {
        $('.data_table').DataTable({
            "order": [[0, "desc"]],
            "aLengthMenu": [[15, 30, 60, 100], [15, 30, 60, 100, "All"]],
            "language": {
                "lengthMenu": "<?= trans('show'); ?> _MENU_",
                "search": "<?= trans('search'); ?>:",
                "zeroRecords": "<?= trans('no_records_found'); ?>"
            },
            "infoCallback": function (settings, start, end, max, total, pre) {
                return total > 0 ? "<?= trans('number_of_entries'); ?>: " + total : '';
            }
        });
    });
    $(document).ready(function () {
        $('#cs_datatable_currency').DataTable({
            "ordering": false,
            "aLengthMenu": [[15, 30, 60, 100], [15, 30, 60, 100, "All"]],
            "language": {
                "lengthMenu": "<?= trans('show'); ?> _MENU_",
                "search": "<?= trans('search'); ?>:",
                "zeroRecords": "<?= trans('no_records_found'); ?>"
            },
            "infoCallback": function (settings, start, end, max, total, pre) {
                return total > 0 ? "<?= trans('number_of_entries'); ?>: " + total : '';
            }
        });
    });
</script>
<?php if (isset($langSearchColumn)): ?>
    <script>
        //datatable
        var table = $('.cs_datatable_lang').DataTable({
            dom: 'l<"#table_dropdown">frtip',
            "order": [[0, "desc"]],
            "aLengthMenu": [[15, 30, 60, 100], [15, 30, 60, 100, "All"]],
            "language": {
                "lengthMenu": "<?= trans('show'); ?> _MENU_",
                "search": "<?= trans('search'); ?>:",
                "zeroRecords": "<?= trans('no_records_found'); ?>",
                "info": "<?= trans('number_of_entries'); ?>: _TOTAL_"
            },
            "infoCallback": function (settings, start, end, max, total, pre) {
                return total > 0 ? "<?= trans('number_of_entries'); ?>: " + total : '';
            }
        });
        $('<label class="table-label"><label/>').text("<?= trans('language'); ?>").appendTo('#table_dropdown');
        //insert the select and some options
        $select = $('<select class="form-control input-sm"><select/>').appendTo('#table_dropdown');
        $('<option/>').val('').text('<?= trans("all"); ?>').appendTo($select);
        <?php foreach ($activeLanguages as $lang): ?>
        $('<option/>').val('<?= $lang->name; ?>').text('<?= $lang->name; ?>').appendTo($select);
        <?php endforeach; ?>
        table.column(<?= $langSearchColumn; ?>).search('').draw();
        $("#table_dropdown select").change(function () {
            table.column(<?= $langSearchColumn; ?>).search($(this).val()).draw();
        });
    </script>
<?php endif; ?>
<script>
    $('#location_1').on('ifChecked', function () {
        $("#location_countries").hide();
    });
    $('#location_2').on('ifChecked', function () {
        $("#location_countries").show();
    });
</script>
</body>
</html>

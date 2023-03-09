</section>
</div>
</div>
<script src="<?= base_url('assets/admin/js/jquery-ui.min.js'); ?>"></script>
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
<script src="<?= base_url('assets/admin/js/dashboard-2.3.js'); ?>"></script>
<script src="<?= base_url('assets/vendor/tinymce/tinymce.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendor/file-manager/file-manager.js'); ?>"></script>
<script>$('<input>').attr({type: 'hidden', name: 'back_url', value: '<?= getCurrentUrl(); ?>'}).appendTo('form[method="post"]');</script>
<script>
    $(document).ready(function () {
        $('.dataTable').DataTable({
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
        $('.dataTableNoSort').DataTable({
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

    function initTinyMCE(selector, minHeight) {
        var menuBar = 'file insert format table help';
        if (selector == '.tinyMCEsmall') {
            menuBar = false;
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
            toolbar: 'fullscreen code preview | undo redo | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | numlist bullist | forecolor backcolor removeformat | image media link',
            content_css: ['<?= base_url('assets/vendor/tinymce/editor_content.css'); ?>'],
        });
    }

    if ($('.tinyMCE').length > 0) {
        initTinyMCE('.tinyMCE', 500);
    }
    if ($('.tinyMCEsmall').length > 0) {
        initTinyMCE('.tinyMCEsmall', 300);
    }
</script>
</body>
</html>
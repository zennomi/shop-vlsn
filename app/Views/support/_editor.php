<script src="<?= base_url('assets/vendor/file-uploader/js/jquery.dm-uploader.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendor/file-uploader/js/ui.js'); ?>"></script>
<script src="<?= base_url('assets/vendor/tinymce/tinymce.min.js'); ?>"></script>
<link rel="stylesheet" href="<?= base_url('assets/vendor/file-uploader/css/jquery.dm-uploader.min.css'); ?>">
<link rel="stylesheet" href="<?= base_url('assets/vendor/file-uploader/css/styles.css'); ?>">
<script>
    tinymce.init({
        selector: '.tinyMCEticket',
        height: 400,
        min_height: 400,
        valid_elements: '*[*]',
        relative_urls: false,
        remove_script_host: false,
        directionality: MdsConfig.rtl,
        entity_encoding: "raw",
        language: '<?= $activeLang->text_editor_lang; ?>',
        menubar: false,
        plugins: [],
        toolbar: 'fullscreen code preview | undo redo | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | numlist bullist | forecolor backcolor removeformat | image media link',
        content_css: ['<?= base_url('assets/vendor/tinymce/editor_content.css'); ?>'],
    });
    $(function () {
        $('#drag-and-drop-zone').dmUploader({
            url: '<?= base_url('SupportController/uploadSupportAttachment'); ?>',
            queue: false,
            extraData: function (id) {
                return {
                    'file_id': id,
                    'ticket_type': 'client',
                    '<?= csrf_token() ?>': '<?= csrf_hash(); ?>'
                };
            },
            onDragEnter: function () {
                this.addClass('active');
            },
            onDragLeave: function () {
                this.removeClass('active');
            },
            onNewFile: function (id, file) {
                ui_multi_add_file(id, file, "file");
            },
            onBeforeUpload: function (id) {
                $('#uploaderFile' + id + ' .dm-progress-waiting').hide();
                ui_multi_update_file_progress(id, 0, '', true);
                ui_multi_update_file_status(id, 'uploading', 'Uploading...');
            },
            onUploadProgress: function (id, percent) {
                ui_multi_update_file_progress(id, percent);
            },
            onUploadSuccess: function (id, data) {
                var obj = JSON.parse(data);
                if (obj.result == 1) {
                    document.getElementById("response_uploaded_files").innerHTML = obj.response;
                }
                document.getElementById("uploaderFile" + id).remove();
                ui_multi_update_file_status(id, 'success', 'Upload Complete');
                ui_multi_update_file_progress(id, 100, 'success', false);
            },
            onFileSizeError: function (file) {
                alert("<?= trans("file_too_large", true) ?>");
            }
        });
    });
</script>
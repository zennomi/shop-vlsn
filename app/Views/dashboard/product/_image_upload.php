<div class="dm-uploader-container">
    <div id="drag-and-drop-zone" class="dm-uploader text-center" style="padding: 20p">
        <p class="dm-upload-icon">
            <i class="icon-upload"></i>
        </p>
        <p class="dm-upload-text"><?= trans("drag_drop_images_here"); ?>&nbsp;<span style="text-decoration: underline"><?= trans('browse_files'); ?></span></p>
        <a class='btn btn-md dm-btn-select-files'>
            <input type="file" name="file" size="40" multiple="multiple">
        </a>
        <ul class="dm-uploaded-files" id="files-image">
            <?php $uploadedImageCount = 0;
            if (!empty($images)):
                foreach ($images as $image):
                    $uploadedImageCount += 1; ?>
                    <li class="media" id="uploaderFile<?= $image->file_id; ?>">
                        <img src="<?= base_url('uploads/temp/' . $image->img_small); ?>" alt="">
                        <a href="javascript:void(0)" class="btn-img-delete btn-delete-product-img-session" data-file-id="<?= $image->file_id; ?>"><i class="icon-close"></i></a>
                        <?php if ($image->is_main == 1): ?>
                            <a href="javascript:void(0)" class="btn btn-xs btn-success btn-is-image-main btn-set-image-main-session"><?= trans("main"); ?></a>
                        <?php else: ?>
                            <a href="javascript:void(0)" class="btn btn-xs btn-secondary btn-is-image-main btn-set-image-main-session" data-file-id="<?= $image->file_id; ?>"><?= trans("main"); ?></a>
                        <?php endif; ?>
                    </li>
                <?php endforeach;
            endif; ?>
        </ul>
    </div>
</div>
<div class="row-custom">
    <p class="images-exp"><i class="icon-exclamation-circle"></i><?= trans("product_image_exp"); ?></p>
</div>
<script type="text/html" id="files-template-image">
    <li class="media">
        <img class="preview-img" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="bg">
        <div class="media-body">
            <div class="progress">
                <div class="dm-progress-waiting"><?= trans("waiting"); ?></div>
                <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
    </li>
</script>

<script>
    var imageUploadCount = <?= $uploadedImageCount; ?>;
    $(function () {
        $('#drag-and-drop-zone').dmUploader({
            url: '<?= base_url('upload-image-session-post'); ?>',
            maxFileSize: <?= $productSettings->max_file_size_image; ?>,
            queue: true,
            allowedTypes: 'image/*',
            extFilter: ["jpg", "jpeg", "png", "gif"],
            extraData: function (id) {
                return {
                    'file_id': id,
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
                if (imageUploadCount >= MdsConfig.imageUploadLimit) {
                    swal({
                        text: "<?= trans("error_image_limit", true);?>",
                        icon: "warning",
                        button: sweetalert_ok
                    });
                    return false;
                }
                ui_multi_add_file(id, file, "image");
                if (typeof FileReader !== "undefined") {
                    var reader = new FileReader();
                    var img = $('#uploaderFile' + id).find('img');

                    reader.onload = function (e) {
                        img.attr('src', e.target.result);
                    }
                    reader.readAsDataURL(file);
                }
                imageUploadCount++;
            },
            onBeforeUpload: function (id, file) {
                $('#uploaderFile' + id + ' .dm-progress-waiting').hide();
                ui_multi_update_file_progress(id, 0, '', true);
                ui_multi_update_file_status(id, 'uploading', 'Uploading...');
            },
            onUploadProgress: function (id, percent) {
                ui_multi_update_file_progress(id, percent);
            },
            onUploadSuccess: function (id, data) {
                var data = {
                    'file_id': id
                };
                $.ajax({
                    type: 'POST',
                    url: MdsConfig.baseURL + '/get-sess-uploaded-image-post',
                    data: setAjaxData(data),
                    success: function (response) {
                        var obj = JSON.parse(response);
                        if (obj.result == 1) {
                            document.getElementById("uploaderFile" + id).innerHTML = obj.imageHtml;
                        }
                    }
                });
                ui_multi_update_file_status(id, 'success', 'Upload Complete');
                ui_multi_update_file_progress(id, 100, 'success', false);
            },
            onFileSizeError: function (file) {
                swal({
                    text: "<?= trans('file_too_large', true) . ' ' . formatSizeUnits($productSettings->max_file_size_image); ?>",
                    icon: "warning",
                    button: MdsConfig.textOk
                });
            },
            onFileTypeError: function (file) {
                swal({
                    text: "<?= trans("invalid_file_type", true);?>",
                    icon: "warning",
                    button: MdsConfig.textOk
                });
            },
            onFileExtError: function (file) {
                swal({
                    text: "<?= trans('invalid_file_type', true); ?>",
                    icon: "warning",
                    button: MdsConfig.textOk
                });
            }
        });
    });
</script>


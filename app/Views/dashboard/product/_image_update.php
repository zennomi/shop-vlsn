<div class="dm-uploader-container">
    <div id="drag-and-drop-zone" class="dm-uploader text-center">
        <p class="dm-upload-icon"><i class="icon-upload"></i></p>
        <p class="dm-upload-text"><?= trans("drag_drop_images_here"); ?>&nbsp;<span style="text-decoration: underline"><?= trans('browse_files'); ?></span></p>
        <a class='btn btn-md dm-btn-select-files'>
            <input type="file" name="file" size="40" multiple="multiple">
        </a>
        <ul class="dm-uploaded-files" id="files-image">
            <?php $uploadedImageCount = 0;
            if (!empty($productImages)):
                foreach ($productImages as $productImage):
                    $uploadedImageCount += 1; ?>
                    <li class="media">
                        <img src="<?= getProductImageURL($productImage, 'image_small'); ?>" alt="">
                        <a href="javascript:void(0)" class="btn-img-delete btn-delete-product-img" data-file-id="<?= $productImage->id; ?>">
                            <i class="icon-close"></i>
                        </a>
                        <?php if ($productImage->is_main == 1): ?>
                            <a href="javascript:void(0)" class="btn btn-xs btn-success btn-is-image-main btn-set-image-main"><?= trans("main"); ?></a>
                        <?php else: ?>
                            <a href="javascript:void(0)" class="btn btn-xs btn-secondary btn-is-image-main btn-set-image-main" data-image-id="<?= $productImage->id; ?>" data-product-id="<?= $productImage->product_id; ?>"><?= trans("main"); ?></a>
                        <?php endif; ?>
                    </li>
                <?php endforeach;
            endif; ?>
        </ul>
    </div>
</div>
<p class="images-exp"><i class="icon-exclamation-circle"></i><?= trans("product_image_exp"); ?></p>
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
    var imageUploadedCount = <?= $uploadedImageCount; ?>;
    $('#drag-and-drop-zone').dmUploader({
        url: '<?= base_url('upload-image-post'); ?>',
        maxFileSize: <?= $productSettings->max_file_size_image; ?>,
        queue: true,
        allowedTypes: 'image/*',
        extFilter: ["jpg", "jpeg", "png", "gif"],
        extraData: function (id) {
            return {
                'product_id': <?= $product->id; ?>,
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
            if (imageUploadedCount >= MdsConfig.imageUploadLimit) {
                swal({
                    text: "<?= trans("error_image_limit", true);?>",
                    icon: 'warning',
                    button: MdsConfig.textOk
                });
                return false;
            }
            ui_multi_add_file(id, file, 'image');
            if (typeof FileReader !== 'undefined') {
                var reader = new FileReader();
                var img = $('#uploaderFile' + id).find('img');
                reader.onload = function (e) {
                    img.attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
            imageUploadedCount++;
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
            var data = {
                'image_id': obj.image_id
            };
            $.ajax({
                type: 'POST',
                url: MdsConfig.baseURL + '/get-uploaded-image-post',
                data: setAjaxData(data),
                success: function (response) {
                    document.getElementById('uploaderFile' + id).innerHTML = response;
                }
            });
        },
        onUploadError: function (id, xhr, status, message) {
            if (message == 'Not Acceptable') {
                $("#uploaderFile" + id).remove();
                $(".error-message-img-upload").show();
                setTimeout(function () {
                    $(".error-message-img-upload").fadeOut("slow");
                }, 4000)
            }
        },
        onFileSizeError: function (file) {
            swal({
                text: "<?= trans('file_too_large', true) . ' ' . formatSizeUnits($productSettings->max_file_size_image); ?>",
                icon: 'warning',
                button: MdsConfig.textOk
            });
        },
        onFileTypeError: function (file) {
            swal({
                text: "<?= trans("invalid_file_type", true);?>",
                icon: 'warning',
                button: MdsConfig.textOk
            });
        },
        onFileExtError: function (file) {
            swal({
                text: "<?= trans('invalid_file_type', true); ?>",
                icon: 'warning',
                button: MdsConfig.textOk
            });
        }
    });
</script>


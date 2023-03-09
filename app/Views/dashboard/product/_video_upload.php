<div class="col-sm-12">
    <div id="video_upload_result">
        <?= view('dashboard/product/_video_upload_response'); ?>
    </div>
</div>

<script type="text/html" id="files-template-video">
    <li class="media">
        <div class="media-body">
            <div class="progress">
                <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
    </li>
</script>

<script>
    $('#drag-and-drop-zone-video').dmUploader({
        url: '<?= base_url('upload-video-post'); ?>',
        maxFileSize: <?= $productSettings->max_file_size_video; ?>,
        queue: true,
        extFilter: ["mp4", "webm"],
        multiple: false,
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
            ui_multi_add_file(id, file, "video");
        },
        onBeforeUpload: function (id) {
            ui_multi_update_file_progress(id, 0, '', true);
            ui_multi_update_file_status(id, 'uploading', 'Uploading...');
        },
        onUploadProgress: function (id, percent) {
            ui_multi_update_file_progress(id, percent);
        },
        onUploadSuccess: function (id, productId) {
            loadVideoPreview(productId);
        },
        onFileSizeError: function (file) {
            $("#drag-and-drop-zone-video .error-message-file-upload").show();
            $("#drag-and-drop-zone-video .error-message-file-upload p").html("<?= trans('file_too_large', true) . ' ' . formatSizeUnits($productSettings->max_file_size_video); ?>");
            setTimeout(function () {
                $("#drag-and-drop-zone-video .error-message-file-upload").fadeOut("slow");
            }, 4000)
        }
    });
    $(document).ajaxStop(function () {
        $('#drag-and-drop-zone-video').dmUploader({
            url: '<?= base_url('upload-video-post'); ?>',
            maxFileSize: <?= $productSettings->max_file_size_video; ?>,
            queue: true,
            extFilter: ["mp4", "webm"],
            multiple: false,
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
                ui_multi_add_file(id, file, "video");
            },
            onBeforeUpload: function (id) {
                ui_multi_update_file_progress(id, 0, '', true);
                ui_multi_update_file_status(id, 'uploading', 'Uploading...');
            },
            onUploadProgress: function (id, percent) {
                ui_multi_update_file_progress(id, percent);
            },
            onUploadSuccess: function (id, productId) {
                loadVideoPreview(productId);
            },
            onFileSizeError: function (file) {
                $("#drag-and-drop-zone-video .error-message-file-upload").show();
                $("#drag-and-drop-zone-video .error-message-file-upload p").html("<?= trans('file_too_large', true) . ' ' . formatSizeUnits($productSettings->max_file_size_video); ?>");
                setTimeout(function () {
                    $("#drag-and-drop-zone-video .error-message-file-upload").fadeOut("slow");
                }, 4000)
            }
        });
    });

    function loadVideoPreview(productId) {
        var data = {
            'product_id': productId
        };
        $.ajax({
            type: 'POST',
            url: MdsConfig.baseURL + '/load-video-preview-post',
            data: setAjaxData(data),
            success: function (response) {
                setTimeout(function () {
                    document.getElementById("video_upload_result").innerHTML = response;
                    const player = new Plyr('#player');
                }, 4000);
            }
        });
    }
</script>


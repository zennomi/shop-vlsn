<div class="col-sm-12">
    <div id="audio_upload_result">
        <?= view('dashboard/product/_audio_upload_response'); ?>
    </div>
</div>

<script type="text/html" id="files-template-audio">
    <li class="media">
        <div class="media-body">
            <div class="progress">
                <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
    </li>
</script>

<script>
    $('#drag-and-drop-zone-audio').dmUploader({
        url: '<?= base_url('upload-audio-post'); ?>',
        maxFileSize: <?= $productSettings->max_file_size_audio; ?>,
        queue: true,
        extFilter: ["mp3", "wav"],
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
            ui_multi_add_file(id, file, "audio");
        },
        onBeforeUpload: function (id) {
            ui_multi_update_file_progress(id, 0, '', true);
            ui_multi_update_file_status(id, 'uploading', 'Uploading...');
        },
        onUploadProgress: function (id, percent) {
            ui_multi_update_file_progress(id, percent);
        },
        onUploadSuccess: function (id, productId) {
            loadAudioPreview(productId);
        },
        onFileSizeError: function (file) {
            $("#drag-and-drop-zone-audio .error-message-file-upload").show();
            $("#drag-and-drop-zone-audio .error-message-file-upload p").html("<?= trans('file_too_large') . ' ' . formatSizeUnits($productSettings->max_file_size_audio); ?>");
            setTimeout(function () {
                $("#drag-and-drop-zone-audio .error-message-file-upload").fadeOut("slow");
            }, 4000)
        }
    });
    $(document).ajaxStop(function () {
        $('#drag-and-drop-zone-audio').dmUploader({
            url: '<?= base_url('upload-audio-post'); ?>',
            maxFileSize: <?= $productSettings->max_file_size_audio; ?>,
            queue: true,
            extFilter: ["mp3", "wav"],
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
                ui_multi_add_file(id, file, "audio");
            },
            onBeforeUpload: function (id) {
                ui_multi_update_file_progress(id, 0, '', true);
                ui_multi_update_file_status(id, 'uploading', 'Uploading...');
            },
            onUploadProgress: function (id, percent) {
                ui_multi_update_file_progress(id, percent);
            },
            onUploadSuccess: function (id, productId) {
                loadAudioPreview(productId);
            },
            onFileSizeError: function (file) {
                $("#drag-and-drop-zone-audio .error-message-file-upload").show();
                $("#drag-and-drop-zone-audio .error-message-file-upload p").html("<?= trans('file_too_large') . ' ' . formatSizeUnits($productSettings->max_file_size_audio); ?>");
                setTimeout(function () {
                    $("#drag-and-drop-zone-audio .error-message-file-upload").fadeOut("slow");
                }, 4000)
            }
        });
    });
    
    function loadAudioPreview(productId) {
        var data = {
            'product_id': productId
        };
        $.ajax({
            type: 'POST',
            url: MdsConfig.baseURL + '/load-audio-preview-post',
            data: setAjaxData(data),
            success: function (response) {
                setTimeout(function () {
                    document.getElementById("audio_upload_result").innerHTML = response;
                    const audio_player = new Plyr('#audio_player');
                }, 4000);
            }
        });
    }
</script>


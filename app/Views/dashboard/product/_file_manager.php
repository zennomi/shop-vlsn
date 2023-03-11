<div class="modal fade" id="fileManagerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-file-manager" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= trans("images"); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="icon-close"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="file-manager">
                    <div class="file-manager-left">
                        <div class="dm-uploader-container">
                            <div id="drag-and-drop-zone-file-manager" class="dm-uploader text-center">
                                <p class="file-manager-file-types">
                                    <span>JPG</span>
                                    <span>JPEG</span>
                                    <span>PNG</span>
                                </p>
                                <p class="dm-upload-icon">
                                    <i class="icon-upload"></i>
                                </p>
                                <p class="dm-upload-text"><?= trans("drag_drop_images_here"); ?></p>
                                <p class="text-center">
                                    <button class="btn btn-default btn-browse-files"><?= trans('browse_files'); ?></button>
                                </p>
                                <a class='btn btn-md dm-btn-select-files'>
                                    <input type="file" name="file" size="40" multiple="multiple">
                                </a>
                                <ul class="dm-uploaded-files" id="files-file-manager"></ul>
                                <button type="button" id="btn_reset_upload_image" class="btn btn-reset-upload"><?= trans("reset"); ?></button>
                            </div>
                        </div>
                    </div>
                    <div class="file-manager-right">
                        <div class="file-manager-content">
                            <div id="ckimage_file_upload_response">
                                <?php if (!empty($fileManagerImages)):
                                    foreach ($fileManagerImages as $image): ?>
                                        <div class="col-file-manager" id="fm_img_col_id_<?= $image->id; ?>">
                                            <div class="file-box" data-file-id="<?= $image->id; ?>" data-file-path="<?= getFileManagerImageUrl($image); ?>">
                                                <div class="image-container">
                                                    <img src="<?= getFileManagerImageUrl($image); ?>" alt="" class="img-responsive">
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach;
                                endif; ?>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="selected_fm_img_file_id">
                    <input type="hidden" id="selected_fm_img_file_path">
                </div>
            </div>
            <div class="modal-footer">
                <div class="file-manager-footer">
                    <button type="button" id="btn_fm_img_delete" class="btn btn-sm btn-danger color-white pull-left btn-file-delete m-r-3"><i class="icon-trash"></i>&nbsp;&nbsp;<?= trans('delete'); ?></button>
                    <button type="button" id="btn_fm_img_select" class="btn btn-sm btn-info color-white btn-file-select"><i class="icon-check"></i>&nbsp;&nbsp;<?= trans('select_image'); ?></button>
                    <button type="button" class="btn btn-sm btn-secondary color-white" data-dismiss="modal"><?= trans('close'); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/html" id="files-template-file-manager">
    <li class="media">
        <img class="preview-img" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" alt="">
        <div class="media-body">
            <div class="progress">
                <div class="dm-progress-waiting"><?= trans("waiting"); ?></div>
                <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
    </li>
</script>

<script>
    $(function () {
        $('#drag-and-drop-zone-file-manager').dmUploader({
            url: '<?= base_url('upload-file-manager-images-post'); ?>',
            queue: true,
            allowedTypes: 'image/*',
            extFilter: ["jpg", "jpeg", "png"],
            extraData: function (id) {
                return {
                    'file_id': id,
                    '<?= csrf_token() ?>': '<?= csrf_hash(); ?>'
                };
            },
            onNewFile: function (id, file) {
                ui_multi_add_file(id, file, 'file-manager');
                if (typeof FileReader !== "undefined") {
                    var reader = new FileReader();
                    var img = $('#uploaderFile' + id).find('img');
                    reader.onload = function (e) {
                        img.attr('src', e.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            },
            onBeforeUpload: function (id) {
                $('#uploaderFile' + id + ' .dm-progress-waiting').hide();
                ui_multi_update_file_progress(id, 0, '', true);
                ui_multi_update_file_status(id, 'uploading', 'Uploading...');
                $("#btn_reset_upload_image").show();
            },
            onUploadProgress: function (id, percent) {
                ui_multi_update_file_progress(id, percent);
            },
            onUploadSuccess: function (id, data) {
                document.getElementById("uploaderFile" + id).remove();
                refreshFileManagerImages();
                ui_multi_update_file_status(id, 'success', 'Upload Complete');
                ui_multi_update_file_progress(id, 100, 'success', false);
                $("#btn_reset_upload_image").hide();
            }
        });
    });
    $(document).on('click', '#btn_reset_upload_image', function () {
        $("#drag-and-drop-zone-file-manager").dmUploader("reset");
        $("#files-file-manager").empty();
        $(this).hide();
    });
</script>
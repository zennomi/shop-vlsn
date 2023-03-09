<div class="row">
    <div class="col-12">
        <?php if (!empty($product)):
            if (!empty($productVideo)):?>
                <div class="dm-uploader-container">
                    <div id="drag-and-drop-zone-video" class="dm-uploader dm-uploader-media text-center">
                        <ul class="dm-uploaded-files dm-uploaded-media-file">
                            <li class="media li-dm-media-preview">
                                <video id="player" playsinline controls>
                                    <source src="<?= getProductVideoUrl($productVideo); ?>" type="video/mp4">
                                </video>
                                <a href="javascript:void(0)" class="btn-img-delete btn-video-delete" onclick="deleteProductVideoPreview('<?= $product->id; ?>','<?= trans("confirm_product_video", true) ?>');">
                                    <i class="icon-close"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            <?php else: ?>
                <div class="dm-uploader-container">
                    <div id="drag-and-drop-zone-video" class="dm-uploader dm-uploader-media text-center">
                        <p class="dm-upload-icon">
                            <i class="icon-upload"></i>
                        </p>
                        <p class="dm-upload-text"><?= trans("drag_drop_file_here"); ?>&nbsp;<span style="text-decoration: underline"><?= trans('browse_files'); ?></span></p>
                        <a class='btn btn-md dm-btn-select-files'>
                            <input type="file" name="file">
                        </a>
                        <ul class="dm-uploaded-files dm-uploaded-media-file" id="files-video"></ul>
                        <div class="error-message-file-upload">
                            <p class="m-0 text-center"></p>
                        </div>
                    </div>
                </div>
            <?php endif;
        endif; ?>
    </div>
</div>
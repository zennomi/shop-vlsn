<?php if (!empty($product)):
    $digitalFile = getProductDigitalFile($product->id);
    if (!empty($digitalFile)):?>
        <form action="<?= base_url('download-digital-file-post'); ?>" method="post" id="form_download_digital_file">
            <?= csrf_field(); ?>
            <input type="hidden" name="file_id" value="<?= $digitalFile->id; ?>">
            <div class="dm-uploaded-digital-file">
                <a href="javascript:void(0)" class="pull-left link-uploaded-digital-file" onclick="$('#form_download_digital_file').submit();">
                    <i class="icon-file-archive file-icon"></i>&nbsp;&nbsp;<strong><?= esc($digitalFile->file_name); ?></strong>
                </a>
                <a href="javascript:void(0)" class="btn btn-sm btn-danger pull-right" onclick="deleteProductDigitalFile('<?= $digitalFile->id; ?>','<?= trans("confirm_delete", true) ?>');">
                    <i class="icon-trash"></i>&nbsp;&nbsp;<?= trans("delete"); ?>
                </a>
                <button type="submit" class="btn btn-sm btn-info color-white pull-right m-r-5">
                    <i class="icon-download-solid"></i>&nbsp;&nbsp;<?= trans("download"); ?>
                </button>
            </div>
        </form>
    <?php else: ?>
        <div class="dm-uploader-container">
            <div id="drag-and-drop-zone-digital-files" class="dm-uploader dm-uploader-media text-center">
                <p class="dm-upload-icon">
                    <i class="icon-upload"></i>
                </p>
                <p class="dm-upload-text"><?= trans("drag_drop_file_here"); ?>&nbsp;<span style="text-decoration: underline"><?= trans('browse_files'); ?></span></p>
                <a class='btn btn-md dm-btn-select-files'>
                    <input type="file" name="file">
                </a>
                <ul class="dm-uploaded-files dm-uploaded-media-file" id="files-digital-files"></ul>
            </div>
        </div>
    <?php endif;
endif; ?>

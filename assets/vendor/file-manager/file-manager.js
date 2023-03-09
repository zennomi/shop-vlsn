/**
 ------------------------------------------------------------------------------------------------
 File Manager
 ------------------------------------------------------------------------------------------------
 */
var data_editor_id = "";
$(document).on('click', '#btn_add_image_editor', function () {
    data_editor_id = $(this).attr('data-editor-id');
    refreshFileManagerImages();
    $('#selected_fm_img_file_id').val('');
    $('#selected_fm_img_file_path').val('');
    $('#btn_fm_img_delete').hide();
    $('#btn_fm_img_select').hide();
});

$(document).on('click', '#fileManagerModal .file-box', function () {
    $('.file-manager .file-box').removeClass('selected');
    $(this).addClass('selected');
    var val_id = $(this).attr('data-file-id');
    var val_path = $(this).attr('data-file-path');
    $('#selected_fm_img_file_id').val(val_id);
    $('#selected_fm_img_file_path').val(val_path);

    $('#btn_fm_img_delete').show();
    $('#btn_fm_img_select').show();
});

//refresh file manager images
function refreshFileManagerImages() {
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/FileController/getFileManagerImages',
        data: setAjaxData({}),
        success: function (response) {
            var obj = JSON.parse(response);
            if (obj.result == 1) {
                document.getElementById("ckimage_file_upload_response").innerHTML = obj.content;
            }
        }
    });
}

//select image file
$(document).on('click', '#fileManagerModal #btn_fm_img_select', function () {
    var imgUrl = $('#selected_fm_img_file_path').val();
    tinymce.get(data_editor_id).execCommand('mceInsertContent', false, '<p><img src="' + imgUrl + '" alt=""/></p>');
    $('#fileManagerModal').modal('toggle');
});

//select image file on double click
$(document).on('dblclick', '#fileManagerModal .file-box', function () {
    var imgUrl = $('#selected_fm_img_file_path').val();
    tinymce.get(data_editor_id).execCommand('mceInsertContent', false, '<p><img src="' + imgUrl + '" alt=""/></p>');
    $('#fileManagerModal').modal('toggle');
});

//delete image file
$(document).on('click', '#fileManagerModal #btn_fm_img_delete', function () {
    var fileId = $('#selected_fm_img_file_id').val();
    $('#fm_img_col_id_' + fileId).remove();
    var data = {
        'file_id': fileId
    };
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/FileController/deleteFileManagerImage',
        data: setAjaxData(data),
        success: function (response) {
            $('#btn_fm_img_delete').hide();
            $('#btn_fm_img_select').hide();
        }
    });
});

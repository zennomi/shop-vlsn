/**
 ------------------------------------------------------------------------------------------------
 Image File Manager
 ------------------------------------------------------------------------------------------------
 */
var manager_image_type = "main";
$(document).on('click', '.btn-file-manager', function () {
    manager_image_type = $(this).attr("data-image-type");
    refreshFileManagerImages();
    $('#selected_file_manager_img_id').val('');
    $('#selected_file_manager_img_path').val('');
    $('#btn_file_manager_delete').hide();
    $('#btn_file_manager_select').hide();
});
$(document).on('click', '#imageFileManagerModal .file-box', function () {
    $('.file-manager .file-box').removeClass('selected');
    $(this).addClass('selected');
    var val_id = $(this).attr('data-file-id');
    var val_path = $(this).attr('data-file-path');
    $('#selected_file_manager_img_id').val(val_id);
    $('#selected_file_manager_img_path').val(val_path);

    $('#btn_file_manager_delete').show();
    $('#btn_file_manager_select').show();
});

//refresh file manager images
function refreshFileManagerImages() {
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/FileController/getBlogImages',
        data: setAjaxData({}),
        success: function (response) {
            var obj = JSON.parse(response);
            if (obj.result == 1) {
                document.getElementById("image_file_manager_upload_response").innerHTML = obj.content;
            }
        }
    });
}

//select image file
$(document).on('click', '#imageFileManagerModal #btn_file_manager_select', function () {
    select_image();
});

//select image file on double click
$(document).on('dblclick', '#imageFileManagerModal .file-box', function () {
    select_image();
});

//select image
function select_image() {
    var img_id = $('#selected_file_manager_img_id').val();
    var img_path = $('#selected_file_manager_img_path').val();
    if (manager_image_type == "editor") {
        tinymce.activeEditor.execCommand('mceInsertContent', false, '<p><img src="' + img_path + '" alt=""/></p>');
    } else {
        var image = '<div class="post-select-image-container">' +
            '<img src="' + img_path + '" alt="">' +
            '<a id="btn_delete_blog_main_image" class="btn btn-danger btn-sm btn-delete-selected-file-image">' +
            '<i class="fa fa-times"></i> ' +
            '</a>' +
            '</div>';
        document.getElementById("blog_select_image_container").innerHTML = image;
        $('input[name=blog_image_id]').val(img_id);
    }
    $('#imageFileManagerModal').modal('toggle');
}

//delete image file
$(document).on('click', '#imageFileManagerModal #btn_file_manager_delete', function () {
    var fileId = $('#selected_file_manager_img_id').val();
    $('#file_manager_col_id_' + fileId).remove();
    var data = {
        'file_id': fileId
    };
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/FileController/deleteBlogImage',
        data: setAjaxData(data),
        success: function (response) {
            $('#btn_file_manager_delete').hide();
            $('#btn_file_manager_select').hide();
        }
    });
});

//delete blog main image
$(document).on('click', '#btn_delete_blog_main_image', function () {
    var content = '<a class="btn-select-image" data-toggle="modal" data-target="#imageFileManagerModal">' +
        '<div class="btn-select-image-inner">' +
        '<i class="fa fa-image"></i>' +
        '<button class="btn">' + MdsConfig.textSelectImage + '</button>' +
        '</div>' +
        '</a>';
    document.getElementById("blog_select_image_container").innerHTML = content;
    $("#blog_image_id").val('');
});

//delete blog main image database
$(document).on('click', '#btn_delete_blog_main_image_database', function () {
    var data = {
        'post_id': $(this).attr("data-post-id")
    };
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/BlogController/deletePostImagePost',
        data: setAjaxData(data),
        success: function (response) {
            var content = '<a class="btn-select-image" data-toggle="modal" data-target="#imageFileManagerModal">' +
                '<div class="btn-select-image-inner">' +
                '<i class="fa fa-image"></i>' +
                '<button class="btn">' + MdsConfig.textSelectImage + '</button>' +
                '</div>' +
                '</a>';
            document.getElementById("blog_select_image_container").innerHTML = content;
            $("#blog_image_id").val('');
            $('#btn_file_manager_delete').hide();
            $('#btn_file_manager_select').hide();
        }
    });
});

jQuery(function ($) {
    $('.file-manager-content').on('scroll', function () {
        if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
            var min = 0;
            $('#image_file_manager_upload_response .file-box').each(function () {
                var value = parseInt($(this).attr('data-file-id'));
                if (min == 0) {
                    min = value;
                }
                if (value < min) {
                    min = value;
                }
            });
            var data = {
                'min': min
            };
            $.ajax({
                type: 'POST',
                url: MdsConfig.baseURL + '/FileController/loadMoreBlogImages',
                data: setAjaxData(data),
                success: function (response) {
                    setTimeout(function () {
                        var obj = JSON.parse(response);
                        if (obj.result == 1) {
                            $("#image_file_manager_upload_response").append(obj.content);
                        }
                    }, 100);
                }
            });
        }

    })
});

$('#imageFileManagerModal').on('show.bs.modal', function (e) {
    refreshFileManagerImages();
});
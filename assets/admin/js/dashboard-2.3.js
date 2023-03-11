function getStates(val, map) {
    $('#select_states').children('option').remove();
    $('#select_cities').children('option').remove();
    $('#get_states_container').hide();
    $('#get_cities_container').hide();
    var data = {
        'country_id': val,
    };
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/AjaxController/getStates',
        data: setAjaxData(data),
        success: function (response) {
            var obj = JSON.parse(response);
            if (obj.result == 1) {
                document.getElementById("select_states").innerHTML = obj.content;
                $('#get_states_container').show();
            } else {
                document.getElementById("select_states").innerHTML = '';
                $('#get_states_container').hide();
            }
        }
    });
}

function getCities(val, map) {
    var data = {
        'state_id': val,
    };
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/AjaxController/getCities',
        data: setAjaxData(data),
        success: function (response) {
            var obj = JSON.parse(response);
            if (obj.result == 1) {
                document.getElementById("select_cities").innerHTML = obj.content;
                $('#get_cities_container').show();
            } else {
                document.getElementById("select_cities").innerHTML = '';
                $('#get_cities_container').hide();
            }
        }
    });
}

//set main image session
$(document).on('click', '.btn-set-image-main-session', function () {
    var fileId = $(this).attr('data-file-id');
    var data = {
        'file_id': fileId
    };
    $('.btn-is-image-main').removeClass('btn-success');
    $('.btn-is-image-main').addClass('btn-secondary');
    $(this).removeClass('btn-secondary');
    $(this).addClass('btn-success');
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/FileController/setImageMainSession',
        data: setAjaxData(data),
        success: function (response) {
        }
    });
});

//set main image
$(document).on('click', '.btn-set-image-main', function () {
    var imageId = $(this).attr('data-image-id');
    var productId = $(this).attr('data-product-id');
    var data = {
        'image_id': imageId,
        'product_id': productId
    };
    $('.btn-is-image-main').removeClass('btn-success');
    $('.btn-is-image-main').addClass('btn-secondary');
    $(this).removeClass('btn-secondary');
    $(this).addClass('btn-success');
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/FileController/setImageMain',
        data: setAjaxData(data),
        success: function (response) {
        }
    });
});

//delete product image session
$(document).on('click', '.btn-delete-product-img-session', function () {
    var fileId = $(this).attr('data-file-id');
    var data = {
        'file_id': fileId
    };
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/FileController/deleteImageSession',
        data: setAjaxData(data),
        success: function () {
            imageUploadCount = imageUploadCount - 1;
            if (imageUploadCount < 0) {
                imageUploadCount = 0;
            }
            $('#uploaderFile' + fileId).remove();
        }
    });
});

//delete product image
$(document).on('click', '.btn-delete-product-img', function () {
    var fileId = $(this).attr('data-file-id');
    var data = {
        'file_id': fileId
    };
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/FileController/deleteImage',
        data: setAjaxData(data),
        success: function (response) {
            location.reload();
        }
    });
});

//delete product video preview
function deleteProductVideoPreview(productId, message) {
    swal(swalOptions(message)).then(function (isConfirm) {
        if (isConfirm) {
            var data = {
                'product_id': productId
            };
            $.ajax({
                type: 'POST',
                url: MdsConfig.baseURL + '/FileController/deleteVideo',
                data: setAjaxData(data),
                success: function (response) {
                    document.getElementById("video_upload_result").innerHTML = response;
                }
            });
        }
    });
}

//delete product audio preview
function deleteProductAudioPreview(productId, message) {
    swal(swalOptions(message)).then(function (isConfirm) {
        if (isConfirm) {
            var data = {
                'product_id': productId
            };
            $.ajax({
                type: 'POST',
                url: MdsConfig.baseURL + '/FileController/deleteAudio',
                data: setAjaxData(data),
                success: function (response) {
                    document.getElementById("audio_upload_result").innerHTML = response;
                }
            });
        }
    });
}

function generateUniqueString() {
    var time = String(new Date().getTime()),
        i = 0,
        output = '';
    for (i = 0; i < time.length; i += 2) {
        output += Number(time.substr(i, 2)).toString(36);
    }
    return (output.toUpperCase());
}

$('input[type=radio][name=product_type]').change(function () {
    $('input[name=listing_type]').prop('checked', false);
    if (this.value == 'digital') {
        $('.listing_ordinary_listing').hide();
        $('.listing_bidding').hide();
        $('.listing_license_keys').show();
    } else {
        $('.listing_ordinary_listing').show();
        $('.listing_bidding').show();
        $('.listing_license_keys').hide();
    }
});

//delete product digital file
function deleteProductDigitalFile(fileId, message) {
    swal(swalOptions(message)).then(function (isConfirm) {
        if (isConfirm) {
            var data = {
                'file_id': fileId
            };
            $.ajax({
                type: 'POST',
                url: MdsConfig.baseURL + '/FileController/deleteDigitalFile',
                data: setAjaxData(data),
                success: function (response) {
                    var obj = JSON.parse(response);
                    if (obj.result == 1) {
                        document.getElementById("digital_files_upload_result").innerHTML = obj.htmlContent;
                    }
                }
            });
        }
    });
}

/*
 * --------------------------------------------------------------------
 * License Key Functions
 * --------------------------------------------------------------------
 */

//add license key
function addLicenseKeys(productId) {
    var licenseKeys = $('#textarea_license_keys').val();
    if (licenseKeys.trim() != "") {
        $(".btn-add-license-keys").prop('disabled', true);
        $(".loader-license-keys").show();
        var data = {
            'product_id': productId,
            'license_keys': licenseKeys,
            'allow_dublicate': $("input[name='allow_dublicate_license_keys']:checked").val()
        };
        $.ajax({
            type: 'POST',
            url: MdsConfig.baseURL + '/DashboardController/addLicenseKeys',
            data: setAjaxData(data),
            success: function (response) {
                var obj = JSON.parse(response);
                if (obj.result == 1) {
                    document.getElementById("result-add-license-keys").innerHTML = obj.message;
                    $('#textarea_license_keys').val('');
                    setTimeout(function () {
                        $(".btn-add-license-keys").prop('disabled', false);
                        $(".loader-license-keys").hide();
                    }, 500);
                }
            }
        });
    }
}

//delete license key
function deleteLicenseKey(id, productId) {
    var data = {
        'id': id,
        'product_id': productId
    };
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/DashboardController/deleteLicenseKey',
        data: setAjaxData(data),
        success: function (response) {
            $('#tr_license_key_' + id).remove();
        }
    });
}

//update license code list on modal open
$("#viewLicenseKeysModal").on('show.bs.modal', function () {
    var productId = $('#license_key_list_product_id').val();
    var data = {
        'product_id': productId
    };
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/DashboardController/loadLicenseKeysList',
        data: setAjaxData(data),
        success: function (response) {
            var obj = JSON.parse(response);
            if (obj.result == 1) {
                document.getElementById("response_license_key").innerHTML = obj.htmlContent;
            }
        }
    });
});

//get filter subcategories
function getFilterSubCategoriesDashboard(val) {
    var data = {
        'parent_id': val
    };
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/DashboardController/getSubCategories',
        data: setAjaxData(data),
        success: function (response) {
            $('#subcategories').children('option:not(:first)').remove();
            $("#subcategories").append(response);
        }
    });
}
<link rel="stylesheet" href="<?= base_url('assets/vendor/colorpicker/bootstrap-colorpicker.min.css'); ?>">
<script src="<?= base_url('assets/vendor/colorpicker/bootstrap-colorpicker.min.js'); ?>"></script>
<script>
    //add product variation post
    $("#form_add_product_variation").submit(function (event) {
        event.preventDefault();
        var input_variation_label = $.trim($('#input_variation_label').val());
        if (input_variation_label.length < 1) {
            $('#input_variation_label').addClass("is-invalid");
            return false;
        } else {
            $('#input_variation_label').removeClass("is-invalid");
        }
        var form = $(this);
        var serializedData = form.serializeArray();
        serializedData = setSerializedData(serializedData);
        $.ajax({
            type: 'POST',
            url: MdsConfig.baseURL + '/add-variation-post',
            data: serializedData,
            success: function (response) {
                $(".input-variation-label").val('');
                $("#addVariationModal").modal('hide');
                $(".variation-options-container").empty();
                var obj = JSON.parse(response);
                if (obj.result == 1) {
                    document.getElementById("response_product_variations").innerHTML = obj.htmlContent;
                }
            }
        });
    });

    //edit product variation
    function editProductVariation(id) {
        $("#btn-variation-edit-" + id).css("visibility", "hidden");
        $("#sp-edit-" + id).show();
        var data = {
            'id': id
        };
        $.ajax({
            type: 'POST',
            url: MdsConfig.baseURL + '/edit-variation',
            data: setAjaxData(data),
            success: function (response) {
                var obj = JSON.parse(response);
                if (obj.result == 1) {
                    document.getElementById("response_product_variation_edit").innerHTML = obj.htmlContent;
                }
                setTimeout(
                    function () {
                        $("#editVariationModal").modal('show');
                        $("#btn-variation-edit-" + id).css("visibility", "visible");
                        $("#sp-edit-" + id).hide();
                    }, 250);
            }
        });
    }

    //edit product variation post
    $("#form_edit_product_variation").submit(function (event) {
        event.preventDefault();
        var input_variation_label = $.trim($('#input_variation_label_edit').val());
        if (input_variation_label.length < 1) {
            $('#input_variation_label_edit').addClass("is-invalid");
            return false;
        } else {
            $('#input_variation_label_edit').removeClass("is-invalid");
        }
        var form = $(this);
        var serializedData = form.serializeArray();
        serializedData = setSerializedData(serializedData);
        $.ajax({
            type: 'POST',
            url: MdsConfig.baseURL + '/edit-variation-post',
            data: serializedData,
            success: function (response) {
                $(".input-variation-label").val('');
                $("#editVariationModal").modal('hide');
                var obj = JSON.parse(response);
                if (obj.result == 1) {
                    document.getElementById("response_product_variations").innerHTML = obj.htmlContent;
                }
            }
        });
    });

    //delete product variation
    function deleteProductVariation(id, message) {
        swal({
            text: message,
            icon: 'warning',
            buttons: [MdsConfig.textCancel, MdsConfig.textOk],
            dangerMode: true,
        }).then(function (willDelete) {
            if (willDelete) {
                var data = {
                    'id': id
                };
                $.ajax({
                    type: 'POST',
                    url: MdsConfig.baseURL + '/delete-variation-post',
                    data: setAjaxData(data),
                    success: function (response) {
                        var obj = JSON.parse(response);
                        if (obj.result == 1) {
                            document.getElementById("response_product_variations").innerHTML = obj.htmlContent;
                        }
                    }
                });
            }
        });
    }

    //add product variation option
    function addProductVariationOption(id) {
        $("#btn-variation-text-add-" + id).css("visibility", "hidden");
        $("#sp-options-add-" + id).show();
        var data = {
            'variation_id': id
        };
        $.ajax({
            type: 'POST',
            url: MdsConfig.baseURL + '/add-variation-option',
            data: setAjaxData(data),
            success: function (response) {
                var obj = JSON.parse(response);
                if (obj.result == 1) {
                    document.getElementById("response_product_add_variation_option").innerHTML = obj.htmlContent;
                }
                setTimeout(
                    function () {
                        $("#addVariationOptionModal").modal('show');
                        $("#btn-variation-text-add-" + id).css("visibility", "visible");
                        $("#sp-options-add-" + id).hide();
                    }, 250);
            }
        });
    }

    //view product variation options
    function viewProductVariationOptions(id) {
        $("#btn-variation-text-options-" + id).css("visibility", "hidden");
        $("#sp-options-" + id).show();
        var data = {
            'variation_id': id
        };
        $.ajax({
            type: 'POST',
            url: MdsConfig.baseURL + '/view-variation-options',
            data: setAjaxData(data),
            success: function (response) {
                var obj = JSON.parse(response);
                if (obj.result == 1) {
                    document.getElementById("response_product_variation_options_edit").innerHTML = obj.htmlContent;
                }
                setTimeout(
                    function () {
                        $("#viewVariationOptionsModal").modal('show');
                        $("#btn-variation-text-options-" + id).css("visibility", "visible");
                        $("#sp-options-" + id).hide();
                    }, 250);
            }
        });
    }

    //edit product variation option
    function editProductVariationOption(variationId, optionId) {
        var data = {
            'variation_id': variationId,
            'option_id': optionId
        };
        $.ajax({
            type: 'POST',
            url: MdsConfig.baseURL + '/edit-variation-option',
            data: setAjaxData(data),
            success: function (response) {
                var obj = JSON.parse(response);
                if (obj.result == 1) {
                    document.getElementById("response_product_edit_variation_option").innerHTML = obj.htmlContent;
                }
                setTimeout(
                    function () {
                        $("#editVariationOptionModal").modal('show');
                    }, 200);
            }
        });
    }

    $(document).on('click', '#btn_add_variation_option', function () {
        var input_variation_option = $.trim($('#input_variation_option_name').val());
        if (input_variation_option.length < 1) {
            $('#input_variation_option_name').addClass("is-invalid");
            return false;
        } else {
            $('#input_variation_option_name').removeClass("is-invalid");
        }
        var form = $("#form_add_product_variation_option");
        var serializedData = form.serializeArray();
        serializedData = setSerializedData(serializedData);
        $(".input-variation-label").val('');
        $.ajax({
            type: 'POST',
            url: MdsConfig.baseURL + '/add-variation-option-post',
            data: serializedData,
            success: function (response) {
                var obj = JSON.parse(response);
                if (obj.result == 1) {
                    document.getElementById("response_product_add_variation_option").innerHTML = obj.htmlContent;
                }
            }
        });
    });

    $(document).on('click', '#btn_edit_variation_option', function () {
        var variationId = $("#form_edit_variation_id").val();
        var input_variation_option = $.trim($('#input_edit_variation_option_name').val());
        if (input_variation_option.length < 1) {
            $('#input_edit_variation_option_name').addClass("is-invalid");
            return false;
        } else {
            $('#input_edit_variation_option_name').removeClass("is-invalid");
        }
        var form = $("#form_edit_product_variation_option");
        var serializedData = form.serializeArray();
        serializedData = setSerializedData(serializedData);
        $(".input-variation-label").val('');
        $.ajax({
            type: 'POST',
            url: MdsConfig.baseURL + '/edit-variation-option-post',
            data: serializedData,
            success: function (response) {
                var obj = JSON.parse(response);
                if (obj.result == 1) {
                    document.getElementById("response_product_edit_variation_option").innerHTML = obj.htmlContent;
                    //refresh variation option lists
                    viewProductVariationOptions(variationId);
                }
            }
        });
    });

    //delete product variation option
    function deleteProductVariationOption(variationId, optionId, message) {
        swal({
            text: message,
            icon: 'warning',
            buttons: [MdsConfig.textCancel, MdsConfig.textOk],
            dangerMode: true,
        }).then(function (willDelete) {
            if (willDelete) {
                var data = {
                    'variation_id': variationId,
                    'option_id': optionId
                };
                $.ajax({
                    type: 'POST',
                    url: MdsConfig.baseURL + '/delete-variation-option-post',
                    data: setAjaxData(data),
                    success: function (response) {
                        var obj = JSON.parse(response);
                        if (obj.result == 1) {
                            document.getElementById("response_product_variation_options_edit").innerHTML = obj.htmlContent;
                        }
                    }
                });
            }
        });
    }

    //select product variation
    $("#form_select_product_variation").submit(function (event) {
        event.preventDefault();
        var form = $(this);
        var serializedData = form.serializeArray();
        serializedData = setSerializedData(serializedData);
        $.ajax({
            type: 'POST',
            url: MdsConfig.baseURL + '/select-variation-post',
            data: serializedData,
            success: function (response) {
                $("#variationModalSelect").modal('hide');
                var obj = JSON.parse(response);
                if (obj.result == 1) {
                    document.getElementById("response_product_variations").innerHTML = obj.htmlContent;
                }
            }
        });
    });

    $(document).on('click', '.btn-delete-variation-image-session', function () {
        var fileId = $(this).attr("data-file-id");
        var data = {
            'file_id': fileId
        };
        $.ajax({
            type: 'POST',
            url: MdsConfig.baseURL + '/delete-variation-image-session-post',
            data: setAjaxData(data),
            success: function (response) {
                $("#uploaderFile" + fileId).remove();
            }
        });
    });

    $(document).on('click', '.btn-delete-variation-image', function () {
        var variationId = $(this).attr("data-variation-id");
        var imageId = $(this).attr("data-file-id");
        var data = {
            'variation_id': variationId,
            'image_id': imageId
        };
        $.ajax({
            type: 'POST',
            url: MdsConfig.baseURL + '/delete-variation-image-post',
            data: setAjaxData(data),
            success: function (response) {
                $("#uploaderFile" + imageId).remove();
                $("#uploaded_vr_img_" + imageId).remove();
            }
        });
    });

    //set main variation image session
    $(document).on('click', '.btn-set-variation-image-main-session', function () {
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
            url: MdsConfig.baseURL + '/set-variation-image-main-session',
            data: setAjaxData(data),
            success: function (response) {
            }
        });
    });

    $(document).on('click', '.btn-set-variation-image-main', function () {
        var fileId = $(this).attr('data-file-id');
        var optionId = $(this).attr('data-option-id');
        var data = {
            'file_id': fileId,
            'option_id': optionId
        };
        $('.btn-is-image-main').removeClass('btn-success');
        $('.btn-is-image-main').addClass('btn-secondary');
        $(this).removeClass('btn-secondary');
        $(this).addClass('btn-success');
        $.ajax({
            type: 'POST',
            url: MdsConfig.baseURL + '/set-variation-image-main',
            data: setAjaxData(data),
            success: function (response) {
            }
        });
    });

    $(document).on('change', '#checkbox_discount_rate_variation', function () {
        if (!this.checked) {
            $("#discount_input_container_variation").show();
        } else {
            $('#input_discount_rate_variation').val("0");
            $("#discount_input_container_variation").hide();
        }
    });
    $(document).on('change', '#checkbox_price_variation', function () {
        if (!this.checked) {
            $("#price_input_container_variation").show();
        } else {
            $('#price_input_container_variation input').val("0");
            $("#price_input_container_variation").hide();
        }
    });
    $(document).on('change', '#checkbox_price_variation', function () {
        if (!this.checked) {
            $("#price_input_container_variation").show();
        } else {
            $('#price_input_container_variation input').val("0");
            $("#price_input_container_variation").hide();
        }
    });

    $(document).on('change', 'input[name=is_default]', function () {
        var value = $('input[name=is_default]:checked').val();
        if (value == 1) {
            $(".hide-if-default").addClass("display-none");
        } else {
            $(".hide-if-default").removeClass("display-none");
        }
    });

    function showHideFormOptionImages(val) {
        if (val == 'radio_button' || val == 'dropdown') {
            $(".form-group-show-option-images").show();
        } else {
            $(".form-group-show-option-images").hide();
        }
        if (val == 'text' || val == 'number' || val == 'dropdown') {
            $(".form-group-display-type").hide();
        } else {
            $(".form-group-display-type").show();
        }
        if (val == 'dropdown') {
            $(".form-group-parent-variation").show();
        } else {
            $(".form-group-parent-variation").hide();
        }
    }

    $(document).ajaxStop(function () {
        $(".colorpicker").colorpicker();
    });
</script>


function setAjaxData(object = null) {
    var data = {
        'sysLangId': MdsConfig.sysLangId,
    };
    data[MdsConfig.csrfTokenName] = $('meta[name="X-CSRF-TOKEN"]').attr('content');
    if (object != null) {
        Object.assign(data, object);
    }
    return data;
}

function setSerializedData(serializedData) {
    serializedData.push({name: 'sysLangId', value: MdsConfig.sysLangId});
    serializedData.push({name: MdsConfig.csrfTokenName, value: $('meta[name="X-CSRF-TOKEN"]').attr('content')});
    return serializedData;
}

//run email queue
$(document).ready(function () {
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/AjaxController/runEmailQueue',
        data: setAjaxData({})
    });
});

function swalOptions(message) {
    return {
        text: message,
        icon: 'warning',
        buttons: true,
        buttons: [MdsConfig.textCancel, MdsConfig.textOk],
        dangerMode: true,
    };
}

//hide left side of the menu if there is image
var menu_elements = document.getElementsByClassName("mega-menu-content");
for (var i = 0; i < menu_elements.length; i++) {
    var id = menu_elements[i].id;
    if (document.getElementById(id).getElementsByClassName("col-category-images")[0]) {
        var content = document.getElementById(id).getElementsByClassName("col-category-images")[0].innerHTML;
        if (content.trim() == "") {
            document.getElementById(id).classList.add("mega-menu-content-no-image");
        }
    }
}

$(document).ready(function () {
    //main slider
    $('#main-slider').on('init', function (e, slick) {
        var $firstAnimatingElements = $('#main-slider .item:first-child').find('[data-animation]');
        doAnimations($firstAnimatingElements);
    });
    $('#main-slider').on('beforeChange', function (e, slick, currentSlide, nextSlide) {
        var $animatingElements = $('#main-slider .item[data-slick-index="' + nextSlide + '"]').find('[data-animation]');
        doAnimations($animatingElements);
    });
    $('#main-slider').slick({
        autoplay: true,
        autoplaySpeed: 9000,
        slidesToShow: 1,
        slidesToScroll: 1,
        infinite: true,
        speed: 500,
        fade: (MdsConfig.sliderFadeEffect == 1) ? true : false,
        swipeToSlide: true,
        rtl: MdsConfig.rtl,
        cssEase: 'linear',
        prevArrow: $('#main-slider-nav .prev'),
        nextArrow: $('#main-slider-nav .next'),
    });

    //main slider
    $('#main-mobile-slider').on('init', function (e, slick) {
        var $firstAnimatingElements = $('#main-mobile-slider .item:first-child').find('[data-animation]');
        doAnimations($firstAnimatingElements);
    });
    $('#main-mobile-slider').on('beforeChange', function (e, slick, currentSlide, nextSlide) {
        var $animatingElements = $('#main-mobile-slider .item[data-slick-index="' + nextSlide + '"]').find('[data-animation]');
        doAnimations($animatingElements);
    });
    $('#main-mobile-slider').slick({
        autoplay: true,
        autoplaySpeed: 9000,
        slidesToShow: 1,
        slidesToScroll: 1,
        infinite: true,
        speed: 500,
        fade: (MdsConfig.sliderFadeEffect == 1) ? true : false,
        swipeToSlide: true,
        rtl: MdsConfig.rtl,
        cssEase: 'linear',
        prevArrow: $('#main-mobile-slider-nav .prev'),
        nextArrow: $('#main-mobile-slider-nav .next')
    });

    function doAnimations(elements) {
        var animationEndEvents = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
        elements.each(function () {
            var $this = $(this);
            var $animationDelay = $this.data('delay');
            var $animationType = 'animated ' + $this.data('animation');
            $this.css({
                'animation-delay': $animationDelay,
                '-webkit-animation-delay': $animationDelay
            });
            $this.addClass($animationType).one(animationEndEvents, function () {
                $this.removeClass($animationType);
            });
        });
    }

    if ($('#slider_special_offers').length != 0) {
        $('#slider_special_offers').slick({
            autoplay: false,
            autoplaySpeed: 4900,
            infinite: true,
            speed: 200,
            swipeToSlide: true,
            rtl: MdsConfig.rtl,
            cssEase: 'linear',
            lazyLoad: 'progressive',
            prevArrow: $('#slider_special_offers_nav .prev'),
            nextArrow: $('#slider_special_offers_nav .next'),
            slidesToShow: 5,
            slidesToScroll: 5,
            responsive: [
                {
                    breakpoint: 992,
                    settings: {
                        slidesToShow: 4,
                        slidesToScroll: 4
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 3
                    }
                },
                {
                    breakpoint: 576,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2
                    }
                }
            ]
        });
    }

    $('#product_slider').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        speed: 300,
        arrows: true,
        fade: true,
        infinite: false,
        swipeToSlide: true,
        cssEase: 'linear',
        lazyLoad: 'progressive',
        prevArrow: $('#product-slider-nav .prev'),
        nextArrow: $('#product-slider-nav .next'),
        asNavFor: '#product_thumbnails_slider'
    });

    $('#product_thumbnails_slider').slick({
        slidesToShow: 7,
        slidesToScroll: 1,
        speed: 300,
        focusOnSelect: true,
        arrows: false,
        infinite: false,
        vertical: true,
        centerMode: false,
        arrows: true,
        cssEase: 'linear',
        lazyLoad: 'progressive',
        prevArrow: $('#product-thumbnails-slider-nav .prev'),
        nextArrow: $('#product-thumbnails-slider-nav .next'),
        asNavFor: '#product_slider'
    });

    $(document).on('click', '#product_thumbnails_slider .slick-slide', function () {
        var index = $(this).attr("data-slick-index");
        $('#product_slider').slick('slickGoTo', parseInt(index));
    });

    $(document).ready(function () {
        baguetteBox.run('.product-slider', {
            animation: MdsConfig.rtl == true ? 'fadeIn' : 'slideIn'
        });
    });

    $(document).ajaxStop(function () {
        baguetteBox.run('.product-slider', {
            animation: MdsConfig.rtl == true ? 'fadeIn' : 'slideIn'
        });
    });

    $('#blog-slider').slick({
        autoplay: false,
        autoplaySpeed: 4900,
        infinite: true,
        speed: 200,
        swipeToSlide: true,
        rtl: MdsConfig.rtl,
        cssEase: 'linear',
        prevArrow: $('#blog-slider-nav .prev'),
        nextArrow: $('#blog-slider-nav .next'),
        slidesToShow: 3,
        slidesToScroll: 3,
        responsive: [
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });

    //rate product
    $(document).on('click', '.rating-stars .label-star', function () {
        $('#user_rating').val($(this).attr('data-star'));
    });

    //mobile memu
    $(document).on('click', '.btn-open-mobile-nav', function () {
        if ($("#navMobile").hasClass('nav-mobile-open')) {
            $("#navMobile").removeClass('nav-mobile-open');
            $('#overlay_bg').hide();
        } else {
            $("#navMobile").addClass('nav-mobile-open');
            $('#overlay_bg').show();
        }
    });
    $(document).on('click', '#overlay_bg', function () {
        $("#navMobile").removeClass('nav-mobile-open');
        $('#overlay_bg').hide();
    });
    //close menu
    $(document).on('click', '.close-menu-click', function () {
        $("#navMobile").removeClass('nav-mobile-open');
        $('#overlay_bg').hide();
    });

});

//mobile menu
var obj_mobile_nav = {
    id: "",
    name: "",
    parent_id: "",
    parent_name: "",
    back_button: 1
};
$(document).on('click', '#navbar_mobile_categories li a', function () {
    obj_mobile_nav.id = $(this).attr('data-id');
    obj_mobile_nav.name = ($(this).text() != "") ? $(this).text() : '';
    obj_mobile_nav.parent_id = ($(this).attr('data-parent-id') != null) ? $(this).attr('data-parent-id') : 0;
    obj_mobile_nav.back_button = 1;
    mobile_menu();
});
$(document).on('click', '#navbar_mobile_back_button a', function () {
    obj_mobile_nav.id = $(this).attr('data-id');
    obj_mobile_nav.name = ($(this).attr('data-category-name') != null) ? $(this).attr('data-category-name') : '';
    obj_mobile_nav.parent_id = ($(this).attr('data-parent-id') != null) ? $(this).attr('data-parent-id') : 0;
    if (obj_mobile_nav.id == 0) {
        obj_mobile_nav.back_button = 0;
    }
    mobile_menu();
});

function mobile_menu() {
    var categories = $('.mega-menu li a[data-parent-id="' + obj_mobile_nav.id + '"]');
    if (categories.length > 0) {
        if (obj_mobile_nav.back_button == 1) {
            $("#navbar_mobile_links").hide();
        } else {
            $("#navbar_mobile_links").show();
            $("#navbar_mobile_back_button").empty();
        }
        $("#navbar_mobile_categories").empty();
        $("#navbar_mobile_back_button").empty();
        if (obj_mobile_nav.back_button == 1) {
            if (obj_mobile_nav.parent_id == 0) {
                document.getElementById("navbar_mobile_back_button").innerHTML = '<a href="javascript:void(0)" class="nav-link" data-id="0"><strong><i class="icon-angle-left"></i>' + obj_mobile_nav.name + '</strong></a>';
            } else {
                var item_parent_name = $('.mega-menu li a[data-id="' + obj_mobile_nav.parent_id + '"]').text();
                document.getElementById("navbar_mobile_back_button").innerHTML = '<a href="javascript:void(0)" class="nav-link" data-id="' + obj_mobile_nav.parent_id + '" data-category-name="' + item_parent_name + '"><strong><i class="icon-angle-left"></i>' + obj_mobile_nav.name + '</strong></a>';
            }
            var item_all_link = $('.mega-menu li a[data-id="' + obj_mobile_nav.id + '"]').attr("href");
            document.getElementById("navbar_mobile_categories").innerHTML = '<li class="nav-item"><a href="' + item_all_link + '" class="nav-link">' + MdsConfig.textAll + '</a></li>';
        }
        $('.mega-menu li a[data-parent-id="' + obj_mobile_nav.id + '"]').each(function () {
            var item_id = $(this).attr("data-id");
            var item_parent_id = obj_mobile_nav.id;
            var item_link = $(this).attr("href");
            var item_text = $(this).text();
            var item_has_sb = $(this).attr("data-has-sb");
            var has_sub = false;
            var sub_id = parseInt($('.navbar-nav a[data-parent-id="' + item_id + '"]').attr('data-id'));
            if (!isNaN(sub_id) && sub_id > 0) {
                has_sub = true;
            }
            if (item_has_sb == 1 && has_sub == true) {
                $("#navbar_mobile_categories").append('<li class="nav-item"><a href="javascript:void(0)" class="nav-link" data-id="' + item_id + '" data-parent-id="' + item_parent_id + '">' + item_text + '<i class="icon-arrow-right"></i></a></li>');
            } else {
                $("#navbar_mobile_categories").append('<li class="nav-item"><a href="' + item_link + '" class="nav-link">' + item_text + '</a></li>');
            }
        });

        $(".nav-mobile-links").addClass('slide-in-150s');
        setTimeout(function () {
            $(".nav-mobile-links").removeClass('slide-in-150s');
        }, 150);
    }
}

//search
$(document).on('click', '.nav-mobile-header-container .a-search-icon', function () {
    if ($(".mobile-search-form").hasClass("display-block")) {
        $(".mobile-search-form").removeClass("display-block");
        $(".mobile-search .search-icon i").removeClass("icon-close");
        $(".mobile-search .search-icon i").addClass("icon-search")
    } else {
        $(".mobile-search-form").addClass("display-block");
        $(".mobile-search .search-icon i").removeClass("icon-search");
        $(".mobile-search .search-icon i").addClass("icon-close")
    }
});

//custom scrollbar
$(function () {
    $('.filter-custom-scrollbar').overlayScrollbars({});
    $('.search-results-product').overlayScrollbars({});
    $('.search-results-location').overlayScrollbars({});
    $('.search-categories').overlayScrollbars({});
    $('.custom-scrollbar').overlayScrollbars({});
    $('.messages-sidebar').overlayScrollbars({});
    if ($('#message-custom-scrollbar').length > 0) {
        var instance_message_scrollbar = OverlayScrollbars(document.getElementById('message-custom-scrollbar'), {});
        instance_message_scrollbar.scroll({y: "100%"}, 0);
    }
});

/*mega menu*/
$(".mega-menu .nav-item").hover(function () {
    var menu_id = $(this).attr('data-category-id');
    $("#mega_menu_content_" + menu_id).show();
    $(".large-menu-item").removeClass('active');
    $(".large-menu-item-first").addClass('active');
    $(".large-menu-content-first").addClass('active');
    //$("#menu-overlay").show();
}, function () {
    var menu_id = $(this).attr('data-category-id');
    $("#mega_menu_content_" + menu_id).hide();
    //$("#menu-overlay").hide();
});

$(".mega-menu .dropdown-menu").hover(function () {
    $(this).show();
}, function () {
});

$(".large-menu-item").hover(function () {
    var menu_id = $(this).attr('data-subcategory-id');
    $(".large-menu-item").removeClass('active');
    $(this).addClass('active');
    $(".large-menu-content").removeClass('active');
    $("#large_menu_content_" + menu_id).addClass('active');
}, function () {
});

//scrollup
$(window).scroll(function () {
    if ($(this).scrollTop() > 100) {
        $(".scrollup").fadeIn()
    } else {
        $(".scrollup").fadeOut()
    }
});
$(".scrollup").click(function () {
    $("html, body").animate({scrollTop: 0}, 700);
    return false
});

$(document).on('click', '.quantity-select-product .dropdown-menu .dropdown-item', function () {
    $(".quantity-select-product .btn span").text($(this).text());
    $("input[name='product_quantity']").val($(this).text());
});

//show phone number
$(document).on('click', '#show_phone_number', function () {
    $(this).hide();
    $("#phone_number").show();
});

$(document).ready(function () {
    $(".select2").select2({
        placeholder: $(this).attr('data-placeholder'),
        height: 42,
        dir: MdsConfig.rtl == true ? "rtl" : "ltr",
        "language": {
            "noResults": function () {
                return MdsConfig.textNoResultsFound;
            }
        },
    });
});

/*
 * --------------------------------------------------------------------
 * Auth Functions
 * --------------------------------------------------------------------
 */

//login
$(document).ready(function () {
    $("#form_login").submit(function (event) {
        var form = $(this);
        if (form[0].checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
        } else {
            event.preventDefault();
            var inputs = form.find("input, select, button, textarea");
            var serializedData = form.serializeArray();
            serializedData = setSerializedData(serializedData);
            $.ajax({
                type: 'POST',
                url: MdsConfig.baseURL + '/login-post',
                data: serializedData,
                success: function (response) {
                    var obj = JSON.parse(response);
                    if (obj.result == 1) {
                        location.reload();
                    } else if (obj.result == 0) {
                        document.getElementById("result-login").innerHTML = obj.errorMessage;
                    }
                }
            });
        }
        form[0].classList.add('was-validated');
    });
});

function checkRecaptchaRegisterForm(form) {
    var serializedData = $(form).serializeArray();
    var recaptcha = '';
    $.each(serializedData, function (i, field) {
        if (field.name == 'g-recaptcha-response') {
            recaptcha = field.value;
        }
    });
    if (recaptcha.length < 5) {
        $('.g-recaptcha>div').addClass('is-invalid');
        return false;
    } else {
        $('.g-recaptcha>div').removeClass('is-invalid');
    }
}

//send activation email
function sendActivationEmail(token, type) {
    document.getElementById("confirmation-result-" + type).innerHTML = '';
    var data = {
        'token': token,
        'type': type
    };
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/AuthController/sendActivationEmailPost',
        data: setAjaxData(data),
        success: function (response) {
            console.log(response);
            var obj = JSON.parse(response);
            if (obj.result == 1) {
                setTimeout(function () {
                    document.getElementById("confirmation-result-" + type).innerHTML = obj.successMessage;
                }, 500);
            } else {
                location.reload();
            }
        }
    });
}

//delete cover image
function deleteCoverImage() {
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/ProfileController/deleteCoverImagePost',
        data: setAjaxData({}),
        success: function (response) {
            location.reload();
        }
    });
}

//show image preview
function showImagePreview(input, showAsBackground) {
    var divId = $(input).attr('data-img-id');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            if (showAsBackground) {
                $('#' + divId).css('background-image', 'url(' + e.target.result + ')');
            } else {
                $('#' + divId).attr('src', e.target.result);
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}

/*
 * --------------------------------------------------------------------
 * Variation Functions
 * --------------------------------------------------------------------
 */

function selectProductVariationOption(variationId, variationType, selectedOptionId) {
    var arrayVariationValues = [];
    $('.input-product-variation').each(function () {
        var varId = $(this).attr('data-id');
        var varType = $(this).attr('data-type');
        var varOptionId = '';
        if (varType == 'radio_button') {
            varOptionId = $("input[name='variation" + varId + "']:checked").val();
        }
        if (varType == 'checkbox') {
            varOptionId = $("input[name='variation" + varId + "']:checked").val();
        }
        if (varType == 'dropdown') {
            varOptionId = $("#variation_dropdown_" + varId).val();
        }
        if (varOptionId == undefined) {
            varOptionId = '';
        }
        var item = {
            'var_id': $(this).attr('data-id'),
            'var_option_id': varOptionId
        };
        arrayVariationValues.push(item);
    });
    var data = {
        'variation_id': variationId,
        'selected_option_id': selectedOptionId,
        'variation_array': arrayVariationValues,
    };
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/select-variation-option-post',
        data: setAjaxData(data),
        success: function (response) {
            var obj = JSON.parse(response);
            if (obj.status == 1) {
                if (obj.htmlContentPrice != '') {
                    document.getElementById("product_details_price_container").innerHTML = obj.htmlContentPrice;
                }
                if (obj.htmlContentStock != '') {
                    document.getElementById("text_product_stock_status").innerHTML = obj.htmlContentStock;
                    if (obj.stockStatus == 0) {
                        $(".btn-product-cart").attr("disabled", true);
                    } else {
                        $(".btn-product-cart").attr("disabled", false);
                    }
                }
                if (obj.htmlContentSlider != '') {
                    $('#product_slider').slick('unslick');
                    $('#product_thumbnails_slider').slick('unslick');
                    document.getElementById("product_slider_container").innerHTML = obj.htmlContentSlider;
                    $('#product_slider').slick({
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        speed: 300,
                        arrows: true,
                        fade: true,
                        infinite: false,
                        swipeToSlide: true,
                        cssEase: 'linear',
                        lazyLoad: 'progressive',
                        prevArrow: $('#product-slider-nav .prev'),
                        nextArrow: $('#product-slider-nav .next'),
                        asNavFor: '#product_thumbnails_slider'
                    });
                    $('#product_thumbnails_slider').slick({
                        slidesToShow: 7,
                        slidesToScroll: 1,
                        speed: 300,
                        focusOnSelect: true,
                        arrows: false,
                        infinite: false,
                        vertical: true,
                        centerMode: false,
                        arrows: true,
                        cssEase: 'linear',
                        lazyLoad: 'progressive',
                        prevArrow: $('#product-thumbnails-slider-nav .prev'),
                        nextArrow: $('#product-thumbnails-slider-nav .next'),
                        asNavFor: '#product_slider'
                    });
                }
            }
            if (variationType == 'dropdown') {
                getSubVariationOptions(variationId, selectedOptionId);
            }
        }
    });
}

function getSubVariationOptions(variationId, selectedOptionId) {
    var data = {
        'variation_id': variationId,
        'selected_option_id': selectedOptionId,
    };
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/get-sub-variation-options',
        data: setAjaxData(data),
        success: function (response) {
            var obj = JSON.parse(response);
            if (obj.status == 1) {
                if (selectedOptionId == '') {
                    document.getElementById("variation_dropdown_" + obj.subVariationId).innerHTML = '';
                } else {
                    document.getElementById("variation_dropdown_" + obj.subVariationId).innerHTML = obj.htmlContent;
                }
            }
        }
    });
}

/*
 * --------------------------------------------------------------------
 * Number Spinner Functions
 * --------------------------------------------------------------------
 */

//number spinner
$(document).on('click', '.product-add-to-cart-container .number-spinner button', function () {
    update_number_spinner($(this));
});

function update_number_spinner(btn) {
    var btn = btn,
        oldValue = btn.closest('.number-spinner').find('input').val().trim(),
        newVal = 0;
    if (btn.attr('data-dir') == 'up') {
        newVal = parseInt(oldValue) + 1;
    } else {
        if (oldValue > 1) {
            newVal = parseInt(oldValue) - 1;
        } else {
            newVal = 1;
        }
    }
    btn.closest('.number-spinner').find('input').val(newVal);
}

$(document).on("input keyup paste change", ".number-spinner input", function () {
    var val = $(this).val();
    val = val.replace(",", "");
    val = val.replace(".", "");
    if (!$.isNumeric(val)) {
        val = 1;
    }
    if (isNaN(val)) {
        val = 1;
    }
    $(this).val(val);
});

$(document).on("input paste change", ".cart-item-quantity .number-spinner input", function () {
    var data = {
        'product_id': $(this).attr('data-product-id'),
        'cart_item_id': $(this).attr('data-cart-item-id'),
        'quantity': $(this).val(),
    };
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/update-cart-product-quantity',
        data: setAjaxData(data),
        success: function (response) {
            location.reload();
        }
    });
});

$(document).on("click", ".cart-item-quantity .btn-spinner-minus", function () {
    update_number_spinner($(this));
    var cart_id = $(this).attr("data-cart-item-id");
    if ($("#q-" + cart_id).val() != 0) {
        $("#q-" + cart_id).change();
    }
});

$(document).on("click", ".cart-item-quantity .btn-spinner-plus", function () {
    update_number_spinner($(this));
    var cart_id = $(this).attr("data-cart-item-id");
    $("#q-" + cart_id).change();
});

function removeCartDiscountCoupon() {
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/CartController/removeCartDiscountCoupon',
        data: setAjaxData({}),
        success: function (response) {
            location.reload();
        }
    });
}

/*
 * --------------------------------------------------------------------
 * Review Functions
 * --------------------------------------------------------------------
 */

$(document).on('click', '.rate-product .rating-stars label', function () {
    $('.rate-product  .rating-stars label i').removeClass("icon-star");
    $('.rate-product  .rating-stars label i').addClass("icon-star-o");
    var selected_star = $(this).attr("data-star");
    $('.rate-product  .rating-stars label').each(function () {
        var star = $(this).attr("data-star");
        if (star <= selected_star) {
            $(this).find('i').removeClass("icon-star-o");
            $(this).find('i').addClass("icon-star");
        }
    });
});

$(document).on('click', '.rate-product .label-star-open-modal', function () {
    var product_id = $(this).attr("data-product-id");
    var rate = $(this).attr("data-star");
    $("#rateProductModal #review_product_id").val(product_id);
    $("#rateProductModal #user_rating").val(rate);
});

$(document).on('click', '.btn-add-review', function () {
    var product_id = $(this).attr("data-product-id");
    $("#rateProductModal #review_product_id").val(product_id);
});

/*
 * --------------------------------------------------------------------
 * Product Comment Functions
 * --------------------------------------------------------------------
 */

$(document).ready(function () {
    //add comment
    $("#form_add_comment").submit(function (event) {
        event.preventDefault();
        var isLoggedIn = true;
        var isValid = true;
        var formSerialized = $("#form_add_comment").serializeArray();
        objectSerialized = {};
        $(formSerialized).each(function (i, field) {
            objectSerialized[field.name] = field.value;
            if (field.name == "g-recaptcha-response") {
                gRecaptcha = field.value;
            }
        });
        if ($("#form_add_comment").find("#comment_name").length > 0) {
            isLoggedIn = false;
        }
        if (isLoggedIn == false) {
            if (strLenght(objectSerialized.name) < 1) {
                $('#comment_name').addClass("is-invalid");
                isValid = false;
            } else {
                $('#comment_name').removeClass("is-invalid");
            }
            if (strLenght(objectSerialized.email) < 1) {
                $('#comment_email').addClass("is-invalid");
                isValid = false;
            } else {
                $('#comment_email').removeClass("is-invalid");
            }
            if (MdsConfig.isRecaptchaEnabled == true && isLoggedIn == false) {
                if (gRecaptcha == '') {
                    $("#form_add_comment .g-recaptcha").addClass("is-recaptcha-invalid");
                    isValid = false;
                } else {
                    $("#form_add_comment .g-recaptcha").removeClass("is-recaptcha-invalid");
                }
            }
        }
        if (strLenght(objectSerialized.comment) < 1) {
            $('#comment_text').addClass("is-invalid");
            isValid = false;
        } else {
            $('#comment_text').removeClass("is-invalid");
        }
        if (!isValid) {
            return false;
        }
        formSerialized.push({name: 'limit', value: parseInt($("#product_comment_limit").val())});
        formSerialized = setSerializedData(formSerialized);
        $.ajax({
            type: 'POST',
            url: MdsConfig.baseURL + '/AjaxController/addComment',
            data: formSerialized,
            success: function (response) {
                if (MdsConfig.isRecaptchaEnabled == true && isLoggedIn == false) {
                    grecaptcha.reset();
                }
                $("#form_add_comment")[0].reset();
                var obj = JSON.parse(response);
                if (obj.type == 'message') {
                    document.getElementById("message-comment-result").innerHTML = obj.htmlContent;
                } else {
                    document.getElementById("comment-result").innerHTML = obj.htmlContent;
                }
            }
        });
    });
});

//add subcomment
$(document).on('click', '.btn-submit-subcomment', function () {
    var commentId = $(this).attr("data-comment-id");
    var data = {};
    $("#form_add_subcomment_" + commentId).ajaxSubmit({
        beforeSubmit: function () {
            var isLoggedIn = true;
            var isValid = true;
            var gRecaptcha = '';
            if ($("#form_add_subcomment_" + commentId).find(".form-comment-name").length > 0) {
                isLoggedIn = false;
            }
            var formSerialized = $("#form_add_subcomment_" + commentId).serializeArray();
            objectSerialized = {};
            $(formSerialized).each(function (i, field) {
                objectSerialized[field.name] = field.value;
                if (field.name == 'g-recaptcha-response') {
                    gRecaptcha = field.value;
                }
            });
            if (isLoggedIn == false) {
                if (objectSerialized.name.length < 1) {
                    $(".form-comment-name").addClass("is-invalid");
                    isValid = false;
                } else {
                    $(".form-comment-name").removeClass("is-invalid");
                }
                if (objectSerialized.email.length < 1 || !isEmail(objectSerialized.email)) {
                    $(".form-comment-email").addClass("is-invalid");
                    isValid = false;
                } else {
                    $(".form-comment-email").removeClass("is-invalid");
                }
                if (MdsConfig.isRecaptchaEnabled == true) {
                    if (gRecaptcha == '') {
                        $("#form_add_subcomment_" + commentId + ' .g-recaptcha').addClass("is-recaptcha-invalid");
                        isValid = false;
                    } else {
                        $("#form_add_subcomment_" + commentId + ' .g-recaptcha').removeClass("is-recaptcha-invalid");
                    }
                }
            }
            if (objectSerialized.comment.length < 1) {
                $(".form-comment-text").addClass("is-invalid");
                isValid = false;
            } else {
                $(".form-comment-text").removeClass("is-invalid");
            }
            if (isValid == false) {
                return false;
            }
        },
        type: 'POST',
        url: MdsConfig.baseURL + '/AjaxController/addComment',
        data: setAjaxData(data),
        success: function (response) {
            var obj = JSON.parse(response);
            if (obj.type == 'message') {
                document.getElementById("sub_comment_form_" + commentId).innerHTML = obj.htmlContent;
            } else {
                document.getElementById("comment-result").innerHTML = obj.htmlContent;
            }
        }
    });
});

//load more comment
function loadMoreComments(productId) {
    var limit = parseInt($("#product_comment_limit").val());
    var data = {
        'product_id': productId,
        'limit': limit
    };
    $("#load_comment_spinner").show();
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/AjaxController/loadMoreComments',
        data: setAjaxData(data),
        success: function (response) {
            var obj = JSON.parse(response);
            if (obj.type == 'comments') {
                setTimeout(function () {
                    $("#load_comment_spinner").hide();
                    document.getElementById("comment-result").innerHTML = obj.htmlContent;
                    $("#product_comment_limit").val(obj.commentLimit);
                }, 500);
            }
        }
    });
}

//validate email
function isEmail(email) {
    var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (!regex.test(email)) {
        return false;
    } else {
        return true;
    }
}

//get string lenght
function strLenght(str) {
    if (str == '' || str == null) {
        return 0;
    }
    str = str.trim();
    return str.length;
}

//delete comment
function deleteComment(commentId, productId, message) {
    swal(swalOptions(message)).then(function (isConfirm) {
        if (isConfirm) {
            var limit = parseInt($("#product_comment_limit").val());
            var data = {
                'id': commentId,
                'product_id': productId,
                'limit': limit
            };
            $.ajax({
                type: 'POST',
                url: MdsConfig.baseURL + '/AjaxController/deleteComment',
                data: setAjaxData(data),
                success: function (response) {
                    var obj = JSON.parse(response);
                    if (obj.type == 'comments') {
                        document.getElementById("comment-result").innerHTML = obj.htmlContent;
                    }
                }
            });
        }
    });
}

//show comment box
function showCommentForm(commentId) {
    $('.visible-sub-comment').empty();
    var limit = parseInt($("#product_comment_limit").val());
    var data = {
        'comment_id': commentId,
        'limit': limit,
    };
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/AjaxController/loadSubCommentForm',
        data: setAjaxData(data),
        success: function (response) {
            var obj = JSON.parse(response);
            if (obj.type == 'form') {
                $('#sub_comment_form_' + commentId).append(obj.htmlContent);
            }
        }
    });
}

/*
 * --------------------------------------------------------------------
 * Blog Comments Functions
 * --------------------------------------------------------------------
 */

$(document).ready(function () {
    //add comment
    $("#form_add_blog_comment").submit(function (event) {
        event.preventDefault();
        var isLoggedIn = true;
        var isValid = true;
        var formSerialized = $("#form_add_blog_comment").serializeArray();
        objectSerialized = {};
        $(formSerialized).each(function (i, field) {
            objectSerialized[field.name] = field.value;
            if (field.name == 'g-recaptcha-response') {
                gRecaptcha = field.value;
            }
        });
        if ($("#form_add_blog_comment").find("#comment_name").length > 0) {
            isLoggedIn = false;
        }
        if (isLoggedIn == false) {
            if (strLenght(objectSerialized.name) < 1) {
                $('#comment_name').addClass('is-invalid');
                isValid = false;
            } else {
                $('#comment_name').removeClass('is-invalid');
            }
            if (strLenght(objectSerialized.email) < 1) {
                $('#comment_email').addClass('is-invalid');
                isValid = false;
            } else {
                $('#comment_email').removeClass('is-invalid');
            }
            if (MdsConfig.isRecaptchaEnabled == true && isLoggedIn == false) {
                if (gRecaptcha == '') {
                    $('#form_add_blog_comment .g-recaptcha').addClass('is-recaptcha-invalid');
                    isValid = false;
                } else {
                    $('#form_add_blog_comment .g-recaptcha').removeClass('is-recaptcha-invalid');
                }
            }
        }
        if (strLenght(objectSerialized.comment) < 1) {
            $('#comment_text').addClass('is-invalid');
            isValid = false;
        } else {
            $('#comment_text').removeClass('is-invalid');
        }
        if (!isValid) {
            return false;
        }
        formSerialized.push({name: 'limit', value: parseInt($("#blog_comment_limit").val())});
        formSerialized = setSerializedData(formSerialized);
        $.ajax({
            type: 'POST',
            url: MdsConfig.baseURL + '/AjaxController/addBlogComment',
            data: formSerialized,
            success: function (response) {
                if (MdsConfig.isRecaptchaEnabled == true && isLoggedIn == false) {
                    grecaptcha.reset();
                }
                $("#form_add_blog_comment")[0].reset();
                var obj = JSON.parse(response);
                if (obj.type == 'message') {
                    document.getElementById("message-comment-result").innerHTML = obj.htmlContent;
                } else {
                    document.getElementById("comment-result").innerHTML = obj.htmlContent;
                }
            }
        });
    });
});

//load more blog comment
function loadMoreBlogComments(postId) {
    var limit = parseInt($("#blog_comment_limit").val());
    var data = {
        'post_id': postId,
        'limit': limit
    };
    $("#load_comment_spinner").show();
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/AjaxController/loadMoreBlogComments',
        data: setAjaxData(data),
        success: function (response) {
            var obj = JSON.parse(response);
            if (obj.type == 'comments') {
                setTimeout(function () {
                    $("#load_comment_spinner").hide();
                    document.getElementById("comment-result").innerHTML = obj.htmlContent;
                }, 500);
            }
        }
    });
}

//delete blog comment
function deleteBlogComment(commentId, postId, message) {
    swal(swalOptions(message)).then(function (isConfirm) {
        if (isConfirm) {
            var limit = parseInt($("#blog_comment_limit").val());
            var data = {
                'comment_id': commentId,
                'post_id': postId,
                'limit': limit
            };
            $.ajax({
                type: 'POST',
                url: MdsConfig.baseURL + '/AjaxController/deleteBlogComment',
                data: setAjaxData(data),
                success: function (response) {
                    var obj = JSON.parse(response);
                    if (obj.type == 'comments') {
                        document.getElementById("comment-result").innerHTML = obj.htmlContent;
                    }
                }
            });
        }
    });
}

/*
 * --------------------------------------------------------------------
 * Message Functions
 * --------------------------------------------------------------------
 */

//delete conversation
function deleteConversation(conversationId, message) {
    swal(swalOptions(message)).then(function (isConfirm) {
        if (isConfirm) {
            var data = {
                'conversation_id': conversationId,
            };
            $.ajax({
                type: 'POST',
                url: MdsConfig.baseURL + '/HomeController/deleteConversationPost',
                data: setAjaxData(data),
                success: function (response) {
                    location.reload();
                }
            });
        }
    });
}

/*
 * --------------------------------------------------------------------
 * Cart Functions
 * --------------------------------------------------------------------
 */

$("#form_add_cart").submit(function (event) {
    var form = $(this);
    if (form[0].checkValidity() === false) {
        event.preventDefault();
        event.stopPropagation();
        setTimeout(function () {
            validateVariations('form_add_cart');
        }, 50);
    } else {
        event.preventDefault();
        $('#form_add_cart .btn-product-cart').prop('disabled', true);
        $('#form_add_cart .btn-product-cart .btn-cart-icon').html('<span class="spinner-border spinner-border-add-cart"></span>');
        var serializedData = form.serializeArray();
        serializedData.push({name: 'is_ajax', value: 1});
        serializedData = setSerializedData(serializedData);
        $.ajax({
            type: 'POST',
            url: MdsConfig.baseURL + '/add-to-cart',
            data: serializedData,
            success: function (response) {
                var obj = JSON.parse(response);
                if (obj.result == 1) {
                    setTimeout(function () {
                        $('#form_add_cart .btn-product-cart').html('<i class="icon-check"></i>' + MdsConfig.textAddedtoCart);
                        $('.span_cart_product_count').html(obj.productCount);
                        $('.span_cart_product_count').removeClass('visibility-hidden');
                        $('.span_cart_product_count').addClass('visibility-visible');
                    }, 400);
                    setTimeout(function () {
                        $('#form_add_cart .btn-product-cart').html('<span class="btn-cart-icon"><i class="icon-cart-solid"></i></span>' + MdsConfig.textAddtoCart);
                        $('#form_add_cart .btn-product-cart').prop('disabled', false);
                    }, 1000);
                }
            }
        });
    }
    form[0].classList.add('was-validated');
});

$(document).on('click', '.btn-item-add-to-cart', function () {
    var productId = $(this).attr("data-product-id");
    var buttonId = $(this).attr("data-id");
    document.getElementById("btn_add_cart_" + buttonId).innerHTML = '<div class="spinner-border spinner-border-add-cart-list"></div>';
    var data = {
        'product_id': productId,
        'is_ajax': true
    };
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/CartController/addToCart',
        data: setAjaxData(data),
        success: function (response) {
            var obj = JSON.parse(response);
            if (obj.result == 1) {
                setTimeout(function () {
                    $('#btn_add_cart_' + buttonId).css('background-color', 'rgb(40, 167, 69, .7)');
                    document.getElementById("btn_add_cart_" + buttonId).innerHTML =
                        '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">\n' +
                        '<path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>\n' +
                        '</svg>';
                    $('.span_cart_product_count').html(obj.productCount);
                    $('.span_cart_product_count').removeClass('visibility-hidden');
                    $('.span_cart_product_count').addClass('visibility-visible');
                }, 400);
                setTimeout(function () {
                    $('#btn_add_cart_' + buttonId).css('background-color', 'rgba(255, 255, 255, .7)');
                    document.getElementById("btn_add_cart_" + buttonId).innerHTML = '<i class="icon-cart"></i>';
                }, 2000);
            }
        }
    });
});

//remove from cart
function removeFromCart(cartItemId) {
    var data = {
        'cart_item_id': cartItemId
    };
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/CartController/removeFromCart',
        data: setAjaxData(data),
        success: function (response) {
            location.reload();
        }
    });
};

function validateVariations(formId) {
    $('#' + formId + ' .custom-control-variation input').each(function () {
        if ($(this).hasClass('error')) {
            var id = $(this).attr('id');
            $('#' + formId + ' .custom-control-variation label').each(function () {
                if ($(this).attr('for') == id) {
                    $(this).addClass('is-invalid');
                }
            });
        } else {
            var id = $(this).attr('id');
            $('#' + formId + ' .custom-control-variation label').each(function () {
                if ($(this).attr('for') == id) {
                    $(this).removeClass('is-invalid');
                }
            });
        }
    });
}

$(document).ready(function () {
    $('#use_same_address_for_billing').change(function () {
        if ($(this).is(":checked")) {
            $('.cart-form-billing-address').hide();
            $('.cart-form-billing-address select').removeClass('select2-req');
        } else {
            $('.cart-form-billing-address').show();
            $('.cart-form-billing-address select').addClass('select2-req');
        }
    });
});

//approve order product
function approveOrderProduct(id, message) {
    swal(swalOptions(message)).then(function (isConfirm) {
        if (isConfirm) {
            var data = {
                'order_product_id': id,
            };
            $.ajax({
                type: 'POST',
                url: MdsConfig.baseURL + '/OrderController/approveOrderProductPost',
                data: setAjaxData(data),
                success: function (response) {
                    location.reload();
                }
            });
        }
    });
};

//cancel order
function cancelOrder(id, message) {
    swal(swalOptions(message)).then(function (isConfirm) {
        if (isConfirm) {
            var data = {
                'order_id': id
            };
            $.ajax({
                type: 'POST',
                url: MdsConfig.baseURL + '/OrderController/cancelOrderPost',
                data: setAjaxData(data),
                success: function (response) {
                    location.reload();
                }
            });
        }
    });
};

//get shipping methods by location
function getShippingMethodsByLocation(stateId) {
    $('#cart_shipping_methods_container').hide();
    $('.cart-shipping-loader').show();
    var data = {
        'state_id': stateId,
    };
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/CartController/getShippingMethodsByLocation',
        data: setAjaxData(data),
        success: function (response) {
            var obj = JSON.parse(response);
            if (obj.result == 1) {
                document.getElementById("cart_shipping_methods_container").innerHTML = obj.htmlContent;
                setTimeout(function () {
                    $('#cart_shipping_methods_container').show();
                    $('.cart-shipping-loader').hide();
                }, 400);
            }
        }
    });
};

/*
 * --------------------------------------------------------------------
 * Location Functions
 * --------------------------------------------------------------------
 */

//search location
$(document).on("input paste click", "#input_location", function () {
    var inputValue = $(this).val();
    if (inputValue.length > 0) {
        $('.btn-reset-location-input').show();
    } else {
        $('#location_id_inputs input').val('');
        $('.btn-reset-location-input').hide();
    }
    if (inputValue.length < 2) {
        $('#response_search_location').hide();
        return false;
    }
    var data = {
        'input_value': inputValue,
    };
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/AjaxController/searchLocation',
        data: setAjaxData(data),
        success: function (response) {
            var obj = JSON.parse(response);
            if (obj.result == 1) {
                document.getElementById("response_search_location").innerHTML = obj.response;
                $('#response_search_location').show();
            }
            //search text
            $('#response_search_location ul li a').wrapInTag({
                words: [inputValue]
            });
        }
    });
});

$.fn.wrapInTag = function (opts) {
    function getText(obj) {
        return obj.textContent ? obj.textContent : obj.innerText;
    }

    var tag = opts.tag || 'strong',
        words = opts.words || [],
        regex = RegExp(words.join('|'), 'gi'),
        replacement = '<' + tag + '>$&</' + tag + '>';
    $(this).contents().each(function () {
        if (this.nodeType === 3) {
            $(this).replaceWith(getText(this).replace(regex, replacement));
        } else if (!opts.ignoreChildNodes) {
            $(this).wrapInTag(opts);
        }
    });
};

//set location
$(document).on("click", "#response_search_location ul li a", function () {
    $('#input_location').val($(this).text());
    var country_id = $(this).attr('data-country');
    var state_id = $(this).attr('data-state');
    var city_id = $(this).attr('data-city');
    $('#location_id_inputs').empty();
    if (country_id != null && country_id != 0) {
        $('#location_id_inputs').append("<input type='hidden' value='" + country_id + "' name='country' class='input-location-filter'>");
    }
    if (state_id != null && state_id != 0) {
        $('#location_id_inputs').append("<input type='hidden' value='" + state_id + "' name='state' class='input-location-filter'>");
    }
    if (city_id != null && city_id != 0) {
        $('#location_id_inputs').append("<input type='hidden' value='" + city_id + "' name='city' class='input-location-filter'>");
    }
});

$(document).on('click', '#btn_submit_location', function () {
    var data = {
        'country_id': $("#location_id_inputs input[name='country']").val(),
        'state_id': $("#location_id_inputs input[name='state']").val(),
        'city_id': $("#location_id_inputs input[name='city']").val()
    };
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/AjaxController/setDefaultLocation',
        data: setAjaxData(data),
        success: function (response) {
            location.reload();
        }
    });
});

$(document).on('click', '.btn-reset-location-input', function () {
    $('#input_location').val('');
    $('#location_id_inputs input').val('');
    $(this).hide();
});

$(document).on('click', function (e) {
    if ($(e.target).closest(".filter-location").length === 0) {
        $("#response_search_location").hide();
    }
});

/*
 * --------------------------------------------------------------------
 * Abuse Reports
 * --------------------------------------------------------------------
 */

//report product
$("#form_report_product").submit(function (event) {
    event.preventDefault();
    reportAbuse("form_report_product", "product");
});

//report seller
$("#form_report_seller").submit(function (event) {
    event.preventDefault();
    reportAbuse("form_report_seller", "seller");
});

//report review
$("#form_report_review").submit(function (event) {
    event.preventDefault();
    reportAbuse("form_report_review", "review");
});

//report comment
$("#form_report_comment").submit(function (event) {
    event.preventDefault();
    reportAbuse("form_report_comment", "comment");
});

function reportAbuse(form_id, item_type) {
    var formSerialized = $("#" + form_id).serializeArray();
    formSerialized.push({name: "item_type", value: item_type});
    formSerialized = setSerializedData(formSerialized);
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/AjaxController/reportAbusePost',
        data: formSerialized,
        success: function (response) {
            var obj = JSON.parse(response);
            if (obj.message != '') {
                document.getElementById("response_" + form_id).innerHTML = obj.message;
                $("#" + form_id)[0].reset();
            }
        }
    });
}

if ($(".profile-cover-image")[0]) {
    document.addEventListener('lazybeforeunveil', function (e) {
        var bg = e.target.getAttribute('data-bg-cover');
        if (bg) {
            e.target.style.backgroundImage = 'url(' + bg + ')';
        }
    });
}

/*
 * --------------------------------------------------------------------
 * Other Functions
 * --------------------------------------------------------------------
 */

$(function () {
    $(".search-select a").click(function () {
        $(".search-select .btn").text($(this).text());
        $(".search-select .btn").val($(this).text());
        $("#input_search_category").val($(this).attr("data-value"));
        $("#input_search_category_mobile").val($(this).attr("data-value"));
        $(".search-results-ajax").hide();
    });
});

//AJAX search
$(document).on("input", "#input_search", function () {
    var category = $('#input_search_category').val();
    var inputValue = $(this).val();
    if (category && inputValue) {
        searchProducts(category, inputValue, 'desktop');
    }
});

$(document).on("input", "#input_search_mobile", function () {
    var category = $('#input_search_category_mobile').val();
    var inputValue = $(this).val();
    if (category && inputValue) {
        searchProducts(category, inputValue, 'mobile');
    }
});

function searchProducts(category, inputValue, device) {
    var contentId = 'response_search_results';
    if (device == 'mobile') {
        contentId = contentId + '_mobile';
    }
    if (inputValue.length < 2) {
        $('#' + contentId).hide();
        return false;
    }
    var data = {
        'category': category,
        'input_value': inputValue,
        'lang_base_url': MdsConfig.langBaseURL
    };
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/AjaxController/ajaxSearch',
        data: setAjaxData(data),
        success: function (response) {
            var obj = JSON.parse(response);
            if (obj.result == 1) {
                document.getElementById(contentId).innerHTML = obj.response;
                $('.search-results-product').overlayScrollbars({});
                $('#' + contentId).show();
            }
            //search text
            $('#' + contentId + ' ul li a').wrapInTag({
                words: [inputValue]
            });
        }
    });
}

$(document).on('click', function (e) {
    if ($(e.target).closest(".top-search-bar").length === 0) {
        $("#response_search_results").hide();
    }
});

//search product filters
$(document).on("change keyup paste", ".filter-search-input", function () {
    var filter_id = $(this).attr('data-filter-id');
    var input = $(this).val().toLowerCase();
    var list_items = $("#" + filter_id + " li");
    list_items.each(function (idx, li) {
        var text = $(this).find('label').text().toLowerCase();
        if (text.indexOf(input) > -1) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
});

$(document).on("click", "#btn_filter_price", function () {
    var price_min = $('#price_min').val();
    var price_max = $('#price_max').val();
    var page = $(this).attr('data-page');
    if (price_min != "" || price_max != "") {
        var query_string = $(this).attr('data-query-string');
        var current_url = $(this).attr('data-current-url');
        var params = "";
        if (price_min != "") {
            params = "p_min=" + price_min;
            if (price_max != "") {
                params += "&p_max=" + price_max;
            }
        } else {
            if (price_max != "") {
                params = "p_max=" + price_max;
            }
        }
        if (query_string == "") {
            query_string = "?" + params;
        } else {
            query_string = query_string + "&" + params;
        }
        if (page == "profile") {
            query_string = query_string + "#products";
        }
        window.location.replace(current_url + query_string);
    }
});

$(document).on("change", "#select_sort_items", function () {
    var val = $(this).val();
    var query_string = $(this).attr('data-query-string');
    var current_url = $(this).attr('data-current-url');
    var page = $(this).attr('data-page');
    if (val == "most_recent" || val == "lowest_price" || val == "highest_price" || val == "rating") {
        var params = "sort=" + val;
        if (query_string == "") {
            query_string = "?" + params;
        } else {
            query_string = query_string + "&" + params;
        }
    }
    if (page == "profile") {
        query_string = query_string + "#products";
    }
    window.location.replace(current_url + query_string);
});

$(document).on("click", "#btn_search_vendor", function () {
    var val = $('#input_search_vendor').val();
    if (val != "") {
        var query_string = $(this).attr('data-query-string');
        var current_url = $(this).attr('data-current-url');
        var params = "search=" + val;
        if (query_string == "") {
            query_string = "?" + params;
        } else {
            query_string = query_string + "&" + params;
        }
        window.location.replace(current_url + query_string + "#products");
    }
});

$("#input_search_vendor").keyup(function (event) {
    if (event.keyCode === 13) {
        $("#btn_search_vendor").click();
    }
});

//load more posts
function loadMorePromotedProducts() {
    $("#load_promoted_spinner").show();
    var data = {
        'offset': parseInt($("#promoted_products_offset").val())
    };
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/AjaxController/loadMorePromotedProducts',
        data: setAjaxData(data),
        success: function (response) {
            var obj = JSON.parse(response);
            if (obj.result == 1) {
                setTimeout(function () {
                    $("#promoted_products_offset").val(obj.offset);
                    $("#row_promoted_products").append(obj.htmlContent);
                    $("#load_promoted_spinner").hide();
                    if (obj.hideButton) {
                        $(".promoted-load-more-container").hide();
                    }
                }, 300);
            } else {
                setTimeout(function () {
                    $("#load_promoted_spinner").hide();
                    if (obj.hideButton) {
                        $(".promoted-load-more-container").hide();
                    }
                }, 300);
            }
        }
    });
}

//send message
$("#form_send_message").submit(function (event) {
    event.preventDefault();
    var message_subject = $('#message_subject').val();
    var message_text = $('#message_text').val();
    var message_receiver_id = $('#message_receiver_id').val();
    var message_send_em = $('#message_send_em').val();

    if (message_subject.length < 1) {
        $('#message_subject').addClass("is-invalid");
        return false;
    } else {
        $('#message_subject').removeClass("is-invalid");
    }
    if (message_text.length < 1) {
        $('#message_text').addClass("is-invalid");
        return false;
    } else {
        $('#message_text').removeClass("is-invalid");
    }
    var $form = $(this);
    var $inputs = $form.find("input, select, button, textarea");
    var serializedData = $form.serializeArray();
    serializedData = setSerializedData(serializedData);
    $inputs.prop('disabled', true);
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/HomeController/addConversation',
        data: serializedData,
        success: function (response) {
            $inputs.prop("disabled", false);
            var obj = JSON.parse(response);
            if (obj.result == 1) {
                document.getElementById("send-message-result").innerHTML = obj.htmlContent;
                $("#form_send_message")[0].reset();
            }
        }
    });
});

function getStates(val, idSuffix = '') {
    if (idSuffix != '') {
        idSuffix = '_' + idSuffix;
    }
    $('#select_states' + idSuffix).children('option').remove();
    $('#get_states_container' + idSuffix).hide();
    if ($('#select_cities' + idSuffix).length) {
        $('#select_cities' + idSuffix).children('option').remove();
        $('#get_cities_container' + idSuffix).hide();
    }
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
                document.getElementById("select_states" + idSuffix).innerHTML = obj.content;
                $('#get_states_container' + idSuffix).show();
            } else {
                document.getElementById("select_states" + idSuffix).innerHTML = '';
                $('#get_states_container' + idSuffix).hide();
            }
        }
    });
}

function getCities(val) {
    var data = {
        'state_id': val
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

$(document).on('click', '.btn-add-remove-wishlist', function () {
    var productId = $(this).attr("data-product-id");
    var dataType = $(this).attr("data-type");
    if (dataType == 'list') {
        if ($(this).find("i").hasClass("icon-heart-o")) {
            $(this).find("i").removeClass("icon-heart-o");
            $(this).find("i").addClass("icon-heart");
        } else {
            $(this).find("i").removeClass("icon-heart");
            $(this).find("i").addClass("icon-heart-o");
        }
    }
    if (dataType == 'details') {
        if ($(this).find("i").hasClass("icon-heart-o")) {
            $('.product-add-to-cart-container .btn-add-remove-wishlist').html('<i class="icon-heart"></i><span>' + MdsConfig.textRemoveFromWishlist + '</span>');
        } else {
            $('.product-add-to-cart-container .btn-add-remove-wishlist').html('<i class="icon-heart-o"></i><span>' + MdsConfig.textAddtoWishlist + '</span>');
        }
    }
    var data = {
        'product_id': productId,
    };
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/add-remove-wishlist-post',
        data: setAjaxData(data),
        success: function (response) {
        }
    });
});

$("#form_validate").submit(function () {
    $('.custom-control-validate-input').removeClass('custom-control-validate-error');
    setTimeout(function () {
        $('.custom-control-validate-input .error').each(function () {
            var name = $(this).attr('name');
            if ($(this).is(":visible")) {
                name = name.replace('[]', '');
                $('.label_validate_' + name).addClass('custom-control-validate-error');
            }
        });
    }, 100);
});

$('.custom-control-validate-input input').click(function () {
    var name = $(this).attr('name');
    name = name.replace('[]', '');
    $('.label_validate_' + name).removeClass('custom-control-validate-error');
});

//hide cookies warning
function hideCookiesWarning() {
    $(".cookies-warning").hide();
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/AjaxController/hideCookiesWarning',
        data: setAjaxData({}),
        success: function (response) {
        }
    });
}

$(document).ready(function () {
    if ($(".validate-form").length > 0) {
        $('.validate-form').each(function (i, obj) {
            var id = $(this).attr('id');
            $("#" + id).validate();
        });
    }
});

//validate select2
$(".validate-form").submit(function () {
    $('.select2-req').each(function (i, obj) {
        var id = $(this).attr('id');
        var val = $(this).val();
        if (val == "" || val == null || val == undefined) {
            $('.select2-selection[aria-controls="select2-' + id + '-container"]').addClass('error');
        } else {
            $('.select2-selection[aria-controls="select2-' + id + '-container"]').removeClass('error');
        }
    });
});

$(document).on('change', '.select2-req', function () {
    var id = $(this).attr('id');
    if ($('.select2-selection[aria-controls="select2-' + id + '-container"]').hasClass("error")) {
        $('.select2-selection[aria-controls="select2-' + id + '-container"]').removeClass('error');
    }
});

function checkStateSelected(id) {
    var val = $('#' + id).val();
    if (!val) {
        $("[aria-controls='select2-" + id + "-container']").addClass('error');
    } else {
        $("[aria-controls='select2-" + id + "-container']").removeClass('error');
    }
}

$('#input_vendor_files').on('change', function (e) {
    $('#label_vendor_files').html("");
    var files = $(this).prop('files');
    for (var i = 0; i < files.length; i++) {
        var item = "<span class='badge badge-secondary'>" + files[i].name + "</span><br>";
        $('#container_vendor_files').append(item);
    }
});

$("#form_validate").validate();
$("#form_validate_search").validate();
$("#form_validate_search_mobile").validate();
$("#form_validate_newsletter").validate();
$("#form_add_cart").validate();
$("#form_request_quote").validate();
$("#form_validate_checkout").validate();

$("#form_request_quote").submit(function (event) {
    setTimeout(function () {
        validateVariations('form_request_quote');
    }, 50);
});

$(document).on('click', '.custom-control-variation input', function () {
    var name = $(this).attr('name');
    $('.custom-control-variation label').each(function () {
        if ($(this).attr('data-input-name') == name) {
            $(this).removeClass('is-invalid');
        }
    });
});

$(document).ready(function () {
    $('.validate_terms').submit(function (e) {
        $('.custom-control-validate-input p').remove();
        if (!$('.custom-control-validate-input input').is(":checked")) {
            e.preventDefault();
            $('.custom-control-validate-input').addClass('custom-control-validate-error');
            $('.custom-control-validate-input').append("<p class='text-danger'>" + MdsConfig.textAcceptTerms + "</p>");
        } else {
            $('.custom-control-validate-input').removeClass('custom-control-validate-error');
        }
    });
});

$(document).on("input keyup paste change", ".validate_price .price-input", function () {
    var val = $(this).val();
    val = val.replace(',', '.');
    if ($.isNumeric(val) && val != 0) {
        $(this).removeClass('is-invalid');
    } else {
        $(this).addClass('is-invalid');
    }
});

$(document).ready(function () {
    $('.validate_price').submit(function (e) {
        $('.validate_price .validate-price-input').each(function () {
            var val = $(this).val();
            if (val != '') {
                val = val.replace(',', '.');
                if ($.isNumeric(val) && val != 0) {
                    $(this).removeClass('is-invalid');
                } else {
                    e.preventDefault();
                    $(this).addClass('is-invalid');
                    $(this).focus();
                }
            }
        });
    });
});

$(document).on("input keyup paste change keypress", ".price-input", function () {
    if (typeof mds_config.thousands_separator == 'undefined') {
        mds_config.thousands_separator = '.';
    }
    if (mds_config.thousands_separator == '.') {
        var $this = $(this);
        if ((event.which != 46 || $this.val().indexOf('.') != -1) &&
            ((event.which < 48 || event.which > 57) &&
                (event.which != 0 && event.which != 8))) {
            event.preventDefault();
        }
        var text = $(this).val();
        if ((text.indexOf('.') != -1) &&
            (text.substring(text.indexOf('.')).length > 2) &&
            (event.which != 0 && event.which != 8) &&
            ($(this)[0].selectionStart >= text.length - 2)) {
            event.preventDefault();
        }
    } else {
        var $this = $(this);
        if ((event.which != 44 || $this.val().indexOf(',') != -1) &&
            ((event.which < 48 || event.which > 57) &&
                (event.which != 0 && event.which != 8))) {
            event.preventDefault();
        }
        var text = $(this).val();
        if ((text.indexOf(',') != -1) &&
            (text.substring(text.indexOf(',')).length > 2) &&
            (event.which != 0 && event.which != 8) &&
            ($(this)[0].selectionStart >= text.length - 2)) {
            event.preventDefault();
        }
    }
});

//full screen
$(document).ready(function () {
    $("iframe").attr("allowfullscreen", "")
});

//delete quote request
function deleteQuoteRequest(id, message) {
    swal(swalOptions(message)).then(function (isConfirm) {
        if (isConfirm) {
            var data = {
                'id': id,
            };
            $.ajax({
                type: 'POST',
                url: MdsConfig.baseURL + '/OrderController/deleteQuoteRequest',
                data: setAjaxData(data),
                success: function (response) {
                    location.reload();
                }
            });
        }
    });
}

function getProductShippingCost(val, productId) {
    $("#product_shipping_cost_container").empty();
    $(".product-shipping-loader").show();
    var data = {
        'state_id': val,
        'product_id': productId
    };
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/AjaxController/getProductShippingCost',
        data: setAjaxData(data),
        success: function (response) {
            var obj = JSON.parse(response);
            if (obj.result == 1) {
                setTimeout(function () {
                    document.getElementById("product_shipping_cost_container").innerHTML = obj.response;
                    $(".product-shipping-loader").hide();
                }, 300);
            }
        }
    });
}

function deleteShippingAddress(id, message) {
    swal(swalOptions(message)).then(function (isConfirm) {
        if (isConfirm) {
            var data = {
                'id': id
            };
            $.ajax({
                type: 'POST',
                url: MdsConfig.baseURL + '/ProfileController/deleteShippingAddressPost',
                data: setAjaxData(data),
                success: function (response) {
                    location.reload();
                }
            });
        }
    });
}

//delete attachment
function deleteSupportAttachment(id) {
    var data = {
        'id': id,
        'ticket_type': 'client'
    };
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/SupportController/deleteSupportAttachmentPost',
        data: setAjaxData(data),
        success: function (response) {
            var obj = JSON.parse(response);
            if (obj.result == 1) {
                document.getElementById("response_uploaded_files").innerHTML = obj.response;
            }
        }
    });
}

//close support ticket
function closeSupportTicket(id) {
    var data = {
        'id': id,
    };
    $.ajax({
        type: 'POST',
        url: MdsConfig.baseURL + '/close-ticket-post',
        data: setAjaxData(data),
        success: function (response) {
            location.reload();
        }
    });
}

$(document).ready(function () {
    $(".form-newsletter").submit(function (event) {
        event.preventDefault();
        var form_id = $(this).attr('id');
        var input = "#" + form_id + " .newsletter-input";
        var email = $(input).val().trim();
        if (email == "") {
            $(input).addClass('has-error');
            return false;
        } else {
            $(input).removeClass('has-error');
        }
        var serializedData = $(this).serializeArray();
        serializedData = setSerializedData(serializedData);
        $.ajax({
            type: 'POST',
            url: MdsConfig.baseURL + '/AjaxController/addToNewsletter',
            data: serializedData,
            success: function (response) {
                var obj = JSON.parse(response);
                if (obj.result == 1) {
                    if (form_id == "form_newsletter_footer") {
                        document.getElementById("form_newsletter_response").innerHTML = obj.response;
                    } else {
                        document.getElementById("modal_newsletter_response").innerHTML = obj.response;
                    }
                    $(input).val('');
                }
            }
        });
    });
});

$(document).on("change", ".input-show-selected", function () {
    var id = $(this).attr("data-id");
    var val = $(this).val();
    $("#" + id).html(val.replace(/.*[\/\\]/, ''));
});

if ($('.fb-comments').length > 0) {
    $(".fb-comments").attr("data-href", window.location.href);
}

if ($('.post-text-responsive').length > 0) {
    $(function () {
        $('.post-text-responsive iframe').wrap('<div class="embed-responsive embed-responsive-16by9"></div>');
        $('.post-text-responsive iframe').addClass('embed-responsive-item');
    });
}

//load product shop location map
function loadProductShopLocationMap() {
    var address = $("#span_shop_location_address").text();
    document.getElementById("iframe_shop_location_address").setAttribute("src", "https://maps.google.com/maps?width=100%&height=600&hl=en&q=" + address + "&ie=UTF8&t=&z=8&iwloc=B&output=embed&disableDefaultUI=true");
}

//player modal preview
$('#productVideoModal').on('hidden.bs.modal', function (e) {
    $(this).find('video')[0].pause();
});

$('#productAudioModal').on('hidden.bs.modal', function (e) {
    Amplitude.pause();
});

//payment completed circle
$(document).ready(function () {
    $('.circle-loader').toggleClass('load-complete');
    $('.checkmark').toggle();
});

$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();
});
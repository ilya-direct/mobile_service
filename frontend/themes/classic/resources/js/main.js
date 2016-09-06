$(document).ready(function(){
    $('.bxslider').bxSlider({
        pager: true,
        adaptiveHeight: true,
        speed: 800
    });
    $('.bxslider2').bxSlider({
        pager: false,
        adaptiveHeight: true
    });

    /* Отправка нижней формы */
    $('#footer-callback-form').submit(function () {
        var formData = $(this).serialize();
        $.ajax({
            url : '/footer-form/',
            method : 'post',
            data: formData,
            dataType : 'json',
            success : function(data) {
                if (data.success) {
                    h2 = $('.bottom .feedback h2')[0];
                    $(h2).hide();
                    $('#footer-callback-form').hide();
                    $('#footer-callback-form-success').text(data.msg);
                    $('#footer-callback-form-success').show();
                } else if(data.validation_failed) {
                    $('#footer-callback-form').yiiActiveForm('updateMessages', data.errors);
                } else {
                    alert(data.msg);
                }
            }
        });
        return false;
    });
});
$(function($) {
    $.mask.definitions['~']='[+-]';
    $('#phone').mask('+7-999-999-99-99');
});
$(document).ready(function(){
    var $menu = $("#wr_main_menu");
    $(window).scroll(function(){
        if ( $(this).scrollTop() > 100 && $menu.hasClass("default") ){
            $menu.removeClass("default").addClass("fixed");
        } else if($(this).scrollTop() <= 100 && $menu.hasClass("fixed")) {
            $menu.removeClass("fixed").addClass("default");
        }
    });//scroll
});
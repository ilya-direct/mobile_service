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
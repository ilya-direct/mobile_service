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
    $('#footer-callback-form').on('beforeSubmit', function (e) {
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

// Функция скрола по хещ-тегу с offset из-за фиксированного меню
$(function() {
    $('a[href*="#"]:not([href="#"])').click(function() {
        if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
            if (target.length) {
                var scroll = target.offset().top - 70;
                scroll = ($(window).scrollTop() < 110) ? scroll - 35 : scroll;
                $('html, body').animate({
                    scrollTop: scroll
                }, 1000);
                return false;
            }
        }
    });
});

// Функция отправки ActiveForm Ajax-запросом
var activeFormAjax = function (event) {
    event.preventDefault();
    var form = $(this);
    // Предотвращение double-click
    if (form.data('requestRunning')) {
        return false;
    }
    form.data('requestRunning', true);

    $.post(form.attr('action'), form.serialize(), function(errors) {
        form.yiiActiveForm('updateMessages', errors);
        form.data('requestRunning', false);
    });

    return false;
};

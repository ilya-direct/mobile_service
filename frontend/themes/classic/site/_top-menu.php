<?php

use yii\helpers\Url;
use frontend\assets\AppAsset;

/**
 * @var \yii\web\View $this
 *
 */
$baseUrl = AppAsset::register($this)->baseUrl;
?>
<nav id="menu" class="navbar navbar-default" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button"
                    class="navbar-toggle collapsed"
                    data-toggle="collapse"
                    data-target="#sub-menu"
                    aria-expanded="false">
                <span class="sr-only"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>

        <div class="collapse navbar-collapse" id="sub-menu">
            <ul class="nav navbar-nav devices">
                <li>
                    <a href="<?= Url::to(['site/device', 'alias' => 'computer']); ?>">
                        <img src="<?= $baseUrl; ?>/icons/computer_icon.png">
                        Компьютеры
                    </a>
                </li>
                <li>
                    <a href="<?= Url::to(['site/category', 'alias' => 'noutbuki']); ?>">
                        <img src="<?= $baseUrl; ?>/icons/notebook_icon.png">
                        Ноутбуки
                    </a>
                </li>
                <li>
                    <a href="<?= Url::to(['site/category', 'alias' => 'telefony']); ?>">
                        <img src="<?= $baseUrl; ?>/icons/phone_icon.png">
                        Телефоны
                    </a>
                </li>
                <li>
                    <a href="<?= Url::to(['site/category', 'alias' => 'plansety']); ?>">
                        <img src="<?= $baseUrl; ?>/icons/tablet_icon.png">
                        Планшеты
                    </a>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a class="discounts" href="<?= Url::to(['site/discounts']); ?>">Скидки</a></li>
                <li><a href="#">О нас</a></li>
                <li><a href="<?= Url::to(['site/contacts']); ?>">Контакты</a></li>
            </ul>
        </div>
    </div>
</nav>
<?php
$this->registerJs(<<<JS
$(document).ready(function () {

    var menu = $('#menu');
    var origOffsetY = menu.offset().top;
    function scroll() {
        if ($(window).scrollTop() >= origOffsetY) {
            menu.addClass('navbar-fixed-top');
        } else {
            menu.removeClass('navbar-fixed-top');
        }
    }
    document.onscroll = scroll;
});
JS
);

$this->registerCss(<<<CSS
#menu {
    margin-bottom: 0;
    background-color: #27292c;
    border-radius: 0;
    border: 0;
}

#menu .container-fluid {
    padding-left: 0;
}

#menu .nav > li {
     position: static;
 }

#menu .dropdown-menu {
    width: 100%;
}

#menu ul li {
    display: inline-block;
    vertical-align: top;
    /*padding: 0 15px 0 15px;*/
    border-right: 1px solid #636363;
    margin-left: -5px;
}

#menu .navbar-nav a {
    color: #fff;
    font-family: robotoregular;
    text-transform: uppercase;
}
#menu .navbar-right a {
    text-transform: none;
}

#menu ul li a img {
    height: 20px;
    margin-right: 5px;
}

#menu .discounts:after {
    content: '';
    display: block;
    position: absolute;
    top: 10px;
    right: 5px;
    width: 13px;
    height: 13px;
    background: url({$baseUrl}/images/discounts.png) no-repeat;
}

#menu li:hover {
    background-color: #00C962;
    border-right: 1px solid #00C962;
}

@media (max-width: 900px) {
    .navbar-header {
        float: none;
    }
    .navbar-left,.navbar-right {
        float: none !important;
    }
    .navbar-toggle {
        display: block;
    }
    .navbar-collapse {
        border-top: 1px solid transparent;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.1);
    }
    .navbar-fixed-top {
        top: 0;
        border-width: 0 0 1px;
    }
    .navbar-collapse.collapse {
        display: none!important;
    }
    .navbar-nav {
        float: none!important;
        margin-top: 7.5px;
    }
    .navbar-nav>li {
        float: none;
    }
    .navbar-nav>li>a {
        padding-top: 10px;
        padding-bottom: 10px;
    }
    .collapse.in{
        display:block !important;
    }

    #menu ul li {
        display: block;
        padding: 0 15px 0 15px;
        border: 0;
        margin-left: -5px;
    }
}
CSS
);

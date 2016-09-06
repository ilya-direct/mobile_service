<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\MaskedInput;
use common\models\ar\Order;
use common\models\ar\OrderPerson;
use common\widgets\Alert;
use frontend\assets\AppAsset;
use frontend\models\FooterCallbackForm;

/* @var $this \yii\web\View */
/* @var $content string */
$bundle = AppAsset::register($this);
$baseUrl = $bundle->baseUrl;

/** @var FooterCallbackForm $callbackModel Нижняя форма заказа звонка */
$callbackModel = isset($this->params['footerCallbackForm'])
    ? $this->params['footerCallbackForm']
    : new FooterCallbackForm();

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <?= Html::csrfMetaTags(); ?>
    <title><?= Html::encode($this->title); ?></title>
    <link rel="stylesheet" href="<?= $baseUrl; ?>/css/reset-min.css">
    <?php $this->head(); ?>
</head>
<body>
<?php $this->beginBody() ?>
<?= Alert::widget() ?>
<header>
    <div class="row">
        <div class = "col-xs-12 col-sm-6 col-md-4 col-lg-4">
            <div class="logo">
                <a href="<?= Url::to(['site/index']); ?>">
                    <h1>BMSTU <span>Сервис</span></h1>
                    <p>ремонт портативной техники</p>
                </a>
            </div>
        </div>
        <div class = "col-xs-12 col-sm-6 col-md-4 col-lg-5">
            <?php if (empty($this->params['hideTopModalForm'])): ?>
                <?php
                $modalWindow = \yii\bootstrap\Modal::begin([
                    'header' => '<h2>Оформление заявки </h2>',
                    'toggleButton' => [
                        'tag' => 'div',
                        'label' => '<a>оформить заявку</a>',
                        'class' => 'oform_order',
                    ],
                ]); ?>
                <?= $this->render('//site/quick-order-form', [
                        'order' => new Order(),
                        'orderPerson' => new OrderPerson(),
                ]); ?>
                <?php $modalWindow->end(); ?>
            <?php else: ?>
                <div class="oform_order">
                    <a href="#full-form">оформить заявку</a>
                </div>
            <?php endif; ?>
        </div>
        <div class = "col-xs-12 col-sm-3 col-md-4 col-lg-3 hidden-sm hidden-xs">
            <div class="hd_ttle">
                <p>ст. Бауманская</p>
                <div></div>
                <h3>+7 (963) 656-83-77</h3>
                <div></div>
                <span> пн-пт: 8:00 - 20:00  cб,вс: 8:00 -17:00</span>
            </div>
        </div>
    </div>
</header>
<nav class="navbar navbar-default" role="navigation">
    <div class="wr_main_menu default" id="wr_main_menu">
        <div class="main_menu">
            <div class = "row">
                <div class = "col-xs-12 col-sm-8 col-md-8 col-lg-7 hidden-sm hidden-xs">
                    <ul class="menu">
                        <li>
                            <a href="#" class="icons_1" data-jq-dropdown="#jq-dropdown1">Ноутбуки</a>
                            <ul id="jq-dropdown1" class="submenu jq-dropdown jq-dropdown-tip">
                                <li>
                                    <a href="#" class="main_name">asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                </li>
                                <li>
                                    <a href="#" class="main_name">asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                </li>
                                <li>
                                    <a href="#" class="main_name">asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                </li>
                                <li>
                                    <a href="#" class="main_name">asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" class="icons_2" data-jq-dropdown="#jq-dropdown2">Телефоны</a>
                            <ul id="jq-dropdown2" class="submenu jq-dropdown jq-dropdown-tip">
                                <li>
                                    <a href="#" class="main_name">asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                </li>
                                <li>
                                    <a href="#" class="main_name">asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                </li>
                                <li>
                                    <a href="#" class="main_name">asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                </li>
                                <li>
                                    <a href="#" class="main_name">asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" class="icons_3" data-jq-dropdown="#jq-dropdown3">Планшеты</a>
                            <ul id="jq-dropdown3" class="submenu jq-dropdown jq-dropdown-tip">
                                <li>
                                    <a href="#" class="main_name">asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                </li>
                                <li>
                                    <a href="#" class="main_name">asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                </li>
                                <li>
                                    <a href="#" class="main_name">asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                </li>
                                <li>
                                    <a href="#" class="main_name">asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" class="icons_4" data-jq-dropdown="#jq-dropdown4">APPLE</a>
                            <ul id="jq-dropdown4" class="submenu jq-dropdown jq-dropdown-tip">
                                <li>
                                    <a href="#" class="main_name">asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                </li>
                                <li>
                                    <a href="#" class="main_name">asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                </li>
                                <li>
                                    <a href="#" class="main_name">asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                </li>
                                <li>
                                    <a href="#" class="main_name">asus</a>
                                    <a href="#" class="name">Ноутбук asus</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class = "col-xs-12 col-sm-6 col-md-4 col-lg-2 hidden-md hidden-sm hidden-xs">
                    <!--<div class="status">
                        <a href="#">Статус заявки</a>
                    </div>-->
                </div>
                <div class = "col-xs-12 col-sm-12 col-md-4 col-lg-3">
                    <div class="navbar-header hidden-lg hidden-md">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>
                    <ul class="mini_menu hidden-sm hidden-xs">
                        <li>
                            <a href="<?= Url::to(['site/discounts']); ?>" class="discounts">Скидки</a>
                        </li>
                        <li>
                            <a href="<?= Url::to(['site/about-us']); ?>">О нас</a>
                        </li>
                        <li>
                            <a href="<?= Url::to(['site/contacts']); ?>">Контакты</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- Меню для мобильных устройств -->
        <div class="mob_menu">
            <div class="container-fluid">
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Ноутбуки <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#">ASUS</a>
                                    <ul class="dropmenu">
                                        <li>
                                            <a href="#" class="name">Ноутбук asus</a>
                                            <a href="#" class="name">Ноутбук asus</a>
                                            <a href="#" class="name">Ноутбук asus</a>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="#">ASUS</a>
                                    <ul class="dropmenu">
                                        <li>
                                            <a href="#" class="name">Ноутбук asus</a>
                                            <a href="#" class="name">Ноутбук asus</a>
                                            <a href="#" class="name">Ноутбук asus</a>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="#">ASUS</a>
                                    <ul class="dropmenu">
                                        <li>
                                            <a href="#" class="name">Ноутбук asus</a>
                                            <a href="#" class="name">Ноутбук asus</a>
                                            <a href="#" class="name">Ноутбук asus</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Телефоны <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#">ASUS</a>
                                    <ul class="dropmenu">
                                        <li>
                                            <a href="#" class="name">Ноутбук asus</a>
                                            <a href="#" class="name">Ноутбук asus</a>
                                            <a href="#" class="name">Ноутбук asus</a>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="#">ASUS</a>
                                    <ul class="dropmenu">
                                        <li>
                                            <a href="#" class="name">Ноутбук asus</a>
                                            <a href="#" class="name">Ноутбук asus</a>
                                            <a href="#" class="name">Ноутбук asus</a>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="#">ASUS</a>
                                    <ul class="dropmenu">
                                        <li>
                                            <a href="#" class="name">Ноутбук asus</a>
                                            <a href="#" class="name">Ноутбук asus</a>
                                            <a href="#" class="name">Ноутбук asus</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Планшеты <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#">ASUS</a>
                                    <ul class="dropmenu">
                                        <li>
                                            <a href="#" class="name">Ноутбук asus</a>
                                            <a href="#" class="name">Ноутбук asus</a>
                                            <a href="#" class="name">Ноутбук asus</a>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="#">ASUS</a>
                                    <ul class="dropmenu">
                                        <li>
                                            <a href="#" class="name">Ноутбук asus</a>
                                            <a href="#" class="name">Ноутбук asus</a>
                                            <a href="#" class="name">Ноутбук asus</a>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="#">ASUS</a>
                                    <ul class="dropmenu">
                                        <li>
                                            <a href="#" class="name">Ноутбук asus</a>
                                            <a href="#" class="name">Ноутбук asus</a>
                                            <a href="#" class="name">Ноутбук asus</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">APPLE <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#">ASUS</a>
                                    <ul class="dropmenu">
                                        <li>
                                            <a href="#" class="name">Ноутбук asus</a>
                                            <a href="#" class="name">Ноутбук asus</a>
                                            <a href="#" class="name">Ноутбук asus</a>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="#">ASUS</a>
                                    <ul class="dropmenu">
                                        <li>
                                            <a href="#" class="name">Ноутбук asus</a>
                                            <a href="#" class="name">Ноутбук asus</a>
                                            <a href="#" class="name">Ноутбук asus</a>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="#">ASUS</a>
                                    <ul class="dropmenu">
                                        <li>
                                            <a href="#" class="name">Ноутбук asus</a>
                                            <a href="#" class="name">Ноутбук asus</a>
                                            <a href="#" class="name">Ноутбук asus</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- Меню для мобильных устройств -->
</nav>
<?= $content ?>
<div class="wr_bottom">
    <div class="bottom">
        <div class = "row">
            <div class = "col-xs-12 col-sm-6 col-md-4 col-lg-4">
                <div class="about">
                    <div class="border"></div>
                    <h2>О НАС</h2>
                    <ul>
                        <li>
                            <a href="<?= Url::to(['site/working-order']); ?>">Как мы работаем?</a>
                        </li>
                        <li>
                            <a href="<?= Url::to(['site/courier']); ?>">Выезд мастера и курьера</a>
                        </li>
                        <li>
                            <a href="<?= Url::to(['site/guarantees']); ?>">Гарантии</a>
                        </li>
                        <li>
                            <a href="<?= Url::to(['site/blog']); ?>">Наш блог</a>
                        </li>
                        <li>
                            <a href="<?= Url::to(['site/faq']); ?>">Вопросы и ответы</a>
                        </li>
                        <li>
                            <a href="<?= Url::to(['site/feedback']); ?>">Отзывы</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class = "col-xs-12 col-sm-6 col-md-4 col-lg-4">
                <div class="contacts">
                    <div class="border br2"></div>
                    <h2>Контакты</h2>
                    <ul>
                        <li>
                            <p class="phone">+7 (963) 656 83 77</p>
                        </li>
                        <li>
                            <p class="email">ilya-direct@ya.ru</p>
                        </li>
                        <li>
                            <p class="adress">г. Москва, ул. Фридриха Энгельса, д. 22</p>
                        </li>
                        <li>
                            <p class="work">пн-пт: 10:00 - 20:00 сб-вс: 10:00 - 18:00</p>
                        </li>
                    </ul>
                </div>
            </div>
            <div class = "col-xs-12 col-sm-12 col-md-4 col-lg-4">
                <div class="feedback">
                    <h2>Оставьте заявку на звонок</h2>
                    <?php $form = ActiveForm::begin(['action' => Url::to(['site/footer-callback-form']), 'id' => 'footer-callback-form']); ?>
                    <?= $form->field($callbackModel, 'first_name'); ?>
                    <?= $form->field($callbackModel, 'phone')->widget(MaskedInput::className(),
                        ['mask' => '+7 (999) 999-99-99']); ?>
                    <?= Html::submitButton('Оставить заявку'); ?>
                    <?php $form->end(); ?>
                    <div id="footer-callback-form-success" class="well" style="font: 13px/1.4 robotoregular;font-size: 1.5em;display: none;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<footer>
    <div class="footer">
        <div class="ft_column_left">
            <p>&copy; 2013 BMSTU Сервис</p>
        </div>
        <div class="ft_column_right">
            <ul>
                <li>
                    <a href="#" class="socicon-vkontakte"></a>
                </li>
                <li>
                    <a href="#" class="socicon-facebook"></a>
                </li>
                <li>
                    <a href="#" class="socicon-youtube"></a>
                </li>
                <li>
                    <a href="#" class="socicon-instagram"></a>
                </li>
            </ul>
        </div>
    </div>
</footer>
<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>

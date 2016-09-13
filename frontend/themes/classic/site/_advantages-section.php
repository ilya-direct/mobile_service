<?php

use frontend\assets\AppAsset;

/**
 * @var \yii\web\View $this
 */
$baseUrl = AppAsset::register($this)->baseUrl;
?>
<div class="advantages">
    <div class = "row">
        <div class = "col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="advantages_block">
                <div class = "row">
                    <div class = "col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <div class="advantages_img">
                            <img src="<?= $baseUrl; ?>/images/advantage-replacement.jpg" alt="" />
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                        <div class="advantages_title">
                            <h3>Только качественные запчасти!</h3>
                            <p>Работаем только с качественными запчастями, установка которых гарантирует 100% работоспособность.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class = "col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="advantages_block">
                <div class = "row">
                    <div class = "col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <div class="advantages_img">
                            <img src="<?= $baseUrl; ?>/images/advantage-speed.jpg"/>
                        </div>
                    </div>
                    <div class = "col-xs-12 col-sm-10 col-md-10 col-lg-10">
                        <div class="advantages_title">
                            <h3>Мы быстрее всех!</h3>
                            <p>Мы понимаем, как иногда важно бывает побыстрее вернуть аппарат в рабочее состояние и поэтому ведём статистику по времени обработки всех заявок.
                                Средняя скорость ремонта всего 1 день.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class = "col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="advantages_block">
                <div class = "row">
                    <div class = "col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <div class="advantages_img">
                            <img src="<?= $baseUrl; ?>/images/advantage-courier.jpg" alt="" />
                        </div>
                    </div>
                    <div class = "col-xs-12 col-sm-10 col-md-10 col-lg-10">
                        <div class="advantages_title">
                            <h3>Бесплатный выезд мастера!</h3>
                            <p>Если Вам неудобно приезжать к нам, мы приедем к Вам. А при возможности произведём ремонт на месте.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class = "col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="advantages_block">
                <div class = "row">
                    <div class = "col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <div class="advantages_img">
                            <img src="<?= $baseUrl; ?>/images/advantage-wifi.jpg"/>
                        </div>
                    </div>
                    <div class = "col-xs-12 col-sm-10 col-md-10 col-lg-10">
                        <div class="advantages_title">
                            <h3>У нас есть Wi-Fi!</h3>
                            <p>
                                Пока мастер приводит ваше устройство в рабочее состояние,
                                Вы можете пользоваться безлимитным интернетом. Чай, кофе так же к Вашим услугам :).
                                И всё это абсолютно бесплатно.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php

$this->registerCss(<<<CSS
.advantages {
    max-width: 1140px;
    margin: 0 auto;
}

.advantages_block {
    margin-toP: 45px;
}

.advantages_title h3 {
    margin-top: 5px;
    color: #131313;
    font: 16px/1.1 robotoregular;
}

.advantages_title p {
    color: #64686D;
    font: 13px/1.4 robotoregular;
    margin-toP: 12px;
}
@media screen and (max-width: 768px) {
    .advantages_title, .advantages_img {
        text-align: center;
    }

    .advantages_img {
        margin-bottom: 10px;
    }
}
CSS
);

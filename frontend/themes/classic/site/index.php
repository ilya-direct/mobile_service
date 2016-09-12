<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use common\models\ar\DeviceCategory;
use common\models\ar\Vendor;

/**
 * @var yii\web\View $this
 * @var \frontend\models\PriceCalculatorForm $model
 */
$this->title = 'Сервис по ремонту портативной техники';

$asset = $this->registerAssetBundle(\frontend\assets\AppAsset::className());

$baseUrl = $asset->baseUrl;

?>
<div class="slider">
    <div class="slider_form visible-lg" style="padding: 10px;background: rgba(51, 51, 51, 0.8);font: 13px/1.4 robotoregular;top:5%">
        <h2 style="font-size: 1.5em;color: white;margin-bottom: 15px"> Калькулятор стоимости услуг</h2>
        <?php $form = ActiveForm::begin(); ?>
        <div style="color: red;text-align: center;margin-bottom: 10px;">
            <?= \yii\bootstrap\Html::error($model, 'db'); ?>
        </div>
        <?= $form->field($model, 'device_category_id')
            ->dropDownList(DeviceCategory::getRootList(), [
                'prompt' => 'Выберете тип устройства...',
            ])
            ->label(false); ?>
        <?= $form->field($model, 'vendor_id')
            ->dropDownList(Vendor::getList(['enabled' => true]), ['prompt' => 'Выберете производителя...'])
            ->label(false); ?>
        <?= $form->field($model, 'device_id')
            ->dropDownList(\common\models\ar\Device::getList(['enabled' => true]), ['prompt' => 'Выберете устройство...'])
            ->label(false); ?>
        <?= $form->field($model, 'service_id')
            ->dropDownList(\common\models\ar\Service::getList(['enabled' => true]), ['prompt' => 'Выберете услугу...'])
            ->label(false); ?>
        <div class="row">
            <div class="col-xs-6" style="padding: 5px">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Имя *'])->label(false); ?>
            </div>
            <div class="col-xs-6" style="padding: 5px">
                <?= $form->field($model, 'phone')->widget(\yii\widgets\MaskedInput::className(),
                    ['mask' => '+7 (999) 999-99-99'])->textInput(['placeholder' => 'Телефон *'])->label(false); ?>
            </div>
        </div>
        <?= \yii\helpers\Html::submitButton('Рассчитать'); ?>
        <?php $form->end(); ?>
    </div>
    <ul class="bxslider">
        <li>
            <img src="<?= $baseUrl; ?>/images/slider_img1.jpg" alt="" />
        </li>
        <li>
            <img src="<?= $baseUrl; ?>/images/slider_img2.jpg" alt="" />
        </li>
        <li>
            <img src="<?= $baseUrl; ?>/images/slider_img3.jpg" alt="" />
        </li>
    </ul>
</div>
<?= $this->render('_advantages-section'); ?>
<div class="wr_services">
    <div class="services">
        <div class = "row">
            <div class = "col-xs-12 col-sm-12 col-md-3 col-lg-3">
                <div class="services_ttle">
                    <img src="<?= $baseUrl; ?>/images/computer_icon.jpg" alt="" />
                    <a href="<?= Url::to(['site/device', 'alias' => 'computer']); ?>">Ремонт компьютеров</a>
                    <p>Если Вам понадобился профессиональный ремонт компьютеров, обращайтесь к нам.</p>
                </div>
            </div>
            <div class = "col-xs-12 col-sm-12 col-md-3 col-lg-3">
                <div class="services_ttle">
                    <img src="<?= $baseUrl; ?>/images/services_img4.jpg" alt="" />
                    <a href="<?= Url::to(['site/device', 'alias' => 'noutbuk']); ?>">Ремонт ноутбуков</a>
                    <p>Наш сервис осуществляет ремонт ноутбуков известных производителей.</p>
                </div>
            </div>
            <div class = "col-xs-12 col-sm-12 col-md-3 col-lg-3">
                <div class="services_ttle">
                    <img src="<?= $baseUrl; ?>/images/services_img2.jpg" alt="" />
                    <a href="<?= Url::to(['site/category', 'alias' => 'telefony']); ?>">Ремонт телефонов</a>
                    <p>Так же наш сервисный центр предлагает услугу по ремонту смартфонов.</p>
                </div>
            </div>
            <div class = "col-xs-12 col-sm-12 col-md-3 col-lg-3">
                <div class="services_ttle">
                    <img src="<?= $baseUrl; ?>/images/services_img3.jpg" alt="" />
                    <a href="<?= Url::to(['site/category', 'alias' => 'plansety']); ?>">Ремонт планшетов</a>
                    <p>Наш сервисный центр рад предложить Вам профессиональный ремонт планшетов.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->render('_about-text'); ?>
<?= $this->render('_feedback'); ?>
<?= $this->render('_courier-delivery-banner'); ?>
<?= $this->render('_why-we-text'); ?>

<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\MaskedInput;
use frontend\assets\AppAsset;

/**
 * @var yii\web\View $this
 * @var \frontend\models\ContactUsForm $model
 */
$this->title = 'Контакты';
$this->params['breadcrumbs'][] = $this->title;
$bundle = AppAsset::register($this);
$baseUrl = $bundle->baseUrl;
?>
<div class="associate">
    <h2>Свяжитесь с нами</h2>
    <ul>
        <li>
            <p class="work2">пн-пт: 10:00 - 20:00 сб-вс: 10:00 - 18:00</p>
        </li>
        <li>
            <p class="phone2">
                <a href="tel:<?= Yii::$app->formatter->asPhone(Yii::$app->params['phone'], \common\components\app\Formatter::PHONE_FORMAT_PLAIN); ?>">
                    <?= Yii::$app->formatter->asPhone(Yii::$app->params['phone']); ?>
                </a>
            </p>
        </li>
        <li>
            <p class="email2"><?= Html::mailto(Yii::$app->params['email']); ?></p>
        </li>
        <li>
            <p class="adress2">г.Москва ул. Фридриха Энгельса, д. 21</p>
        </li>
    </ul>
</div>
<div class="map">
    <h2>Приезжайте к нам</h2>
    <div class="bl_mp">
        <img src="<?= $baseUrl; ?>/images/map_bg.jpg" alt="" />
    </div>
</div>
<div class="wr_report">
    <div class="report">
        <h2>Оставьте нам сообщение</h2>
        <?php $form = ActiveForm::begin([
            'id' => 'contact-us-form',
            'action' => Url::current(),
        ]); ?>
        <div class="row">
            <div class="col-xs-12" style="text-align: center">
                <?= $form->field($model, 'db')->hiddenInput()->label(false); ?>
            </div>
        </div>
        <div class="row">
            <div class = "col-xs-12 col-sm-4 col-md-4 col-lg-4">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]); ?>
            </div>
            <div class = "col-xs-12 col-sm-4 col-md-4 col-lg-4">
                <?= $form->field($model, 'phone')->widget(MaskedInput::className(), [
                    'mask' => '+7 (999) 999-99-99',
                ]); ?>
            </div>
            <div class = "col-xs-12 col-sm-4 col-md-4 col-lg-4">
                <?= $form->field($model, 'email')->textInput(['maxlength' => true]); ?>
            </div>
            <div class = "col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <?= $form->field($model, 'message')->textarea(['maxlength' => true]); ?>
            </div>
        </div>
        <?= Html::submitButton('отправить'); ?>
        <?php $form->end(); ?>
    </div>
</div>
<?= $this->render('_why-we-text'); ?>

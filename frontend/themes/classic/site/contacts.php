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
            <p class="phone2">+7 (963) 656 83 77</p>
        </li>
        <li>
            <p class="email2">ilya-direct@ya.ru</p>
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
        <?= Html::submitButton('оформить заявку'); ?>
        <?php $form->end(); ?>
    </div>
</div>
<div class="bl_ttle">
    <h2>Почему именно мы? Все очень просто!</h2>
    <p>Наш сервисный центр придерживается политики «приведи друга». По статистике каждый третий клиент рекомендует нас, либо обращаться к нам повторно. За долгое время мы наработали большую базу лояльных клиентов, что для нас очень важно, так как отпадает надобность тратить огромные средства на рекламные компании, что позволяет переложить расходы на более важные моменты, которые поднимают планку качества выполняемых нами услуг на новый уровень. Например, в нашем штате работают только квалифицированные специалисты, и каждый мастер выполняет именно ту задачу, в которой он разбирается практически идеально.</p>
    <p>Стоит так же отметить, что мы используем только качественные комплектующие, установка которых, гарантирует длительную работу отремонтированного устройства в нашем сервисе. Мы дорожим каждым клиентом, доверяйте свою технику профессионалам и она прослужит Вам еще очень долго!</p>
</div>

<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use frontend\assets\AppAsset;

/**
 * @var \yii\web\View $this
 * @var \common\models\ar\DeviceCategory $category
 * @var \common\models\ar\Vendor[] $vendors
 * @var \frontend\models\NotFoundDeviceForm $notFoundDeviceFormModel
 */
$bundle = AppAsset::register($this);
$baseUrl = $bundle->baseUrl;
$this->params['breadcrumbs'][] = ['label' => $category->name];
$this->registerCssFile($baseUrl . '/css/category.css');
$this->registerJsFile($baseUrl . '/js/category.js', ['depends' => AppAsset::className()]);
$this->title = 'Категория ' . Html::encode($category->name);
?>
<div class="category-vendors-bar">
    <ul>
    <?php foreach ($vendors as $vendor): ?>
        <li>
            <a href="#<?= $vendor->alias; ?>"><?= $vendor->name; ?></a>
        </li>
    <?php endforeach; ?>
    </ul>
</div>
<div class="category-devices-container">
    <?php foreach ($vendors as $vendor): ?>
    <div class="category-vendor-devices">
        <h2 id="<?= $vendor->alias; ?>"><?= $vendor->name; ?></h2>
        <div class = "row">
            <?php
            /** @var \common\models\ar\Device[] $devices Выбираем только 3 элемента из массива */
            $devices = array_slice($vendor->devices, 0, 3);
            ?>
            <?php foreach ($devices as $device): ?>
                <div class = "col-xs-12 col-sm-4 col-md-4 col-lg-4">
                    <div class="category-vendor-device">
                        <a href="<?= \yii\helpers\Url::to(['site/device', 'alias' => $device->alias]) ?>">
                            <?php if ($device->imageWebPath): ?>
                                <img src="<?= $device->imageWebPath; ?> " alt="" />
                            <?php endif; ?>
                            <p><?= $device->name; ?></p>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <a href="#" class="view-all">Смотреть все</a>
    </div>
    <?php endforeach; ?>
</div>
<div class="wr_report">
    <div class="device-not-found-form">
        <h2>Не нашли нужную модель?</h2>
        <h3>Позвоните по тел. <span>+7 (963) 656 83 77</span> или <span>заполните форму</span> и мы свяжемся с вами</h3>
        <?php $form = ActiveForm::begin([
            'id' => 'not-found-device-form',
            'method' => 'get',
            'action' => ['site/not-found-device'],
            'options' => [
                'class' => '',
            ],
            'fieldConfig' => [
                'options' => ['class' => 'input'],
            ],]);
        ?>
        <div class="row">
            <?= $form->field($notFoundDeviceFormModel, 'db', ['options' => ['class' => '', 'style' => 'text-align: center;']])->hiddenInput()->label(false); ?>
            <div class = "col-xs-12 col-sm-4 col-md-4 col-lg-4">
                <?= $form->field($notFoundDeviceFormModel, 'name')->textInput(['maxlength' => true]); ?>
            </div>
            <div class = "col-xs-12 col-sm-4 col-md-4 col-lg-4">
                <?= $form->field($notFoundDeviceFormModel, 'phone')->widget(\yii\widgets\MaskedInput::className(), [
                    'mask' => '+7 (999) 999-99-99',
                ]); ?>
            </div>
            <div class = "col-xs-12 col-sm-4 col-md-4 col-lg-4">
                <?= $form->field($notFoundDeviceFormModel, 'device')->textInput(['maxlength' => true]); ?>
            </div>
            <div class = "col-xs-12">
                <?= Html::submitButton('Перезвонить'); ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<div class="reviews reviews2">
    <h2>Отзывы о нас</h2>
    <div class="slider_rew">
        <ul class="bxslider2">
            <li>
                <div class="top_rew">
                    <p class="name_rew">Леонид Павлович</p>
                    <p class="date_rew">13.01.15</p>
                </div>
                <div class="ttle_rew">
                    <p>Сделали телефон за 2 дня, работой доволен работой доволенработо</p>
                </div>
            </li>
            <li>
                <div class="top_rew">
                    <p class="name_rew">Леонид Павлович</p>
                    <p class="date_rew">13.01.15</p>
                </div>
                <div class="ttle_rew">
                    <p>Сделали телефон за 2 дня, работой доволен работой доволенработо</p>
                </div>
            </li>
        </ul>
    </div>
    <div class="leave_rew">
        <a href="#">Оставить отзыв</a>
    </div>
</div>
<div class="bl_ttle">
    <h2>Почему именно мы? Все очень просто!</h2>
    <p>Наш сервисный центр придерживается политики «приведи друга». По статистике каждый третий клиент рекомендует нас, либо обращаться к нам повторно. За долгое время мы наработали большую базу лояльных клиентов, что для нас очень важно, так как отпадает надобность тратить огромные средства на рекламные компании, что позволяет переложить расходы на более важные моменты, которые поднимают планку качества выполняемых нами услуг на новый уровень. Например, в нашем штате работают только квалифицированные специалисты, и каждый мастер выполняет именно ту задачу, в которой он разбирается практически идеально.</p>
    <p>Стоит так же отметить, что мы используем только качественные комплектующие, установка которых, гарантирует длительную работу отремонтированного устройства в нашем сервисе. Мы дорожим каждым клиентом, доверяйте свою технику профессионалам и она прослужит Вам еще очень долго!</p>
</div>

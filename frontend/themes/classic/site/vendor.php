<?php

use yii\helpers\Html;
use frontend\assets\AppAsset;
use yii\helpers\Url;

/**
 * @var \yii\web\View $this
 * @var \common\models\ar\DeviceCategory $category
 * @var \common\models\ar\Vendor $vendor
 * @var \common\models\ar\Device[] $devices
 */
$bundle = AppAsset::register($this);
$baseUrl = $bundle->baseUrl;
$this->params['breadcrumbs'][] = ['label' => $vendor->name];
/* TODO: Перенести стили */
$this->registerCssFile($baseUrl . '/css/category.css', ['depends' => AppAsset::className()]);
$this->title = Html::encode($vendor->name);

?>
<div class="category-devices-container">
    <div class="category-vendor-devices">
        <h2>Выберите модель:</h2>
        <div class = "row">
            <?php foreach ($devices as $device): ?>
                <div class = "col-xs-12 col-sm-4 col-md-4 col-lg-4">
                    <div class="category-vendor-device">
                        <a href="<?= Url::to(['site/device', 'alias' => $device->alias]); ?>">
                            <?php if ($device->imageWebPath): ?>
                                <img src="<?= $device->imageWebPath; ?>" alt="<?= $device->name; ?>" />
                            <?php endif; ?>
                            <p><?= $device->name; ?></p>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?= $this->render('_not-found-device-form'); ?>
<div style="margin-top:60px"></div>
<?= $this->render('_feedback'); ?>
<?= $this->render('_why-we-text'); ?>

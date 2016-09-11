<?php

use yii\helpers\Html;
use yii\helpers\Url;
use frontend\assets\AppAsset;

/**
 * @var \yii\web\View $this
 * @var \common\models\ar\DeviceCategory $category
 * @var \common\models\ar\Vendor[] $vendors
 */
$bundle = AppAsset::register($this);
$baseUrl = $bundle->baseUrl;
$this->params['breadcrumbs'][] = ['label' => $category->name];
$this->registerCssFile($baseUrl . '/css/category.css');
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
        <?php if (count($vendor->devices) > 3): ?>
            <a href="<?= Url::to(['site/vendor',
                'categoryAlias' => $category->alias,
                'vendorAlias' => $vendor->alias,
            ]); ?>" class="view-all">Смотреть все</a>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>
<?= $this->render('_not-found-device-form'); ?>
<div style="margin-top: 60px;"></div>
<?= $this->render('_feedback'); ?>
<div class="bl_ttle">
    <h2>Почему именно мы? Все очень просто!</h2>
    <p>Наш сервисный центр придерживается политики «приведи друга». По статистике каждый третий клиент рекомендует нас, либо обращаться к нам повторно. За долгое время мы наработали большую базу лояльных клиентов, что для нас очень важно, так как отпадает надобность тратить огромные средства на рекламные компании, что позволяет переложить расходы на более важные моменты, которые поднимают планку качества выполняемых нами услуг на новый уровень. Например, в нашем штате работают только квалифицированные специалисты, и каждый мастер выполняет именно ту задачу, в которой он разбирается практически идеально.</p>
    <p>Стоит так же отметить, что мы используем только качественные комплектующие, установка которых, гарантирует длительную работу отремонтированного устройства в нашем сервисе. Мы дорожим каждым клиентом, доверяйте свою технику профессионалам и она прослужит Вам еще очень долго!</p>
</div>

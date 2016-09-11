<?php

use yii\helpers\Html;
use frontend\assets\AppAsset;
use yii\helpers\Url;
/**
 * @var $this yii\web\View
 * @var $name string
 * @var $message string
 * @var $exception Exception
 */
$baseUrl = AppAsset::register($this)->baseUrl;
$this->params['breadcrumbs'] = [];
$this->title = 'Страница не найдена';
?>
<div class="row">
    <div class="col-md-4 col-sm-3"></div>
    <div style="text-align: center" class="col-md-4 col-sm-6 error">
        <h1><?= Html::encode($this->title) ?></h1>
        <img style="width: 100%" src="<?= $baseUrl; ?>/images/page_not_found.png">
        <a style="margin-top: 15px;background:#00c962" class="btn btn-success" href="<?= Url::home(); ?>">Перейти на главную</a>
    </div>
    <div class="col-md-4 col-sm-3"></div>

</div>
<?php
$this->registerCss(<<<CSS
@media screen and (max-width: 416px){
     .error h1 {
         font-size: 1.6em;
     }
}
CSS
);
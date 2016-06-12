<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\tree\Module;
use kartik\tree\TreeView;
use rmrevin\yii\fontawesome\AssetBundle;

/**
 * @var \yii\web\View $this
 * @var \yii\db\ActiveQuery $deviceCategoryQuery
 */

$this->title = 'Категории устройств';
$this->params['breadcrumbs'][] = $this->title;
$this->registerAssetBundle(AssetBundle::className());

$mainTemplate = <<<HTML
<div class="row">
    <div class="col-sm-4">
        {wrapper}
    </div>
    <div class="col-sm-8">
        {detail}
    </div>
</div>
HTML;
?>

<h1><?= Html::encode($this->title) ?></h1>
<?= TreeView::widget([
    'query' => $deviceCategoryQuery,
    'headingOptions' => ['label' => 'Категории'],
    'fontAwesome' => true,     // optional
    'displayValue' => 1,        // initial display value
    'softDelete' => false,       // defaults to true
    'cacheSettings' => [
        'enableCache' => true   // defaults to true
    ],
    'rootOptions' => ['label'=>'<span class="text-primary"></span>'],
    'nodeActions' => [
        Module::NODE_REMOVE => Url::to(['remove']),
    ],
    'showIDAttribute' => false,
    'mainTemplate' => $mainTemplate,
    'iconEditSettings' => [
        'show' => 'none',
    ]
]) ?>

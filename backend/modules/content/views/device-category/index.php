<?php

use kartik\tree\TreeView;

/**
 * @var \yii\web\View $this
 * @var \yii\db\ActiveQuery $deviceCategoryQuery
 */
$this->title = 'Категории устройств';
$this->registerAssetBundle(\rmrevin\yii\fontawesome\AssetBundle::className());
?>

<?= TreeView::widget([
    // single query fetch to render the tree
    // use the Product model you have in the previous step
    'query' => $deviceCategoryQuery,
    'headingOptions' => ['label' => 'Категории устройств'],
    'fontAwesome' => true,     // optional
    'displayValue' => 1,        // initial display value
    'softDelete' => false,       // defaults to true
    'cacheSettings' => [
        'enableCache' => true   // defaults to true
    ],

    'rootOptions' => ['label'=>'<span class="text-primary">Корень</span>'],
    'iconEditSettings'=> [
        'show' => 'list',
        'listData' => [
            'folder' => 'Folder',
            'file' => 'File',
            'mobile' => 'Phone',
            'bell' => 'Bell',
        ]
    ],
]) ?>

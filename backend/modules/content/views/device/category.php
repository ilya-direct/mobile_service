<?php

use common\models\ar\DeviceCategory;


echo \kartik\tree\TreeView::widget([
    'query' =>DeviceCategory::find()->addOrderBy('tree, lft'),
    'headingOptions' => ['label' => 'Categories'],
    'rootOptions' => ['label'=>'<span class="text-primary">Root</span>'],
    'defaultChildNodeIcon'=> '<i class="fa fa-folder"></i>',
    'fontAwesome' => true,
    'isAdmin' => true,
    'iconEditSettings'=> [
        'show' => 'list',
        'listData' => [
            'folder' => 'Folder',
            'file' => 'File',
            'mobile' => 'Phone',
            'bell' => 'Bell',
        ]
    ],
    'softDelete' => true,
    'cacheSettings' => ['enableCache' => true]
]);
<?php

namespace backend\modules\content;

use Yii;

/**
 * Content module definition class
 */
class Content extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'backend\modules\content\controllers';

    public function init()
    {
        parent::init();
        Yii::$app->view->params['breadcrumbs'][] = ['label' => 'Контент', 'url' => ['/content']];
    }
}

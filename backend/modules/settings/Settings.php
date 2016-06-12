<?php

namespace backend\modules\settings;

use Yii;
/**
 * settings module definition class
 */
class Settings extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'backend\modules\settings\controllers';

    public function init()
    {
        parent::init();
        Yii::$app->view->params['breadcrumbs'][] = ['label' => 'Настройки', 'url' => ['/settings']];
    }
}

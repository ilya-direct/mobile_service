<?php

namespace backend\modules\content\controllers;

use Yii;
use yii\web\Controller;
use common\models\ar\DeviceCategory;

/**
 * Дерево категорий для устройств
 */
class DeviceCategoryController extends Controller
{

    public function actionIndex()
    {
        /*if (!DeviceCategory::find()->where(['name' => 'Мобильные телефоны'])->exists()) {
            $node = new DeviceCategory(['name' => 'Мобильные телефоны']);
            $node->makeRoot();
        } else {
            /** @var DeviceCategory $node * /
            $node = DeviceCategory::findOne(['name' => 'iPhone']);
            $samsung = new DeviceCategory(['name' => 'iPhone 5C']);
            $samsung->prependTo($node);
        }*/

        $node = DeviceCategory::findOne(['name' => 'iPhone']);
        $c = $node->children()->count();
        $query = DeviceCategory::find()->orderBy(['tree' => SORT_ASC, 'lft' => SORT_ASC]);

        return $this->render('index', [
            'deviceCategoryQuery' => $query,
        ]);
    }
}

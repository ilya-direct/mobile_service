<?php

namespace backend\modules\content\controllers;

use Yii;
use yii\base\InvalidCallException;
use yii\web\Controller;
use yii\web\Response;
use common\models\ar\DeviceCategory;

/**
 * Дерево категорий для устройств
 */
class DeviceCategoryController extends Controller
{

    public function actionIndex()
    {
        $query = DeviceCategory::find()->orderBy(['tree' => SORT_ASC, 'lft' => SORT_ASC]);

        return $this->render('index', [
            'deviceCategoryQuery' => $query,
        ]);
    }

    /**
     * Remove a tree node
     */
    public function actionRemove()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!Yii::$app->request->isAjax || !Yii::$app->request->isPost) {
            throw new InvalidCallException(Yii::t('kvtree', 'This operation is not allowed.'));
        }
        extract(Yii::$app->request->post());
        /** @var DeviceCategory $node */
        $node = DeviceCategory::findOne(Yii::$app->request->post('id'));
        $success = $node && $node->deleteWithChildren();

        if ($success) {
            return ['out' => 'Элемент успешно удалён', 'status' => 'success'];
        } else {
            return ['out' => 'Не удалось удалить элемент', 'status' => 'error'];
        }
    }
}

<?php

namespace api\modules\v1\controllers;

use api\components\app\RestController;
use api\modules\v1\models\DeviceCategoryApiResult;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class DeviceCategoryController extends RestController
{
    
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => DeviceCategoryApiResult::find(),
            'sort' => [
                'attributes' => [
                    'name',
                ],
                'defaultOrder' => [
                    'name' => SORT_ASC,
                ],
            ],
        ]);
        
        return $dataProvider;
    }
    
    public function actionView($id)
    {
        $model = DeviceCategoryApiResult::findOne($id);
    
        if (!$model) {
            throw new NotFoundHttpException('The requested deviceCategory does not exist.');
        }
        
        return $model;
    }
}

<?php

namespace api\modules\v1\controllers;

use Yii;
use api\components\app\RestController;
use backend\models\OrderSearchForm;

class OrderController extends RestController
{
    
    public function actionList() {
        $searchModel = new OrderSearchForm();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    
        return $dataProvider;
    }
    
    
    public function actionCreate() {
        
    }
}

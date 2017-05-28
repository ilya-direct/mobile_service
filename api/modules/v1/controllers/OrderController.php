<?php

namespace api\modules\v1\controllers;

use Yii;
use api\components\app\RestController;
use api\modules\v1\models\OrderApiResult;
use common\components\db\ActiveQuery;
use common\models\ar\Order;
use backend\models\OrderSearchForm;

class OrderController extends RestController
{
    
    public function actionIndex() {
        $searchModel = new OrderSearchForm();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    
        return OrderApiResult::toArray($dataProvider->models);
    }
    
    
    public function actionView($id) {
    
        $model = Order::find()
            ->innerJoinWith('orderStatus')
            ->innerJoinWith('orderProvider')
            ->with([
                'orderServices' => function (ActiveQuery $query) {
                    $query->innerJoinWith('deviceAssign.device');
                    $query->innerJoinWith('deviceAssign.service');
                    $query->notDeleted();
                }])
            ->where([ Order::tableName() . '.id' => $id])
            ->notDeleted()
            ->one();
        
        return OrderApiResult::toArray($model);
    }
}

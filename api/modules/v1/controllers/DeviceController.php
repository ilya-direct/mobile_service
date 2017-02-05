<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\DeviceApiResult;
use Yii;
use api\components\app\RestController;
use common\models\ar\Device;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class DeviceController extends RestController
{
    
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => DeviceApiResult::find()->joinWith(['deviceCategory', 'vendor']),
            'pagination' => [
                'pageSizeLimit' => [1, 100000],
                'pageSize' => 100,
            ],
            'sort' => [
                'attributes' => [
                    'name' => [
                        'asc' => [
                            'enabled' => SORT_DESC,
                            'name' => SORT_ASC,
                        ],
                        'desc' => [
                            'enabled' => SORT_DESC,
                            'name' => SORT_DESC,
                        ],
                        'default' => SORT_ASC,
                    ],
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
        $model = DeviceApiResult::find()
            ->joinWith(['deviceCategory', 'vendor'])
            ->where([ Device::tableName() . '.id' => $id])
            ->one();
        if (!$model) {
            throw new NotFoundHttpException('The requested device does not exist.');
        }
        
        return $model;
    }
    
    
    public function actionCreate()
    {
        $model = new Device();
        
        $post = Yii::$app->request->bodyParams;
    
        if ($model->load($post, '') && $model->save()) {
            $response = Yii::$app->response;
            $response->setStatusCode(201);
            $response->headers->set('Location', Url::toRoute(['view', 'id' => $model->id], true));
        }
        
        return $model;
    }
    
    public function actionUploadImage($id)
    {
        $model = Device::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('The requested device_id does not exist.');
        }
        $image = UploadedFile::getInstanceByName('image');
        if (!$image) {
            throw new BadRequestHttpException('Image was not specified');
        }
        
        Yii::$app->storage->saveFileByPath($image->tempName, $image->name, Device::IMAGE_SAVE_FOLDER);
        $model->image_name = $image->name;
        $model->save(false);
        
        Yii::$app->response->setStatusCode(204);
        
        return;
    }
    
    
    public function actionUpdate($id)
    {
        $model = Device::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('The requested device does not exist.');
        }
        $post = Yii::$app->request->bodyParams;
        $model->load($post, '');
        if (!$model->save()) {
            throw new BadRequestHttpException('Wrong Validation');
        }
        Yii::$app->response->setStatusCode(204);
        
        return;
    }
    
    public function actionDelete($id)
    {
        $model = Device::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('The requested device does not exist.');
        }
        $model->delete();
        
        Yii::$app->response->setStatusCode(204);
        
        return;
    }
}
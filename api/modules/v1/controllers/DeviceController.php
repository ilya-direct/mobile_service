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
        } else {
            Yii::$app->response->setStatusCode(400, 'Wrong Validation');
            
            return $model->errors;
        }
        
        return new DeviceApiResult($model);
    }
    
    public function actionImageUpload()
    {
        $image = UploadedFile::getInstanceByName('image');
        if (!$image) {
            throw new BadRequestHttpException('Image was not specified');
        }
        $fileHash = substr(hash_file('sha256', $image->tempName), 0, 8);
        $imageName = $image->baseName . '_' . $fileHash . '.' . $image->extension;
        Yii::$app->storage->saveFileByPath($image->tempName, $imageName, Device::IMAGE_SAVE_FOLDER);
        
        return [
            'image_name' => $imageName,
            'image_url' => Yii::$app->storage->getUrl($imageName, Device::IMAGE_SAVE_FOLDER),
        ];
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
            Yii::$app->response->setStatusCode(400, 'Wrong Validation');
            
            return $model->errors;
        }
        
        return new DeviceApiResult($model);
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
<?php

namespace backend\modules\content\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use common\models\ar\Device;

/**
 * DeviceController implements the CRUD actions for Device model.
 */
class DeviceController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Device models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Device::find()->joinWith('deviceCategory'),
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

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Device model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = Device::find()
            ->joinWith(['deviceCategory'])
            ->where([ Device::tableName() . '.id' => $id])
            ->one();
        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Device model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Device();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $image = UploadedFile::getInstance($model, 'image');
            if ($image) {
                $this->saveImage($image, $model);
            }
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Device model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $image = UploadedFile::getInstance($model, 'image');
            if ($image) {
                $this->saveImage($image, $model);
            }
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Device model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Device model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Device the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Device::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Загрузка изображения устройству с удалением старых
     * @param UploadedFile $image
     * @param Device $model
     */
    protected function saveImage($image, $model)
    {
        $path = Yii::getAlias(Device::IMAGE_SAVE_PATH);
        $files = FileHelper::findFiles($path, ['filter' => function ($path) use ($model) {
            return (boolean)preg_match('/'. preg_quote($model->alias, '/') . '\.\w{3,4}$/u', $path);
        }]);
        foreach ($files as $file) {
            unlink($file);
        }
        preg_match('/\w{3,4}$/u', $image->name, $extension);
        $image->saveAs($path . '/' . $model->alias . '.' . (empty($extension) ? '' : $extension[0]));
    }
}

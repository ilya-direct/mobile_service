<?php

namespace backend\modules\content\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use common\models\ar\Device;
use common\models\ar\DeviceAssign;
use common\models\ar\Service;
use backend\modules\content\models\PriceListImportForm;


class PriceListController extends Controller
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

    public function actionIndex()
    {
        $model = new PriceListImportForm();
        if (Yii::$app->request->isPost && $model->validate()) {
            $result = $model->save();
            if ($result['success']) {
                $flashView = $this->renderPartial('flash-box', ['flashArray' => $result['updatedItems']]);
                Yii::$app->session->setFlash('success', $flashView);
            } else {
                Yii::$app->session->setFlash('error', $result['error']);
            }
        }
        return $this->render('index', [
            'model' => $model,
        ]);
    }

    /**
     * Выгрузка цен в csv
     * @return \yii\web\Response
     */
    public function actionExport()
    {
        /** @var Device[] $devices */
        $devices = Device::find()
            ->innerJoinWith('deviceAssigns.service')
            ->where([DeviceAssign::tableName() . '.enabled' => true])
            ->orderBy([
                Device::tableName() . '.name' => SORT_ASC,
                Service::tableName() . '.name' => SORT_ASC,
            ])
            ->all();
        $fileName = '/files/pricelist.csv';

        $h = fopen(Yii::getAlias('@backend/web') . $fileName, 'w+');

        fputcsv($h, ['id', 'Устройство', 'Услуга', 'Цена', 'Старая цена']);

        foreach ($devices as $device) {
            foreach ($device->deviceAssigns as $assign) {
                fputcsv($h, [$assign->id, $device->name, $assign->service->name, $assign->price, $assign->price_old]);
            }
        }
        fclose($h);

        return $this->redirect($fileName);
    }
}

<?php

namespace backend\modules\content\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use backend\modules\content\models\PriceListImportForm;


class PriceListImportController extends Controller
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
}

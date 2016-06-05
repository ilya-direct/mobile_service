<?php

namespace backend\modules\settings\controllers;

use common\models\ResetPasswordForm;
use Yii;
use backend\models\ar\Admin;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use DateTime;

/**
 * AdminController implements the CRUD actions for Admin model.
 */
class AdminController extends Controller
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
     * Lists all Admin models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Admin::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Admin model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Admin model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Admin();
        $model->scenario = Admin::SCENARIO_CREATE;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->created_at = $model->updated_at = DateTime::createFromFormat(DateTime::W3C, time());
            $model->generatePasswordResetToken();
            $model->generateAuthKey();
            $model->setPassword(Yii::$app->security->generateRandomString());
            $model->generatePasswordResetToken();
            $model->save();
            if (Yii::$app->createControllerByID('site')->sendResetPasswordMail($model)) {
                Yii::$app->session->setFlash('success', 'На email ' . $model->email . ' выслана инструкция по созданию пароля');
            }
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Admin model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = Admin::SCENARIO_UPDATE;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->updated_at = DateTime::createFromFormat(DateTime::W3C, time());
            if($model->recoverPassword){
                $model->generatePasswordResetToken();
                $model->save(false);
                if (Yii::$app->createControllerByID('site')->sendResetPasswordMail($model)) {
                    Yii::$app->session
                        ->setFlash('success', 'На email ' . $model->email . ' выслана инструкция по изменению пароля');
                }
            }
            $model->save(false);
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Admin model.
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
     * Finds the Admin model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Admin the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Admin::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}

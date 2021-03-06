<?php
namespace backend\controllers;


use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\Response;
use common\models\ar\User;
use backend\models\LoginForm;
use common\models\ResetPasswordForm;

/**
 * Site controller
 */
class SiteController extends Controller
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
                    'logout' => ['post'],
                    'remember-password' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {

            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionRememberPassword()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /** @var User $user */
        $user = User::findOne([
            'email' => Yii::$app->request->post('email', ''),
            'enabled' => true,
        ]);

        if (!$user) {
            return ['msg' => 'Сотрудника с введённым email не существует'];
        }

        $user->generatePasswordResetToken();
        return ($user->save(false) && $user->sendResetPasswordMail())
            ? ['msg' => 'Письмо с инструкциями по восстановлению пароля отправлено на почту']
            : ['msg' => 'Не удалось восстановить пароль. Обратитесь по данному вопросу к администратору: '
            . Yii::$app->params['adminEmail']];

    }

    public function actionResetPassword($token)
    {
        $resetForm = new ResetPasswordForm($token);
        Yii::$app->user->logout();
        if ($resetForm->load(Yii::$app->request->post()) && $resetForm->validate()) {
            $resetForm->resetPassword()
                ? Yii::$app->session->setFlash('success', 'Пароль успешно изменён!')
                : Yii::$app->session->setFlash('error', 'Не удалось изменить пароль. Обратитесь к администратору');
            return $this->redirect(['login']);
        }

        return $this->render('reset-password', ['model' => $resetForm]);
    }

}

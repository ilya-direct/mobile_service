<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\db\Exception;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;;
use yii\web\NotFoundHttpException;;
use frontend\models\ContactForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use common\models\ar\Order;
use common\models\ar\OrderPerson;
use common\models\ar\OrderProvider;
use common\models\ar\OrderStatus;
use common\models\LoginForm;

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
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
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

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    public function actionQuickOrder()
    {
        $order = new Order();
        $orderPerson = new OrderPerson();
        $request = Yii::$app->request;
        if ($orderPerson->load($request->post()) && $order->load($request->post())) {
            $order->order_status_id = OrderStatus::get('new');
            $isValid = $orderPerson->validate();
            $isValid = $order->validate() && $isValid;
            if ($isValid) {
                $transaction = Yii::$app->db->beginTransaction();
                $flag = true;
                try {
                    $orderPerson->save(false);
                    $order->order_person_id = $orderPerson->id;
                    $order->order_provider_id = $request->post('fullForm', false)
                        ? OrderProvider::get('top_form_full')
                        : OrderProvider::get('top_form');
                    $order->save(false);
                    $transaction->commit();
                } catch (Exception $e) {
                    $flag = false;
                    $transaction->rollBack();
                    $order->addError('db', 'Ошибка базы данных! Пожалуйста попробуйте ещё раз или оформите заказ по телефону +7 (963) 656-83-77. Вас ждёт приятный бонус!');
                }
                if ($flag) {
                    Yii::$app->session->set('uid', $order->uid);
                    return $this->redirect('success');
                }
            }
        }
        $this->view->params['hideTopModalForm'] = true;

        return $this->render('quick-order', [
            'order' => $order,
            'orderPerson' => $orderPerson,
        ]);
    }


    public function actionSuccess()
    {
        $uid = Yii::$app->session->remove('uid');
        if ($uid) {

            return $this->render('success',['uid' => $uid]);
        } else {
            throw new NotFoundHttpException();
        }
    }

}

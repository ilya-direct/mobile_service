<?php
namespace frontend\controllers;

use Yii;
use yii\bootstrap\ActiveForm;
use yii\db\Exception;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;;
use yii\web\NotFoundHttpException;;
use yii\web\Response;
use common\models\ar\Order;
use common\models\ar\OrderPerson;
use common\models\ar\OrderProvider;
use common\models\ar\OrderStatus;
use frontend\models\FooterCallbackForm;
use frontend\models\PriceCalculatorForm;

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
        $model = new PriceCalculatorForm();

        // Форма калькулятора
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            $flag = true;
            try {
                $orderPerson = new OrderPerson();
                $orderPerson->first_name = $model->name;
                $orderPerson->phone = $model->phone;
                $orderPerson->save(false);
                $order = new Order();
                $order->order_person_id = $orderPerson->id;
                $order->order_status_id = OrderStatus::get('new');
                $order->order_provider_id = OrderProvider::get('calculator');
                $order->save(false);
                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollBack();
                $flag = false;
                $model->addError('db', 'Ошибка базы данных! Пожалуйста попробуйте ещё раз или оформите заказ по телефону +7 (963) 656-83-77. Вас ждёт приятный бонус!');
            }
            if ($flag) {
                Yii::$app->session->set('uid', $order->uid);
                return $this->redirect(Url::to(['site/success']));
            }
        }
        return $this->render('index', [
            'model' => $model,
        ]);
    }

    public function actionAboutUs()
    {
        return $this->render('about-us');
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContacts()
    {
        return $this->render('contacts', [

        ]);
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
                    return $this->redirect(Url::to(['site/success']));
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

    /**
     * Нижняя форма с просьбой перезвонить
     * @return array
     * @throws Exception
     */
    public function actionFooterCallbackForm()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new FooterCallbackForm();

        $success = false;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();

            try {
                $orderPerson = new OrderPerson();
                $orderPerson->first_name = $model->first_name;
                $orderPerson->phone = '+' . preg_replace('/\D/', '', $model->phone);
                $orderPerson->save(false);

                $order = new Order();
                $order->order_status_id = OrderStatus::get('new');
                $order->order_person_id = $orderPerson->id;
                $order->order_provider_id = OrderProvider::get('footer_callback_form');
                $order->save(false);
            } catch(Exception $e) {
                $transaction->rollBack();

                return [
                    'success' => $success,
                    'validation' => true,
                    'msg' => 'Ошибка базы данных! Пожалуйста попробуйте ещё раз или оформите заказ по телефону +7 (963) 656-83-77. Вас ждёт приятный бонус!',
                ];
            }

            $success = true;
            $transaction->commit();

            return [
                'success' => $success,
                'msg' => 'Спасибо за заявку! Наши специалисты свяжутся с Вами в ближайшее время. Номер заявки: '
                    . $order->uid,
            ];
        }

        return [
            'success' => $success,
            'validation_failed' => true,
            'msg' => 'validation failed',
            'errors' => ActiveForm::validate($model),
        ];
    }

}

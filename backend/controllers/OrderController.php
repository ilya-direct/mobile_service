<?php

namespace backend\controllers;

use Yii;
use yii\base\DynamicModel;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\db\Exception;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use common\components\db\ActiveQuery;
use common\models\ar\DeviceAssign;
use common\models\ar\Order;
use common\models\ar\OrderProvider;
use common\models\ar\OrderService;
use common\models\ar\User;
use backend\models\OrderSearchForm;
use linslin\yii2\curl\Curl;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
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
                    'delete-test-orders' => ['POST']
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [User::ROLE_OPERATOR, User::ROLE_WORKER],
                        'actions' => [
                            'index',
                            'update',
                            'view',
                        ],
                    ],
                    [
                        'allow' => true,
                        'roles' => [User::ROLE_OPERATOR],
                        'actions' => [
                            'create',
                            'delete-test-orders',
                        ],
                    ],
                ],
            ]
        ];
    }

    /**
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearchForm();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
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
        if (!$model) {
            throw new NotFoundHttpException('Страница не существует');
        }

        if (Yii::$app->user->can('orderAccess', ['order' => $model, 'action' => 'view'])) {

            return $this->render('view', [
                'model' => $model,
            ]);
        } else {
            throw new ForbiddenHttpException('Вам не разрешено просматривать данный заказ');
        }
    }

    /**
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $order = new Order(['scenario' => Order::SCENARIO_OPERATOR]);

        return $this->proceed($order, true);
    }

    /**
     * @param $id
     * @return string|Response
     * @throws Exception
     * @throws ForbiddenHttpException
     * @throws \yii\base\Exception
     */
    public function actionUpdate($id)
    {
        /** @var Order $order */
        $order = Order::findOneOrFail($id);

        if (Yii::$app->user->can('orderAccess', ['order' => $order, 'action' => 'update'])) {
            if (Yii::$app->user->can(User::ROLE_OPERATOR)) {
                $order->scenario = Order::SCENARIO_OPERATOR;
            } elseif (Yii::$app->user->can(User::ROLE_WORKER)) {
                $order->scenario = Order::SCENARIO_WORKER;
            }

            return $this->proceed($order, false);
        } else {
            throw new ForbiddenHttpException('Вам не разрешено редактировать данный заказ');
        }
    }

    /**
     * @param Order $order
     * @param bool $isNew новый заказ
     * @return string|\yii\web\Response
     * @throws Exception
     */
    private function proceed($order, $isNew)
    {
        $deviceAssigns = new DynamicModel(['deviceAssignIds']);
        $deviceAssigns->addRule('deviceAssignIds', 'string');
        if (!$isNew) {
            $deviceAssigns->deviceAssignIds = implode(',',
                OrderService::find()
                    ->select('device_assign_id')
                    ->where(['order_id' => $order->id])
                    ->orderBy(['device_assign_id' => SORT_ASC])
                    ->notDeleted()
                    ->column()
            );
        }

        $post = Yii::$app->request->post();
        if ($order->load($post) && $deviceAssigns->load($post)) {
            $validator = DeviceAssign::validateCommaStr($deviceAssigns->deviceAssignIds);
            if (!$validator['valid']) {
                $deviceAssigns->addError('deviceAssignIds', 'Неизвестные id: ' . implode(',', $validator['errors']));
            }

            $valid = $order->validate();
            $valid = (!$deviceAssigns->hasErrors()) && $valid;

            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();
                $flag = true;
                try {
                    if ($isNew) {
                        $order->order_provider_id = OrderProvider::PROVIDER_ADMIN_PANEL;
                    }
                    if (!$order->operator_id) {
                        if (Yii::$app->user->can(User::ROLE_OPERATOR)) {
                            $order->operator_id = Yii::$app->user->id;
                        } else {
                            throw new \yii\base\Exception('Оператор заказа неопределён');
                        }
                    }

                    $order->save(false);
                    if (!$isNew) {
                        OrderService::deleteAll([
                            'and',
                            ['order_id' => $order->id],
                            ['not', ['device_assign_id' => $validator['ids']]]
                        ]);
                    }
                    $usedIds = []; // id у OrderService
                    foreach ($validator['ids'] as $id) {
                        $orderService = OrderService::find()->where([
                            'order_id' => $order->id,
                            'device_assign_id' => $id,
                            'deleted' => false,
                        ])->andWhere(['not', ['id' => $usedIds]])->one();
                        if (!$orderService) {
                            $orderService = new OrderService([
                                'order_id' => $order->id,
                                'device_assign_id' => $id,
                            ]);
                            $orderService->save(false);
                        }
                        $usedIds[] = $orderService->id;
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', $e->getMessage());
                    $flag = false;
                }
                if ($flag) {
                    $transaction->commit();

                    return $this->redirect(['view', 'id' => $order->id]);
                }
            }
        }

        return $this->render($isNew ? 'create' : 'update', [
            'order' => $order,
            'deviceAssigns' => $deviceAssigns,
        ]);
    }


    /**
     * Deletes an existing Order model.
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
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionAddress($q)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $yandexGeocoder = new Curl();
        // если у библиотеки Curl не установлены сертификаты раскоментировать
        $yandexGeocoder->setOption(CURLOPT_SSL_VERIFYPEER, 0);
        $yandexGeocoderResult = $yandexGeocoder->get('https://geocode-maps.yandex.ru/1.x/?&format=json&results=1&bbox=36.044480,54.983701~38.835007,56.495778&geocode=' . urlencode($q),
            false);
        $yandexGeocoderResult = $yandexGeocoderResult['response']['GeoObjectCollection'];

        if ($yandexGeocoderResult['metaDataProperty']['GeocoderResponseMetaData']['found'] != "0") {
            $yandexGeoObject = $yandexGeocoderResult['featureMember'][0]['GeoObject'];
            if ($yandexGeoObject['metaDataProperty']['GeocoderMetaData']['kind'] == 'house') {
                $address = $yandexGeoObject['metaDataProperty']['GeocoderMetaData']['text'];
                list($longitude, $latitude) = explode(' ', $yandexGeoObject['Point']['pos']);

                return [
                    'found' => true,
                    'address' => $address,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ];
            }
        }

        return [
            'found' => false,
        ];
    }

    /**
     * Удаление тестовых заказов
     */
    public function actionDeleteTestOrders()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /** @var Order[] $orders */
        $orders = Order::find()
            ->where(['ilike', 'first_name', '%тест', false])
            ->orWhere(['ilike', 'first_name', '%тестовый', false])
            ->notDeleted()
            ->all();

        $i = 0;
        foreach ($orders as $order) {
            ++$i;
            $order->delete();
        }

        return ['msg' => 'Было удалено ' . $i  . ' тестовых заказов'];

    }
}

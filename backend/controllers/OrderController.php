<?php

namespace backend\controllers;

use Yii;
use yii\base\DynamicModel;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\db\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use common\components\db\ActiveQuery;
use common\models\ar\DeviceAssign;
use common\models\ar\Order;
use common\models\ar\OrderPerson;
use common\models\ar\OrderProvider;
use common\models\ar\OrderService;
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
                ],
            ],
        ];
    }

    /**
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Order::find()->notDeleted(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = Order::find()
            ->innerJoinWith('orderStatus')
            ->innerJoinWith('orderProvider')
            ->innerJoinWith('orderPerson')
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

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $order = new Order();
        $orderPerson = new OrderPerson();

        return $this->proceed($order, $orderPerson, true);
    }

    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        /** @var Order $order */
        $order = Order::findOneOrFail($id);

        /** @var OrderPerson $orderPerson */
        $orderPerson = OrderPerson::findOne([$order->order_person_id]);

        return $this->proceed($order, $orderPerson, false);
    }

    /**
     * @param Order $order
     * @param OrderPerson $orderPerson
     * @param bool $isNew новый заказ
     * @return string|\yii\web\Response
     * @throws Exception
     */
    private function proceed($order, $orderPerson, $isNew)
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
        if ($order->load($post) && $orderPerson->load($post) && $deviceAssigns->load($post)) {
            $validator = DeviceAssign::validateCommaStr($deviceAssigns->deviceAssignIds);
            if (!$validator['valid']) {
                $deviceAssigns->addError('deviceAssignIds', 'Неизвестные id: ' . implode(',', $validator['errors']));
            }

            $valid = $order->validate();
            $valid = $orderPerson->validate() && $valid;
            $valid = (!$deviceAssigns->hasErrors()) && $valid;

            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();
                $flag = true;
                try {
                    // Обновление время обновление заказа и того, кто изменил, если изменён OrderPerson
                    if (!$isNew && $orderPerson->dirtyAttributes) {
                        $order->getBehavior('blameable')->skipUpdateOnClean = false;
                        $order->getBehavior('timestamp')->skipUpdateOnClean = false;
                    }

                    $orderPerson->save(false);
                    if ($isNew) {
                        $order->order_person_id = $orderPerson->id;
                        $order->order_provider_id = OrderProvider::get('admin_panel');
                    }
                    $order->save(false);
                    if (!$isNew) {
                        OrderService::deleteAll([
                            'and',
                            ['order_id' => $order->id],
                            ['not', ['device_assign_id' => $validator['ids']]]
                        ]);
                    }
                    foreach ($validator['ids'] as $id) {
                        OrderService::findOrNew([
                            'order_id' => $order->id,
                            'device_assign_id' => $id,
                            'deleted' => false,
                        ])->save(false);
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
            'orderPerson' => $orderPerson,
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

}

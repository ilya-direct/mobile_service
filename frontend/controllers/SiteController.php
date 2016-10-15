<?php
namespace frontend\controllers;

use Yii;
use yii\bootstrap\ActiveForm;
use yii\db\Exception;
use yii\helpers\Url;
use yii\web\Controller;;
use yii\web\NotFoundHttpException;;
use yii\web\Response;
use common\components\db\ActiveQuery;
use common\models\ar\Device;
use common\models\ar\DeviceCategory;
use common\models\ar\OrderProvider;
use common\models\ar\Vendor;
use frontend\models\CourierOrderForm;
use frontend\models\DeviceOrderForm;
use frontend\models\FooterCallbackForm;
use frontend\models\NotFoundDeviceForm;
use frontend\models\ContactUsForm;
use frontend\models\OrderWithDiscountForm;
use frontend\models\QuickOrderForm;
use frontend\models\PriceCalculatorForm;

/**
 * Site controller
 */
class SiteController extends Controller
{

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::className(),
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

        if ($model->load(Yii::$app->request->post()) && $model->create(OrderProvider::PROVIDER_CALCULATOR)) {
            Yii::$app->session->set('uid', $model->uid);

            return $this->redirect(Url::to(['site/success']));
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
        $model = new ContactUsForm();

        if ($model->load(Yii::$app->request->post()) && $model->create(OrderProvider::PROVIDER_CONTACT_US_FORM)) {
            Yii::$app->session->set('uid', $model->uid);

            return $this->redirect(Url::to(['site/success']));
        }

        return $this->render('contacts', [
            'model' => $model,
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
        $order = new QuickOrderForm();
        $request = Yii::$app->request;

        $orderProviderId = $request->post('fullForm', false)
            ? OrderProvider::PROVIDER_TOP_FORM_FULL
            : OrderProvider::PROVIDER_TOP_FORM;
        if ($order->load($request->post()) && $order->create($orderProviderId)) {
            Yii::$app->session->set('uid', $order->uid);

            return $this->redirect(Url::to(['site/success']));
        }
        $this->view->params['hideTopModalForm'] = true;

        return $this->render('quick-order', [
            'order' => $order,
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

        if ($model->load(Yii::$app->request->post()) && $model->create(OrderProvider::PROVIDER_FOOTER_CALLBACK_FORM)) {

            return [
                'success' => true,
                'msg' => 'Спасибо за заявку! Наши специалисты свяжутся с Вами в ближайшее время. Номер заявки: '
                    . $model->uid,
            ];
        }

        return [
            'success' => false,
            'validation_failed' => true,
            'msg' => 'validation failed',
            'errors' => ActiveForm::validate($model),
        ];
    }

    /**
     * Страница отдельного устройства
     * @param $alias
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionDevice($alias)
    {
        // Форма заказа со скидкой
        $order = new OrderWithDiscountForm();

        if ($order->load(Yii::$app->request->post()) && $order->create(OrderProvider::PROVIDER_ORDER_WITH_DISCOUNT)) {
            Yii::$app->session->set('uid', $order->uid);

            return $this->redirect(Url::to(['site/success']));
        }


        $query = Device::find()
            ->where([
                Device::tableName() . '.alias' => $alias,
                Device::tableName() . '.enabled' => true,
            ])
            ->with([
                'vendor' => function (ActiveQuery $q) {
                    $q->enabled();
                },
                'deviceAssigns' => function (ActiveQuery $q) {
                    $q->enabled();
                    $q->joinWith(['service' => function (ActiveQuery $q) {
                        $q->enabled();
                    }]);
                },
                'deviceCategory' => function (\yii\db\ActiveQuery $q) {
                    $q->andWhere(['enabled' => true]);
                }
            ]);
        $sql = $query->createCommand()->rawSql;
        /** @var Device $model */
        $model= $query->one();

        if ($model) {

            return $this->render('device', [
                'model' => $model,
                'orderWithDiscount' => $order,
            ]);
        } else {
            throw new NotFoundHttpException();
        }
    }

    /**
     * Ajax - заказ со страницы устройства (POST only)
     * @return array|Response
     * @throws Exception
     */
    public function actionDeviceOrder()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new DeviceOrderForm();
        if ($model->load(Yii::$app->request->post()) && $model->create(OrderProvider::PROVIDER_DEVICE_FORM, true, $model->device_assign_id)) {
            Yii::$app->session->set('uid', $model->uid);

            return $this->redirect(Url::to(['site/success']));
        }

        return $model->errors;
    }


    /**
     * Страница с категориями
     * @param $alias
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionCategory($alias)
    {
        /** @var DeviceCategory $category */
        $category = DeviceCategory::findOne(['alias' => $alias, 'enabled' => true]);

        if ($category) {
            /** @var Vendor[] $vendors */
            $vendors = Vendor::find()
                ->joinWith(['devices' => function(ActiveQuery $q) use ($category) {
                    $q->where([
                        Device::tableName() . '.device_category_id' => $category->id,
                        Device::tableName() . '.enabled' => true,
                    ]);
                    $q->orderBy([Device::tableName() . '.name' => SORT_DESC]);
                }])
                ->enabled()
                ->orderBy(Vendor::tableName() . '.name')
                ->all();
            $deviceForm = new NotFoundDeviceForm();

            return $this->render('category', [
                'category' => $category,
                'vendors' => $vendors,
                'notFoundDeviceFormModel' => $deviceForm,
            ]);
        } else {
            throw new NotFoundHttpException('Категория не найдена');
        }
    }

    /**
     * Отправка формы "Не нашли нужную модель"
     * POST
     */
    public function actionNotFoundDevice()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new NotFoundDeviceForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->client_comment = 'Не нашёл модель: ' . trim($model->client_comment);
            $model->create(OrderProvider::PROVIDER_NOT_FOUND_DEVICE_FORM, false);
            Yii::$app->session->set('uid', $model->uid);

            return $this->redirect(Url::to(['site/success']));
        }

        return $model->errors;
    }


    /**
     * Страница с конкретным производителем
     * @param $vendorAlias
     * @param null $categoryAlias
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionVendor($vendorAlias, $categoryAlias = null)
    {
        $category = null;
        if ($categoryAlias) {
            /** @var DeviceCategory $category */
            $category = DeviceCategory::findOne(['alias' => $categoryAlias, 'enabled' => true]);
            if ($category) {
                $this->view->params['breadcrumbs'][] = [
                    'label' => $category->name,
                    'url' => [ 'category/' . $category->alias],
                ];
            } else {
                throw new NotFoundHttpException('Категория не найдена');
            }
        }

        /** @var Vendor $vendor */
        $vendor = Vendor::findOne(['alias' => $vendorAlias, 'enabled' => true]);
        if (!$vendor) {
            throw new NotFoundHttpException('Производитель не найден');
        }

        /** @var Device[] $devices */
        $devices = Device::find()
            ->where(['vendor_id' => $vendor->id])
            ->andFilterWhere(['device_category_id' => $category ? $category->id : null])
            ->enabled()
            ->orderBy([Device::tableName() . '.name' => SORT_DESC])
            ->all();

        return $this->render('vendor', [
            'vendor' => $vendor,
            'devices' => $devices,
            'category' => $category,
        ]);
    }

    /**
     * Страница со скидками
     *
     * @return string
     */
    public function actionDiscounts()
    {

        return $this->render('discounts');
    }

    /**
     * Выезд мастера и курьера
     *
     * @return string
     */
    public function actionCourier()
    {
        $model = new CourierOrderForm();

        if ($model->load(Yii::$app->request->post()) && $model->create(OrderProvider::PROVIDER_COURIER_FORM)) {
            Yii::$app->session->set('uid', $model->uid);

            return $this->redirect(Url::to(['site/success']));
        }

        return $this->render('courier', [
            'model' => $model,
        ]);
    }

}

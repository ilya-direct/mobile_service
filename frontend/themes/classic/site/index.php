<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use common\models\ar\DeviceCategory;
use common\models\ar\Vendor;

/**
 * @var yii\web\View $this
 * @var \frontend\models\PriceCalculatorForm $model
 */
$this->title = 'Сервис по ремонту портативной техники';

$asset = $this->registerAssetBundle(\frontend\assets\AppAsset::className());

$baseUrl = $asset->baseUrl;

?>
<div class="slider">
    <div class="slider_form visible-lg" style="padding: 10px;background: rgba(51, 51, 51, 0.8);font: 13px/1.4 robotoregular;top:5%">
        <h2 style="font-size: 1.5em;color: white;margin-bottom: 15px"> Калькулятор стоимости услуг</h2>
        <?php $form = ActiveForm::begin(); ?>
        <div style="color: red;text-align: center;margin-bottom: 10px;">
            <?= \yii\bootstrap\Html::error($model, 'db'); ?>
        </div>
        <?= $form->field($model, 'device_category_id')
            ->dropDownList(DeviceCategory::getRootList(), [
                'prompt' => 'Выберете тип устройства...',
            ])
            ->label(false); ?>
        <?= $form->field($model, 'vendor_id')
            ->dropDownList(Vendor::getList(['enabled' => true]), ['prompt' => 'Выберете производителя...'])
            ->label(false); ?>
        <?= $form->field($model, 'device_id')
            ->dropDownList(\common\models\ar\Device::getList(['enabled' => true]), ['prompt' => 'Выберете устройство...'])
            ->label(false); ?>
        <?= $form->field($model, 'service_id')
            ->dropDownList(\common\models\ar\Service::getList(['enabled' => true]), ['prompt' => 'Выберете услугу...'])
            ->label(false); ?>
        <div class="row">
            <div class="col-xs-6" style="padding: 5px">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Имя *'])->label(false); ?>
            </div>
            <div class="col-xs-6" style="padding: 5px">
                <?= $form->field($model, 'phone')->widget(\yii\widgets\MaskedInput::className(),
                    ['mask' => '+7 (999) 999-99-99'])->textInput(['placeholder' => 'Телефон *'])->label(false); ?>
            </div>
        </div>
        <?= \yii\helpers\Html::submitButton('Рассчитать'); ?>
        <?php $form->end(); ?>
    </div>
    <ul class="bxslider">
        <li>
            <img src="<?= $baseUrl; ?>/images/slider_img1.jpg" alt="" />
        </li>
        <li>
            <img src="<?= $baseUrl; ?>/images/slider_img2.jpg" alt="" />
        </li>
        <li>
            <img src="<?= $baseUrl; ?>/images/slider_img3.jpg" alt="" />
        </li>
    </ul>
</div>
<div class="advantages">
    <div class = "row">
        <div class = "col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="advantages_bl">
                <div class = "row">
                    <div class = "col-xs-12 col-sm-1 col-md-2 col-lg-2">
                        <div class="advantages_img">
                            <img src="<?= $baseUrl; ?>/images/advantage-access.jpg" alt="" />
                        </div>
                    </div>
                    <div class = "col-xs-12 col-sm-10 col-md-10 col-lg-10">
                        <div class="advantages_ttle">
                            <h3>Только качественные запчасти!</h3>
                            <p>Работаем только с качественными запчастями, установка которых гарантирует 100% работоспособность.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class = "col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="advantages_bl">
                <div class = "row">
                    <div class = "col-xs-12 col-sm-1 col-md-2 col-lg-2">
                        <div class="advantages_img">
                            <img src="<?= $baseUrl; ?>/images/advantage-courier.jpg" alt="" />
                        </div>
                    </div>
                    <div class = "col-xs-12 col-sm-10 col-md-10 col-lg-10">
                        <div class="advantages_ttle">
                            <h3>Только качественные запчасти!</h3>
                            <p>Работаем только с качественными запчастями, установка которых гарантирует 100% работоспособность.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class = "col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="advantages_bl">
                <div class = "row">
                    <div class = "col-xs-12 col-sm-1 col-md-2 col-lg-2">
                        <div class="advantages_img">
                            <img src="<?= $baseUrl; ?>/images/advantage-wifi.jpg" alt="" />
                        </div>
                    </div>
                    <div class = "col-xs-12 col-sm-10 col-md-10 col-lg-10">
                        <div class="advantages_ttle">
                            <h3>Только качественные запчасти!</h3>
                            <p>Работаем только с качественными запчастями, установка которых гарантирует 100% работоспособность.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class = "col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="advantages_bl">
                <div class = "row">
                    <div class = "col-xs-12 col-sm-1 col-md-2 col-lg-2">
                        <div class="advantages_img">
                            <img src="<?= $baseUrl; ?>/images/advantage-speed.jpg" alt="" />
                        </div>
                    </div>
                    <div class = "col-xs-12 col-sm-10 col-md-10 col-lg-10">
                        <div class="advantages_ttle">
                            <h3>Только качественные запчасти!</h3>
                            <p>Работаем только с качественными запчастями, установка которых гарантирует 100% работоспособность.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="wr_services">
    <div class="services">
        <div class = "row">
            <div class = "col-xs-12 col-sm-12 col-md-3 col-lg-3">
                <div class="services_ttle">
                    <img src="<?= $baseUrl; ?>/images/services_img1.jpg" alt="" />
                    <a href="<?= Url::to(['site/device', 'alias' => 'computer']); ?>">Ремонт компьютеров</a>
                    <p>Если Вам понадобился профессиональный ремонт компьютеров, обращайтесь к нам.</p>
                </div>
            </div>
            <div class = "col-xs-12 col-sm-12 col-md-3 col-lg-3">
                <div class="services_ttle">
                    <img src="<?= $baseUrl; ?>/images/services_img4.jpg" alt="" />
                    <a href="<?= Url::to(['site/category', 'alias' => 'noutbuki']); ?>">Ремонт ноутбуков</a>
                    <p>Наш сервис осуществляет ремонт ноутбуков известных производителей.</p>
                </div>
            </div>
            <div class = "col-xs-12 col-sm-12 col-md-3 col-lg-3">
                <div class="services_ttle">
                    <img src="<?= $baseUrl; ?>/images/services_img2.jpg" alt="" />
                    <a href="<?= Url::to(['site/category', 'alias' => 'telefony']); ?>">Ремонт телефонов</a>
                    <p>Так же наш сервисный центр предлагает услугу по ремонту смартфонов.</p>
                </div>
            </div>
            <div class = "col-xs-12 col-sm-12 col-md-3 col-lg-3">
                <div class="services_ttle">
                    <img src="<?= $baseUrl; ?>/images/services_img3.jpg" alt="" />
                    <a href="<?= Url::to(['site/category', 'alias' => 'plansety']); ?>">Ремонт планшетов</a>
                    <p>Наш сервисный центр рад предложить Вам профессиональный ремонт планшетов.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="bl_ttle">
    <h2>BMSTUСервис- ремонт мобильной, компьютерной и цифровой техники в Москве</h2>
    <p>Если дело заходит о ремонте компьютерной и цифровой техники, перед пользователем встает вопрос, где починить сломанный гаджет в Москве? Однозначно это должен быть надежный мастер или специализированный сервисный центр, где сделают диагностику, а после произведут ремонт. Сервисный центр «BMSTUСервис» предлагает Вам профессиональный ремонт телефонов, планшетов и ноутбуков.</p>
    <p>Мы имеем достаточно большой опыт для того чтобы произвести профессиональный ремонт сломанного устройства в кротчайшие сроки, для того чтобы выявить ту или иную проблему нашим специалистам не требуется много времени. Диагностика у нас бесплатная, после которой мастер указывает на неисправность, сроках ее устранения и стоимости работ. На все проделанные нами работы предоставляется длительная гарантия, в течение которой мы обязуемся устранить повторною неисправность. Если у Вас сломался компьютер, смартфон или ноутбук будьте уверены что в «BMSTUСервис» их приведут в полный порядок!</p>
</div>
<?= $this->render('_feedback'); ?>
<div class="delivery">
    <h2>Курьерская доставка</h2>
    <h3>Вам необязательно выезжать к нам в сервис что бы отремонтироват</h3>
    <img src="<?= $baseUrl; ?>/images/delivery_img.jpg" alt="" />
    <a href="#">подробнее</a>
</div>
<div class="bl_ttle">
    <h2>Почему именно мы? Все очень просто!</h2>
    <p>Наш сервисный центр придерживается политики «приведи друга». По статистике каждый третий клиент рекомендует нас, либо обращаться к нам повторно. За долгое время мы наработали большую базу лояльных клиентов, что для нас очень важно, так как отпадает надобность тратить огромные средства на рекламные компании, что позволяет переложить расходы на более важные моменты, которые поднимают планку качества выполняемых нами услуг на новый уровень. Например, в нашем штате работают только квалифицированные специалисты, и каждый мастер выполняет именно ту задачу, в которой он разбирается практически идеально.</p>
    <p>Стоит так же отметить, что мы используем только качественные комплектующие, установка которых, гарантирует длительную работу отремонтированного устройства в нашем сервисе. Мы дорожим каждым клиентом, доверяйте свою технику профессионалам и она прослужит Вам еще очень долго!</p>
</div>

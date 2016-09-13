<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use frontend\assets\AppAsset;

/**
 * @var \yii\web\View $this
 * @var \frontend\models\CourierOrderForm $model
 */
$baseUrl = AppAsset::register($this)->baseUrl;
$this->title = 'Услуга мобильный мастер';

$this->params['breadcrumbs'][] = $this->title;

?>
<div class="container-fluid">
    <h2>Мобильный мастер</h2>
    <p>Сервис стал ещё доступнее. Для того, чтобы отремонтировать Ваше устройство. Вы просто можете оставить заявку
        <a style="border-bottom: 1px dashed;text-decoration: none;" href="#courier-form">ниже</a> и мастер приедет к Вам домой
        или в офис</p>
    <p>Внимание, услуга распростаняется не на все виды ремонта, для уточнения возможности заказа данной услуги обратитесь
        к нашим специалистам</p>
    <p>Схема работы:</p>
</div>
<div class="arrow"></div>
<div class="row">
    <div class="col-lg-12 " style="display: -webkit-box;display: -moz-box;display: -ms-flexbox;display: flex;overflow-x:auto; height: 250px;overflow-y:hidden;">
        <img style="margin: 0 auto" src="<?= $baseUrl; ?>/images/courier-working-order.png">
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12" style="max-width: 400px;margin: 0 auto; float: none;margin-top: 20px; height: 300px">
            <?php $form = ActiveForm::begin(['id' => 'courier-form']); ?>
            <p style="text-align: center;font-size: 1.4em;">Заявка</p>
            <div style="text-align: center;border: 1px dashed green;border-radius: 8px;">
                При заказе сейчас устройство будет как новое в <?= date('H:i', time() + 4*60*60); ?>*
            </div>
            <?= $form->field($model, 'db',['options' => ['style' => 'text-align:center;']])->hiddenInput()->label(false); ?>
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]); ?>
            <?= $form->field($model, 'phone')->widget(\yii\widgets\MaskedInput::className(), [
                'mask' => '+7 (999) 999-99-99',
            ]); ?>
            <?= Html::submitButton('Отправить', ['class' => 'btn btn-success',
                'style' => 'background:#00c962;display:block;margin:0 auto;']); ?>
            <?php $form->end(); ?>
        </div>
    </div>
</div>
<?php

$this->registerCss(<<<CSS
.arrow {
  position: relative;
  margin: 20px auto;
      width: 85%;
    height: 5px;
    background: #00c962;
  }
  .arrow:before,
 .arrow:after {
    content: "";
    display: block;
    position: absolute;
    width: 0;
    height: 0;
    border: 40px solid transparent;
    }

  .arrow:after {
    top: -5px;
    right: -25px;
    border-left-color: #00c962;
    border-width: 8px 0 8px 60px;
    border-right: 0;
    }

    .arrow:before {
        border-right-color: #00c962;
    border-width: 8px 60px 8px 0;
    border-left: 0;
    top: -5px;
    left: -25px;
    }

CSS
);

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model \backend\modules\content\models\PriceListImportForm */

$this->title = 'Загрузка стоимости услуг';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="price-list-import">

    <h1><?= Html::encode($this->title); ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'file')->fileInput()->label(false); ?>

    <?= Html::submitButton('Загрузить', ['class'=> 'btn btn-success']); ?>

    <?php ActiveForm::end(); ?>
</div>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model \backend\modules\content\models\PriceListImportForm */

$this->title = 'Прайслисты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="price-list-import">

    <h1 style="display: inline;"><?= Html::encode($this->title); ?></h1>
    <?= Html::a('Выгрузить цены', ['/content/price-list/export'], [
        'class' => 'btn btn-primary',
        'style' => 'position:relative;bottom:5px;left:20px',
    ]); ?>
    <?php $form = ActiveForm::begin(['options' => ['style' => 'margin-top:10px']]); ?>

    <?= $form->field($model, 'file')->fileInput()->label(false); ?>

    <?= Html::submitButton('Загрузить цены', ['class'=> 'btn btn-success']); ?>

    <?php ActiveForm::end(); ?>
</div>

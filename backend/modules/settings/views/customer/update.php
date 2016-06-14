<?php

use yii\helpers\Html;

/**
 * @var $this yii\web\View
 * @var $model common\models\ar\Customer
 */

$this->title = 'Редактирование клиента: ' . $model->last_name . ' ' . $model->first_name;
$this->params['breadcrumbs'][] = ['label' => 'Клиенты', 'url' => ['index']];
$this->params['breadcrumbs'][] = [
    'label' => $model->last_name . ' ' . $model->first_name,
    'url' => ['view', 'id' => $model->id]
];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="customer-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

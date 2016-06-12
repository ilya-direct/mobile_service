<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ar\News */

$this->title = 'Добавление новости';
$this->params['breadcrumbs'][] = ['label' => 'Новости', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="news-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

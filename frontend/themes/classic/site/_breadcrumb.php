<?php

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

/**
 * @var \yii\web\View $this
 */
if (empty($this->params['breadcrumbs'])) {
    return;
}
?>
<div class="wr_container">
    <div class="container">
        <h2><?= Html::encode($this->title); ?></h2>
        <?= Breadcrumbs::widget([
            'links' => $this->params['breadcrumbs'],
        ]); ?>
    </div>
</div>

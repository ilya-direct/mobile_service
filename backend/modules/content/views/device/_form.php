<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\tree\TreeViewInput;
use common\models\ar\Vendor;
use rmrevin\yii\fontawesome\AssetBundle;

/**
 * @var $this yii\web\View
 * @var $model common\models\ar\Device
 * @var $form yii\widgets\ActiveForm
 */

$this->registerAssetBundle(AssetBundle::className());
?>

<div class="device-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'image')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vendor_id')->dropDownList(Vendor::getList(), ['prompt' => 'Выберете производителя']); ?>

    <?= $form->field($model, 'device_category_id', ['template' => "{input}\n{hint}\n{error}"])
        ->widget(TreeViewInput::className(), [
        // TODO модель поместить в контроллер
        'query' => \common\models\ar\DeviceCategory::find()->orderBy(['tree' => SORT_ASC, 'lft' => SORT_ASC]),
        'headingOptions' => ['label' => 'Выберете категории для устройства  '],
        'rootOptions' => ['label' => '<i class="fa fa-tree text-success"></i>'],
        'fontAwesome' => true,
        'defaultChildNodeIcon' => '<i class="fa fa-folder"></i>',
        'asDropdown' => false,
        'multiple' => false,
        'options' => ['disabled' => false],
    ]); ?>

    <?= $form->field($model, 'enabled')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

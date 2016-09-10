<?php

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;

/**
 * @var \yii\web\View $this
 */
if (empty($this->params['breadcrumbs'])) {
    return;
}
$baseUrl = AppAsset::register($this)->baseUrl;
?>
<div class="breadcrumb-container">
    <div class="container">
        <h2><?= Html::encode($this->title); ?></h2>
        <?= Breadcrumbs::widget([
            'links' => $this->params['breadcrumbs'],
        ]); ?>
    </div>
</div>
<?php

$this->registerCss(<<<CSS

.breadcrumb-container {
    border-bottom: 1px solid #E4E4E4;
    padding-top: 10px;
    padding-bottom: 5px;
}

.breadcrumb-container h2 {
    margin-top: 30px;
    margin-bottom: 0;
}


.breadcrumb li a {
    background: url("{$baseUrl}/images/double_pagi.jpg") no-repeat right 6px;
    display: inline-block;
    vertical-align: top;
    color: #c4c4c4;
    font: 13px/1.1 robotolight;
    padding-right: 10px;
    text-decoration: none;
    margin-top: 15px;
}

.breadcrumb {
    padding: 0;
    margin-bottom: 10px;
    background-color: #fff;
}

.breadcrumb .active {
    display: inline-block;
    vertical-align: top;
    color: #c4c4c4;
    font: 13px/1.1 robotolight;
    padding-right: 10px;
    text-decoration: none;
    margin-top: 15px;
}

.breadcrumb > li + li:before {
    padding: 0;
    content: "";
}


CSS
);

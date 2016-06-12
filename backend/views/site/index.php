<?php

use yii\helpers\Url;

/**
 * @var $this yii\web\View
 */
$this->title = 'Мобильная лаборатория - Главная';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-index">
    <div class="body-content">
        <div class="list-group">
            <a href="<?= Url::to(['/content/news']) ?>" class="list-group-item">
                <h4 class="list-group-item-heading">Новости</h4>
                <p class="list-group-item-text">Список новостей для сайта</p>
            </a>
            <a href="<?= Url::to(['/content/device']) ?>" class="list-group-item">
                <h4 class="list-group-item-heading">Устройства</h4>
                <p class="list-group-item-text">Устройства, которые мы ремонтируем с ценами и акциями</p>
            </a>
            <a href="<?= Url::to(['/content/device-category']) ?>" class="list-group-item">
                <h4 class="list-group-item-heading">Категории</h4>
                <p class="list-group-item-text">Категории устройств по брендам. Древовидная структура</p>
            </a>
            <a href="<?= Url::to(['/settings/admin']) ?>" class="list-group-item">
                <h4 class="list-group-item-heading">Сотрудники компании</h4>
                <p class="list-group-item-text">Редактирование профилей сотрудников</p>
            </a>
            <a href="<?= Url::to(['/settings/customer']) ?>" class="list-group-item">
                <h4 class="list-group-item-heading">Клиенты</h4>
                <p class="list-group-item-text">Список всех клиентов с возможностью добавления новых</p>
            </a>
        </div>
    </div>
</div>

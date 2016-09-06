<?php

use yii\helpers\Url;
use frontend\assets\AppAsset;

/**
 * @var yii\web\View $this
 */

$this->title = 'Контакты';
$this->params['breadcrumbs'][] = $this->title;
$bundle = AppAsset::register($this);
$baseUrl = $bundle->baseUrl;
?>

<div class="wr_container">
    <div class="container">
        <h2>Контакты</h2>
        <ul>
            <li>
                <a href="<?= Url::to('/'); ?>">Главная</a>
            </li>
            <li style="margin-top: 15px;color: #c4c4c4;font: 13px/1.1 robotolight;">
                Контакты
            </li>
        </ul>
    </div>
</div>
<div class="associate">
    <h2>Свяжитесь с нами</h2>
    <ul>
        <li>
            <p class="work2">пн-пт: 10:00 - 20:00 сб-вс: 10:00 - 18:00</p>
        </li>
        <li>
            <p class="phone2">+7 (963) 656 83 77</p>
        </li>
        <li>
            <p class="email2">ilya-direct@ya.ru</p>
        </li>
        <li>
            <p class="adress2">г.Москва ул. Фридриха Энгельса, д. 21</p>
        </li>
    </ul>
</div>
<div class="map">
    <h2>Приезжайте к нам</h2>
    <div class="bl_mp">
        <img src="<?= $baseUrl; ?>/images/map_bg.jpg" alt="" />
    </div>
</div>
<div class="wr_report">
    <div class="report">
        <h2>Оставьте нам сообщение</h2>
        <form name="Myform" action="#" method="post">
            <div class = "row">
                <div class = "col-xs-12 col-sm-4 col-md-4 col-lg-4">
                    <div class="report_ipnut">
                        <label>Ваше Имя</label>
                        <input type="text" name="name" placeholder="" />
                    </div>
                </div>
                <div class = "col-xs-12 col-sm-4 col-md-4 col-lg-4">
                    <div class="report_ipnut">
                        <label>Ваш телефон</label>
                        <input type="text" name="phone" placeholder="" />
                    </div>
                </div>
                <div class = "col-xs-12 col-sm-4 col-md-4 col-lg-4">
                    <div class="report_ipnut">
                        <label>E-mail</label>
                        <input type="text" name="email" placeholder="" />
                    </div>
                </div>
                <div class = "col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="report_textarea">
                        <label>Ваше сообщение</label>
                        <textarea cols="5" rows="5"></textarea>
                    </div>
                </div>
            </div>
            <button>оформить заявку</button>
        </form>
    </div>
</div>
<div class="bl_ttle">
    <h2>Почему именно мы? Все очень просто!</h2>
    <p>Наш сервисный центр придерживается политики «приведи друга». По статистике каждый третий клиент рекомендует нас, либо обращаться к нам повторно. За долгое время мы наработали большую базу лояльных клиентов, что для нас очень важно, так как отпадает надобность тратить огромные средства на рекламные компании, что позволяет переложить расходы на более важные моменты, которые поднимают планку качества выполняемых нами услуг на новый уровень. Например, в нашем штате работают только квалифицированные специалисты, и каждый мастер выполняет именно ту задачу, в которой он разбирается практически идеально.</p>
    <p>Стоит так же отметить, что мы используем только качественные комплектующие, установка которых, гарантирует длительную работу отремонтированного устройства в нашем сервисе. Мы дорожим каждым клиентом, доверяйте свою технику профессионалам и она прослужит Вам еще очень долго!</p>
</div>

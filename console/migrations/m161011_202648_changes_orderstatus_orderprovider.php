<?php

use yii\db\Migration;

class m161011_202648_changes_orderstatus_orderprovider extends Migration
{
    public function safeUp()
    {
        Yii::$app->runAction('fix/fill-order-providers');
        Yii::$app->runAction('fix/fill-order-statuses');
    }

    public function safeDown()
    {

        return false;
    }

}

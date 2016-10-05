<?php

use yii\db\Migration;
use common\models\ar\User;

class m161004_115206_rbac_roles extends Migration
{
    public function safeUp()
    {
        $this->execute("CREATE TYPE user_role AS ENUM ('admin', 'operator', 'worker', 'login_only')");
        $this->addColumn(User::tableName(), 'role', 'user_role');
        $this->addCommentOnColumn(User::tableName(), 'role', 'Роль пользователя в системе (у пользователя может быть только одна роль)');
        $this->update(User::tableName(), ['role' => 'login_only']);
        $this->execute('ALTER TABLE ' . User::tableName() . ' ALTER COLUMN [[role]] SET NOT NULL');
        Yii::$app->runAction('fix/revision', [User::className(), 'role']);
        Yii::$app->runAction('rbac/init');
    }

    public function safeDown()
    {
        $this->dropColumn(User::tableName(), 'role');
        $this->execute('DROP TYPE user_role');
    }
}

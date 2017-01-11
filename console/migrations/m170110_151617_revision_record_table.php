<?php

use yii\db\Migration;

class m170110_151617_revision_record_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%revision_record}}', [
            'id' => $this->primaryKey(),
            'revision_table_id' => $this->integer()->notNull()->comment('Table in revision'),
            'record_id' => $this->integer()->notNull()->comment('Table record_id in revision'),
            'value' => 'jsonb NOT NULL',
            'user_id' => $this->integer(),
            'created_at' => $this->dateTime()->notNull(),
            'revision_operation_type_id' => $this->integer()->notNull(),
            'place' => $this->string()->comment('Place, where record changed (deleled, updated)'),
        ]);
        $this->addCommentOnTable('{{%revision_record}}', 'Revisions on records (creates and deletes)');
        $this->addCommentOnColumn('{{%revision_record}}', 'value' ,'Json of record attributes');
        
        $this->createTable('{{%revision_operation_type}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->unique()->comment('Operation name'),
        ]);
    
        $this->addForeignKey(
            'FK__revision_record__revision_table_id__revision_table__id',
            '{{%revision_record}}',
            'revision_table_id',
            '{{%revision_table}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );
    
        $this->addForeignKey(
            'FK__revision_record__user_id__user__id',
            '{{%revision_record}}',
            'user_id',
            '{{%user}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );
    
        $this->addForeignKey(
            'FK__revision_record__revision_operation_type_id__revision_operation_type__id',
            '{{%revision_record}}',
            'revision_operation_type_id',
            '{{%revision_operation_type}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );
    
        /** Удаление всех ревизий на INSERT */
        Yii::$app->db->createCommand()
            ->delete('{{%revision}}', ['operation_type' => true])
            ->execute();
        $this->dropColumn('{{%revision}}', 'operation_type');
    
        Yii::$app->runAction('fix/fill-revision-operation-types');
        Yii::$app->runAction('fix/revision');
        
    }

    public function safeDown()
    {
        $this->dropTable('{{%revision_record}}');
        $this->dropTable('{{%revision_operation_type}}');
        $this->addColumn('{{%revision}}', 'operation_type', $this->boolean()->comment('true - insert operation, false - update operation'));
    }
}

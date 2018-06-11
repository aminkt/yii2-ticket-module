<?php

use yii\db\Migration;

/**
 * Class m180610_083130_department_table
 */
class m180610_083130_department_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%departments}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(191)->notNull(),
            'description' => $this->string(191),
            'status' => $this->smallInteger(1)->defaultValue(1),
            'createAt' => $this->dateTime(),
            'updateAt' => $this->dateTime()
        ]);

        $this->addColumn('{{%tickets}}', 'departmentId', $this->integer()->after('subject'));

        $this->addForeignKey(
            'ticket_fk_department_by_departmentId',
            '{{%tickets}}',
            'departmentId',
            '{{%departments}}',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('ticket_fk_department_by_departmentId', '{{%tickets}}');
        $this->dropTable('{{%departments}}');
        $this->dropColumn('{{%tickets}}', 'departmentId');

    }

}

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
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%ticket_departments}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(191)->notNull(),
            'description' => $this->string(191),
            'status' => $this->smallInteger(1)->defaultValue(1),
            'createAt' => $this->dateTime(),
            'updateAt' => $this->dateTime()
        ], $tableOptions);

        $this->createTable('{{%ticket_user_departments}}', [
            'departmentId' => $this->integer()->notNull(),
            'userId' => $this->integer()->notNull(),
            'createAt' => $this->dateTime()
        ], $tableOptions);
        $this->addPrimaryKey(
            'ticket_user_departments_pk',
            '{{%ticket_user_departments}}',
            ['departmentId', 'userId']
        );

        $this->addForeignKey(
            'user_departments_fk_department_by_departmentId',
            '{{%ticket_user_departments}}',
            'departmentId',
            '{{%ticket_departments}}',
            'id'
        );

        $this->addColumn('{{%tickets}}', 'departmentId', $this->integer()->after('subject'));

        $this->addForeignKey(
            'ticket_fk_department_by_departmentId',
            '{{%tickets}}',
            'departmentId',
            '{{%ticket_departments}}',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('user_departments_fk_department_by_departmentId', '{{%ticket_user_departments}}');
        $this->dropForeignKey('ticket_fk_department_by_departmentId', '{{%tickets}}');

        $this->dropPrimaryKey('ticket_user_departments_pk', '{{%ticket_user_departments}}');

        $this->dropColumn('{{%tickets}}', 'departmentId');

        $this->dropTable('{{%ticket_departments}}');
        $this->dropTable('{{%ticket_user_departments}}');

    }

}

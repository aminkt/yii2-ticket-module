<?php

use yii\db\Migration;

/**
 * Class m180326_075529_ticket_modules
 */
class m180326_075529_ticket_modules extends Migration
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

        $this->createTable("{{%ticket_categories}}", [
            'id' => $this->primaryKey(),
            'name' => $this->string(191)->notNull(),
            'status' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'updateAt' => $this->dateTime(),
            'createAt' => $this->dateTime(),
        ], $tableOptions);

        $this->createTable("{{%tickets}}", [
            'id' => $this->primaryKey(),
            'customerId' => $this->integer(),
            'name' => $this->string(191)->notNull(),
            'mobile' => $this->string(15),
            'email' => $this->string(191),
            'subject' => $this->string(191)->notNull(),
            'categoryId' => $this->integer(),
            'status' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'updateAt' => $this->dateTime(),
            'createAt' => $this->dateTime(),
        ], $tableOptions);

        $this->createTable("{{%ticket_messages}}", [
            'id' => $this->primaryKey(),
            'message' => $this->text(),
            'ticketId' => $this->integer(),
            'attachments' => $this->string(191),
            'customerCareId' => $this->integer(),
            'updateAt' => $this->dateTime(),
            'createAt' => $this->dateTime(),
        ], $tableOptions);


        // Create forging keys.

        $this->addForeignKey("ticket_fk_ticketCategory_by_categoryId", '{{%tickets}}', 'categoryId', "{{%ticket_categories}}", 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey("ticketMessage_fk_ticket_by_ticketId", '{{%ticket_messages}}', 'ticketId', "{{%tickets}}", 'id', 'CASCADE', 'CASCADE');



    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey("ticket_fk_ticketCategory_by_categoryId", '{{%tickets}}');
        $this->dropForeignKey("ticketMessage_fk_ticket_by_ticketId", '{{%ticket_messages}}');

        $this->dropTable("{{%ticket_categories}}");
        $this->dropTable("{{%tickets}}");
        $this->dropTable("{{%ticket_messages}}");


    }
}

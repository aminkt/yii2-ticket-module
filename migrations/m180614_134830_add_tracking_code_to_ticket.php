<?php

use yii\db\Migration;

/**
 * Class m180614_134830_add_tracking_code_to_ticket
 */
class m180614_134830_add_tracking_code_to_ticket extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%tickets}}', 'trackingCode', $this->string(191)->after('status'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%tickets}}', 'trackingCode');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180614_134830_add_tracking_code_to_ticket cannot be reverted.\n";

        return false;
    }
    */
}

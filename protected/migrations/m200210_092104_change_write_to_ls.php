<?php

use prime\models\permissions\Permission;
use yii\db\Migration;

/**
 * Class m200210_092104_change_write_to_ls
 */
class m200210_092104_change_write_to_ls extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->update('{{%permission}}',
            ['permission' => Permission::PERMISSION_LIMESURVEY],
            ['permission' => Permission::PERMISSION_WRITE]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200210_092104_change_write_to_ls cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200210_092104_change_write_to_ls cannot be reverted.\n";

        return false;
    }
    */
}

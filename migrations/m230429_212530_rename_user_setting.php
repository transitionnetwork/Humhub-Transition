<?php

use yii\db\Migration;

/**
 * Class m230429_212530_rename_user_setting
 */
class m230429_212530_rename_user_setting extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        foreach (\humhub\modules\user\models\User::find()->active()->each() as $user) {
            if ($user->settings->get('hasSeenProfileImageUploadPage')) {
                $user->settings->set('hasSeenAfterRegistrationPage', true);
                $user->settings->set('hasSeenProfileImageUploadPage', null);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230429_212530_rename_user_setting cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230429_212530_rename_user_setting cannot be reverted.\n";

        return false;
    }
    */
}

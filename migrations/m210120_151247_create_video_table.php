<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%video}}`.
 */
class m210120_151247_create_video_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('video_content', [
            'id' => $this->primaryKey(),
            'title' => $this->string(),
            'thumbnail_url' => $this->string(),
            'duration' => $this->integer(),
            'views'=> $this->integer(),
            'added' => $this->dateTime()
        ]);

        $this->createIndex('views_ind', 'video_content', 'views');
        $this->createIndex('views_date', 'video_content', 'added');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('video_content');
    }
}

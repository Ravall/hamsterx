<?php

namespace app\models;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use yii\db\ActiveRecord;

/**
 * @property string title
 * @property string thumbnail_url
 * @property int duration
 * @property int views
 * @property string added
 * @property int id
 * @property mixed|null thumbnailUrl
 * @property mixed|null durationInMinutes
 */
class VideoContent extends ActiveRecord
{
    private const DISPLAY_LONG_URL = 30;

    public static function tableName()
    {
        return 'video_content';
    }

    public function getDurationInMinutes()
    {
        return CarbonInterval::seconds($this->duration)->cascade()->forHumans();
    }

    public function getThumbnailUrl()
    {
        return (strlen($this->thumbnail_url) > self::DISPLAY_LONG_URL)
            ? substr($this->thumbnail_url, 0, self::DISPLAY_LONG_URL) . '...'
            : $this->thumbnail_url;
    }
}
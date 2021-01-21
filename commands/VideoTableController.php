<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\VideoContent;
use Carbon\Carbon;
use Faker\Factory;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class VideoTableController extends Controller
{
    private const COUNT_ROWS = 100000;

    /**
     * This command echoes what you have entered as the message.
     * @return int Exit code
     */
    public function actionGenerate()
    {
        $faker = Factory::create();

        for ($i=0; $i<self::COUNT_ROWS; $i++)  {

            $content = new VideoContent();
            $content->title = implode('_', $faker->words());
            $content->thumbnail_url = $faker->url;
            $content->duration = $faker->numberBetween(0, 60 * 60 * 3);
            $content->views = $faker->numberBetween(0, 1000);
            $content->added = Carbon::parse($faker->dateTimeBetween('-1 year'))->toDateTimeString();
            $content->save();

        }

        return ExitCode::OK;
    }
}

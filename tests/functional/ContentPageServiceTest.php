<?php

use app\models\PageableContent\Entity\SortBy;
use app\models\PageableContent\Entity\SortedPage;
use app\models\PageableContent\Enums\DirEnum;
use app\models\PageableContent\Services\ContentPageService;
use app\models\VideoContent;
use Carbon\Carbon;
use Faker\Factory;

class ContentPageServiceTest extends \Codeception\Test\Unit
{
    /**
     * @var \FunctionalTester
     */
    protected $tester;
    
    protected function _before()
    {
        $faker = Factory::create();

        for ($i=1; $i<=12; $i++) {
            $content = new VideoContent();
            $content->title = implode('_', $faker->words());
            $content->thumbnail_url = $faker->url;
            $content->duration = $faker->numberBetween(0, 60 * 60 * 3);
            $content->views = $i;
            $content->added = Carbon::parse($faker->dateTimeBetween('-1 year'))->toDateTimeString();

            $content->save();
        }

    }

    public function providerFirstAndNext()
    {
        return [
            [
                'asc',
                [1, 3],
                [4, 6]
            ],
            [
                'desc',
                [12, 10],
                [9, 7]
            ]
        ];
    }

    /**
     * c первой прыгаем берем вторую с разными сортировками
     *
     * @dataProvider providerFirstAndNext
     */
    public function testFirstAndNext($dir, $fst, $nxt)
    {
        $container = new \yii\di\Container();
        /** @var ContentPageService $service */
        $service = $container->get(ContentPageService::class);
        $first = new SortedPage(1, new SortBy('views', new DirEnum($dir)));
        $result = $service->getContentPage(VideoContent::find(), $first);
        $this->assertCount(3, $result->getContent());
        $this->assertEquals($fst[0], $result->getContent()[0]['views']);
        $this->assertEquals($fst[1], $result->getContent()[count($result->getContent())-1]['views']);

        $nextPage = $result->getNextPage($first);
        $result = $service->getContentPage(VideoContent::find(), $nextPage);
        $this->assertCount(3, $result->getContent());
        $this->assertEquals($nxt[0], $result->getContent()[0]['views']);
        $this->assertEquals($nxt[1], $result->getContent()[count($result->getContent())-1]['views']);
    }

    public function providerLast()
    {
        return [
            [
                'asc',
                [10, 12],
                [7, 9]
            ],
            [
                'desc',
                [3, 1],
                [6, 4]
            ]
        ];
    }

    /**
     * c первой прыгаем на последнюю, и потом на одну назад  с разными сортировками
     * @dataProvider providerLast
     */
    public function testLast($dir, $last, $prev)
    {
        $container = new \yii\di\Container();
        /** @var ContentPageService $service */
        $service = $container->get(ContentPageService::class);
        $first = new SortedPage(
            1,
            new SortBy('views', new DirEnum($dir))
        );
        $result = $service->getContentPage(VideoContent::find(), $first);
        $lastPage = $result->getLastPage($first);
        $result = $service->getContentPage(VideoContent::find(), $lastPage);
        $this->assertCount(3, $result->getContent());
        $this->assertEquals($last[0], $result->getContent()[0]['views']);
        $this->assertEquals($last[1], $result->getContent()[count($result->getContent())-1]['views']);

        $prevPage = $result->getPreviousPage($lastPage);
        $result = $service->getContentPage(VideoContent::find(), $prevPage);
        $this->assertCount(3, $result->getContent());
        $this->assertEquals($prev[0], $result->getContent()[0]['views']);
        $this->assertEquals($prev[1], $result->getContent()[count($result->getContent())-1]['views']);
    }


    public function jumpProvider()
    {
        return [
            [2, 'asc', [4, 6]],
            [2, 'desc', [9, 7]],
            [3, 'asc', [7, 9]],
            [3, 'desc', [6, 4]],
        ];
    }

    /**
     * @dataProvider jumpProvider
     * @param $to
     * @param $dir
     * @param $res
     */
    public function testJump($to, $dir, $res)
    {
        $container = new \yii\di\Container();
        /** @var ContentPageService $service */
        $service = $container->get(ContentPageService::class);
        $jumpPage = new SortedPage(
            $to,
            new SortBy('views', new DirEnum($dir))
        );
        $result = $service->getContentPage(VideoContent::find(), $jumpPage);
        $this->assertCount(3, $result->getContent());
        $this->assertEquals($res[0], $result->getContent()[0]['views']);
        $this->assertEquals($res[1], $result->getContent()[count($result->getContent())-1]['views']);
    }

}
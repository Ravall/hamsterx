<?php

use app\models\PageableContent\Entity\ContentPage;
use app\models\PageableContent\Entity\SortBy;
use app\models\PageableContent\Entity\SortedPage;
use app\models\PageableContent\Enums\DirEnum;

class ContentPageTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    /**
     * @test
     */
    public function getContent()
    {
        $contentPage = $this->getContentPage();
        $this->assertEquals( [
            [
                'id' => 1
            ],
            [
                'id' => 2
            ],
            [
                'id' => 3
            ]
        ], $contentPage->getContent());


        $contentPage = new ContentPage([], 3, 10);
        $this->assertEquals([], $contentPage->getContent());
    }

    /**
     * @test
     */
    public function getFirstPageSortedBy()
    {
        $contentPage = $this->getContentPage();
        $page = $contentPage->getFirstPageSortedBy('xxx', DirEnum::DESC());
        $this->assertEquals(1, $page->getNumber());
        $this->assertEquals(DirEnum::DESC(), $page->getSortBy()->getDir());
        $this->assertEquals('xxx', $page->getSortBy()->getField());
        $this->assertEmpty($page->getPortionPosition());
        $this->assertEquals('/?page=1&sort=xxx&dir=desc', $page->getUrl());
    }


    /**
     * @test
     */
    public function getLastPage()
    {
        $faker = \Faker\Factory::create();
        $word = $faker->word;
        $content = [
            [
                'id' => 100,
                $word => $value = $faker->randomDigit
            ],
            [
                'id' => 200,
                $word => $faker->randomDigit
            ],
            [
                'id' => 300,
                $word => $faker->randomDigit
            ]
        ];
        $contentPage = new ContentPage($content, 3, 10);

        $sorted = new SortedPage(1, new SortBy($word, DirEnum::ASC()));

        $page = $contentPage->getLastPage($sorted);

        $this->assertEquals(4, $page->getNumber());
        $this->assertEquals(DirEnum::ASC(), $page->getSortBy()->getDir());
        $this->assertEquals($word, $page->getSortBy()->getField());
        $this->assertNotEmpty($page->getPortionPosition());
        $this->assertEquals('/?page=4&sort='.$word.'&dir=asc&position_id=100&position_value='.$value.'&position_page=1', $page->getUrl());
    }

    /**
     * @test
     */
    public function getFirstPage()
    {
        $faker = \Faker\Factory::create();

        $contentPage = $this->getContentPage();
        $sorted = new SortedPage(1, new SortBy($word = $faker->word, DirEnum::ASC()));
        $page = $contentPage->getFirstPage($sorted);

        $this->assertEquals(1, $page->getNumber());
        $this->assertEquals(DirEnum::ASC(), $page->getSortBy()->getDir());
        $this->assertEquals($word, $page->getSortBy()->getField());
        $this->assertEmpty($page->getPortionPosition());
        $this->assertEquals('/?page=1&sort='.$word.'&dir=asc', $page->getUrl());
    }


    /**
     * @test
     */
    public function getPreviousPage()
    {
        $faker = \Faker\Factory::create();

        $word = $faker->word;
        $content = [
            [
                'id' => 100,
                $word => $value = $faker->randomDigit
            ],
            [
                'id' => 200,
                $word => $faker->randomDigit
            ],
            [
                'id' => 300,
                $word => $faker->randomDigit
            ]
        ];
        $contentPage = new ContentPage($content, 3, 10);


        $sorted = new SortedPage(3, new SortBy($word, DirEnum::ASC()));
        $page = $contentPage->getPreviousPage($sorted);

        $this->assertEquals(2, $page->getNumber());
        $this->assertEquals(DirEnum::ASC(), $page->getSortBy()->getDir());
        $this->assertEquals($word, $page->getSortBy()->getField());
        $this->assertFalse($page->getPortionPosition()->empty());
        $this->assertEquals($value, $page->getPortionPosition()->getValue());
        $this->assertEquals(3, $page->getPortionPosition()->getPageNumber());
        $this->assertEquals(100, $page->getPortionPosition()->getId());
        $this->assertEquals('/?page=2&sort='.$word.'&dir=asc&position_id=100&position_value='.$value.'&position_page=3', $page->getUrl());
    }



    /**
     * @test
     */
    public function getPreviousPageFromFirst()
    {
        $faker = \Faker\Factory::create();

        $word = $faker->word;
        $content = [
            [
                'id' => 100,
                $word => $value = $faker->randomDigit
            ],
            [
                'id' => 200,
                $word => $faker->randomDigit
            ],
            [
                'id' => 300,
                $word => $faker->randomDigit
            ]
        ];
        $contentPage = new ContentPage($content, 3, 10);


        $sorted = new SortedPage(1, new SortBy($word, DirEnum::ASC()));
        $page = $contentPage->getPreviousPage($sorted);

        $this->assertNull($page);
    }

    /**
     * @test
     */
    public function getNextPage()
    {
        $faker = \Faker\Factory::create();

        $word = $faker->word;
        $content = [
            [
                'id' => 100,
                $word => $value = $faker->randomDigit
            ],
            [
                'id' => 200,
                $word => $faker->randomDigit
            ],
            [
                'id' => 300,
                $word => $faker->randomDigit
            ]
        ];
        $contentPage = new ContentPage($content, 3, 10);


        $sorted = new SortedPage(2, new SortBy($word, DirEnum::ASC()));
        $page = $contentPage->getNextPage($sorted);

        $this->assertEquals(3, $page->getNumber());
        $this->assertEquals(DirEnum::ASC(), $page->getSortBy()->getDir());
        $this->assertEquals($word, $page->getSortBy()->getField());
        $this->assertFalse($page->getPortionPosition()->empty());
        $this->assertEquals($value, $page->getPortionPosition()->getValue());
        $this->assertEquals(2, $page->getPortionPosition()->getPageNumber());
        $this->assertEquals(100, $page->getPortionPosition()->getId());
        $this->assertEquals('/?page=3&sort='.$word.'&dir=asc&position_id=100&position_value='.$value.'&position_page=2', $page->getUrl());
    }

    /**
     * @test
     */
    public function getPreviousPagesDeep3()
    {
        $faker = \Faker\Factory::create();

        $word = $faker->word;
        $content = [
            [
                'id' => 100,
                $word => $value = $faker->randomDigit
            ],
            [
                'id' => 200,
                $word => $faker->randomDigit
            ],
            [
                'id' => 300,
                $word => $faker->randomDigit
            ]
        ];
        $contentPage = new ContentPage($content, 3, 10);


        $sorted = new SortedPage(4, new SortBy($word, DirEnum::ASC()));
        $pages = $contentPage->getPreviousPages($sorted, 3);

        $this->assertCount(3, $pages);

        $this->assertEquals(1, $pages[0]->getNumber());
        $this->assertEquals(2, $pages[1]->getNumber());
        $this->assertEquals(3, $pages[2]->getNumber());

        foreach ($pages as $page) {
            $this->assertEquals(DirEnum::ASC(), $page->getSortBy()->getDir());
            $this->assertEquals($word, $page->getSortBy()->getField());
            $this->assertFalse($page->getPortionPosition()->empty());
            $this->assertEquals($value, $page->getPortionPosition()->getValue());
            $this->assertEquals(100, $page->getPortionPosition()->getId());
        }
    }

    /**
     * @test
     */
    public function getNextPagesDeep3()
    {
        $faker = \Faker\Factory::create();

        $word = $faker->word;
        $content = [
            [
                'id' => 100,
                $word => $value = $faker->randomDigit
            ],
            [
                'id' => 200,
                $word => $faker->randomDigit
            ],
            [
                'id' => 300,
                $word => $faker->randomDigit
            ]
        ];
        $contentPage = new ContentPage($content, 3, 10);


        $sorted = new SortedPage(1, new SortBy($word, DirEnum::ASC()));
        $pages = $contentPage->getNextPages($sorted, 3);
        $this->assertCount(3, $pages);

        $this->assertEquals(2, $pages[0]->getNumber());
        $this->assertEquals(3, $pages[1]->getNumber());
        $this->assertEquals(4, $pages[2]->getNumber());

        foreach ($pages as $page) {
            $this->assertEquals(DirEnum::ASC(), $page->getSortBy()->getDir());
            $this->assertEquals($word, $page->getSortBy()->getField());
            $this->assertFalse($page->getPortionPosition()->empty());
            $this->assertEquals($value, $page->getPortionPosition()->getValue());
            $this->assertEquals(100, $page->getPortionPosition()->getId());
        }
    }


    /**
     * @test
     */
    public function getPreviousPagesDeep3From3()
    {
        $faker = \Faker\Factory::create();

        $word = $faker->word;
        $content = [
            [
                'id' => 100,
                $word => $value = $faker->randomDigit
            ],
            [
                'id' => 200,
                $word => $faker->randomDigit
            ],
            [
                'id' => 300,
                $word => $faker->randomDigit
            ]
        ];
        $contentPage = new ContentPage($content, 3, 10);


        $sorted = new SortedPage(3, new SortBy($word, DirEnum::ASC()));
        $pages = $contentPage->getPreviousPages($sorted, 3);

        $this->assertCount(2, $pages);

        $this->assertEquals(1, $pages[0]->getNumber());
        $this->assertEquals(2, $pages[1]->getNumber());

        foreach ($pages as $page) {
            $this->assertEquals(DirEnum::ASC(), $page->getSortBy()->getDir());
            $this->assertEquals($word, $page->getSortBy()->getField());
            $this->assertFalse($page->getPortionPosition()->empty());
            $this->assertEquals($value, $page->getPortionPosition()->getValue());
            $this->assertEquals(100, $page->getPortionPosition()->getId());
        }
    }


    /**
     * @test
     */
    public function getNextPagesDeep3From2()
    {
        $faker = \Faker\Factory::create();

        $word = $faker->word;
        $content = [
            [
                'id' => 100,
                $word => $value = $faker->randomDigit
            ],
            [
                'id' => 200,
                $word => $faker->randomDigit
            ],
            [
                'id' => 300,
                $word => $faker->randomDigit
            ]
        ];
        $contentPage = new ContentPage($content, 3, 10);


        $sorted = new SortedPage(2, new SortBy($word, DirEnum::ASC()));
        $pages = $contentPage->getNextPages($sorted, 3);

        $this->assertCount(2, $pages);

        $this->assertEquals(3, $pages[0]->getNumber());
        $this->assertEquals(4, $pages[1]->getNumber());

        foreach ($pages as $page) {
            $this->assertEquals(DirEnum::ASC(), $page->getSortBy()->getDir());
            $this->assertEquals($word, $page->getSortBy()->getField());
            $this->assertFalse($page->getPortionPosition()->empty());
            $this->assertEquals($value, $page->getPortionPosition()->getValue());
            $this->assertEquals(100, $page->getPortionPosition()->getId());
        }
    }

    /**
     * @test
     */
    public function getPreviousPagesDeep3From1()
    {
        $faker = \Faker\Factory::create();

        $word = $faker->word;
        $content = [
            [
                'id' => 100,
                $word => $value = $faker->randomDigit
            ],
            [
                'id' => 200,
                $word => $faker->randomDigit
            ],
            [
                'id' => 300,
                $word => $faker->randomDigit
            ]
        ];
        $contentPage = new ContentPage($content, 3, 10);


        $sorted = new SortedPage(1, new SortBy($word, DirEnum::ASC()));
        $pages = $contentPage->getPreviousPages($sorted, 3);

        $this->assertEmpty($pages);
    }

    /**
     * @test
     */
    public function getNextPagesDeep3From4()
    {
        $faker = \Faker\Factory::create();

        $word = $faker->word;
        $content = [
            [
                'id' => 100,
                $word => $value = $faker->randomDigit
            ],
            [
                'id' => 200,
                $word => $faker->randomDigit
            ],
            [
                'id' => 300,
                $word => $faker->randomDigit
            ]
        ];
        $contentPage = new ContentPage($content, 3, 10);

        $sorted = new SortedPage(4, new SortBy($word, DirEnum::ASC()));
        $pages = $contentPage->getNextPages($sorted, 3);

        $this->assertEmpty($pages);
    }

    /**
     * @test
     */
    public function getPreviousPagesDeep1()
    {
        $faker = \Faker\Factory::create();

        $word = $faker->word;
        $content = [
            [
                'id' => 100,
                $word => $value = $faker->randomDigit
            ],
            [
                'id' => 200,
                $word => $faker->randomDigit
            ],
            [
                'id' => 300,
                $word => $faker->randomDigit
            ]
        ];
        $contentPage = new ContentPage($content, 3, 10);


        $sorted = new SortedPage(4, new SortBy($word, DirEnum::ASC()));
        $pages = $contentPage->getPreviousPages($sorted, 1);

        $this->assertCount(1, $pages);

        $this->assertEquals(3, $pages[0]->getNumber());

        foreach ($pages as $page) {
            $this->assertEquals(DirEnum::ASC(), $page->getSortBy()->getDir());
            $this->assertEquals($word, $page->getSortBy()->getField());
            $this->assertFalse($page->getPortionPosition()->empty());
            $this->assertEquals($value, $page->getPortionPosition()->getValue());
            $this->assertEquals(100, $page->getPortionPosition()->getId());
        }

    }


    /**
     * @test
     */
    public function getNextPagesDeep1()
    {
        $faker = \Faker\Factory::create();

        $word = $faker->word;
        $content = [
            [
                'id' => 100,
                $word => $value = $faker->randomDigit
            ],
            [
                'id' => 200,
                $word => $faker->randomDigit
            ],
            [
                'id' => 300,
                $word => $faker->randomDigit
            ]
        ];
        $contentPage = new ContentPage($content, 3, 10);


        $sorted = new SortedPage(2, new SortBy($word, DirEnum::ASC()));
        $pages = $contentPage->getNextPages($sorted, 1);

        $this->assertCount(1, $pages);

        $this->assertEquals(3, $pages[0]->getNumber());

        foreach ($pages as $page) {
            $this->assertEquals(DirEnum::ASC(), $page->getSortBy()->getDir());
            $this->assertEquals($word, $page->getSortBy()->getField());
            $this->assertFalse($page->getPortionPosition()->empty());
            $this->assertEquals($value, $page->getPortionPosition()->getValue());
            $this->assertEquals(100, $page->getPortionPosition()->getId());
        }
    }


    /**
     * @test
     */
    public function getPreviousPagesDeep0()
    {
        $faker = \Faker\Factory::create();

        $word = $faker->word;
        $content = [
            [
                'id' => 100,
                $word => $value = $faker->randomDigit
            ],
            [
                'id' => 200,
                $word => $faker->randomDigit
            ],
            [
                'id' => 300,
                $word => $faker->randomDigit
            ]
        ];
        $contentPage = new ContentPage($content, 3, 10);


        $sorted = new SortedPage(4, new SortBy($word, DirEnum::ASC()));
        $pages = $contentPage->getPreviousPages($sorted, 0);

        $this->assertEmpty($pages);
    }

    /**
     * @test
     */
    public function getNextPagesDeep0()
    {
        $faker = \Faker\Factory::create();

        $word = $faker->word;
        $content = [
            [
                'id' => 100,
                $word => $value = $faker->randomDigit
            ],
            [
                'id' => 200,
                $word => $faker->randomDigit
            ],
            [
                'id' => 300,
                $word => $faker->randomDigit
            ]
        ];
        $contentPage = new ContentPage($content, 3, 10);

        $sorted = new SortedPage(4, new SortBy($word, DirEnum::ASC()));
        $pages = $contentPage->getNextPages($sorted, 0);

        $this->assertEmpty($pages);
    }


    /**
     * @test
     */
    public function isLast()
    {
        $faker = \Faker\Factory::create();
        $word = $faker->word;
        $content = [
            [
                'id' => 100,
                $word => $value = $faker->randomDigit
            ],
            [
                'id' => 200,
                $word => $faker->randomDigit
            ],
            [
                'id' => 300,
                $word => $faker->randomDigit
            ]
        ];
        $contentPage = new ContentPage($content, 3, 10);

        $sorted = new SortedPage(4, new SortBy($word, DirEnum::ASC()));
        $this->assertTrue($contentPage->isLast($sorted));

        $sorted = new SortedPage(1, new SortBy($word, DirEnum::ASC()));
        $this->assertFalse($contentPage->isLast($sorted));
    }

    /**
     * @test
     */
    public function isFirst()
    {
        $faker = \Faker\Factory::create();
        $word = $faker->word;
        $content = [
            [
                'id' => 100,
                $word => $value = $faker->randomDigit
            ],
            [
                'id' => 200,
                $word => $faker->randomDigit
            ],
            [
                'id' => 300,
                $word => $faker->randomDigit
            ]
        ];
        $contentPage = new ContentPage($content, 3, 10);

        $sorted = new SortedPage(4, new SortBy($word, DirEnum::ASC()));
        $this->assertFalse($contentPage->isFirst($sorted));

        $sorted = new SortedPage(1, new SortBy($word, DirEnum::ASC()));
        $this->assertTrue($contentPage->isFirst($sorted));
    }

    /**
     * @test
     */
    public function getNextPageFromLast()
    {
        $faker = \Faker\Factory::create();

        $word = $faker->word;
        $content = [
            [
                'id' => 100,
                $word => $value = $faker->randomDigit
            ],
            [
                'id' => 200,
                $word => $faker->randomDigit
            ],
            [
                'id' => 300,
                $word => $faker->randomDigit
            ]
        ];
        $contentPage = new ContentPage($content, 3, 10);


        $sorted = new SortedPage(4, new SortBy($word, DirEnum::ASC()));
        $page = $contentPage->getNextPage($sorted);

        $this->assertNull($page);
    }


    private function getContentPage(): ContentPage
    {

        $content = [
            [
                'id' => 1,
            ],
            [
                'id' => 2
            ],
            [
                'id' => 3
            ]
        ];
        return new ContentPage($content, 3, 10);
    }

}
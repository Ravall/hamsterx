<?php

namespace app\models\PageableContent\Services;

use app\models\PageableContent\Entity\ContentPage;
use app\models\PageableContent\Entity\SortedPage;
use app\models\PageableContent\Enums\DirEnum;
use yii\db\ActiveQuery;
use yii\db\Expression;

final class ContentPageService
{
    /**
     * @var int
     */
    private $perPage;

    public function __construct(int $perPage)
    {
        $this->perPage = $perPage;
    }

    public function getMaxPage(ActiveQuery $query): int
    {
        $query = clone $query;
        return (int) ceil($query->count() / $this->perPage);
    }

    public function getContentPage(ActiveQuery $query, SortedPage $sortedPageLink): ContentPage
    {
        $contentQuery = $query
            ->limit($this->perPage)
            ->orderBy([
                $sortedPageLink->getSortBy()->getField() => $sortedPageLink->getSortBy()->getQueryDir(),
                'id' => $sortedPageLink->getSortBy()->getQueryDir(),
            ]);
        $total = $query->count();

        /**
         * в чем идея
         * Don’t touch what you don’t need
         * мы на ххх странице
         * и прыгаем на ххх+3 страницу, то нам достаточно пропустить занчения
         *
         * SELECT * FROM "video_content" WHERE (added,id) <= (date1, id1) ORDER BY "added" DESC, "id" DESC OFFSET 20*3 LIMIT 20
         * где date1 и id1 - значения первой строчки на странице ххх
         *
         * т.е все будет работать быстро. За исключением прыжков с 1-й страницы на 10000-ую тогда будет большой OFFSET
         * но интерфейс такие прыжки не позволяет. Если вбил руками page в url страницы = то увы, сам себе злобный буратино.
         *
         * на случай прыжков с 1-й на 49000 страницу возмона оптимизация:
         *  считать что проще офсетнутся на 1000 с полседней, чем на 49000 с 1-й
         *  (как минимум это может сократить скорость работы от 2 раз и выше)
         *  но я не стал реализовывать этот фунционал - поленился. Все-таки в задаче нет кейса прыжков на страницы.
         */
        if (!$sortedPageLink->getPortionPosition()->empty()) {
            $offset = $this->perPage * ($sortedPageLink->getNumber() - $sortedPageLink->getPortionPosition()->getPageNumber());

            $condition = [
                DirEnum::DESC()->equals($sortedPageLink->getSortBy()->getDir()) ? '<=' : '>=',
                '(' . implode(',', [$sortedPageLink->getSortBy()->getField(), 'id']) . ')',
                new Expression(
                    '(' . implode(',', [
                       "'" . $sortedPageLink->getPortionPosition()->getValue() . "'",
                             $sortedPageLink->getPortionPosition()->getId()
                    ]) . ')'),
            ];

            $contentQuery
                ->offset($offset)
                ->where($condition);
        }

        return new ContentPage(
            $contentQuery->all(),
            $this->perPage,
            $total
        );
    }
}


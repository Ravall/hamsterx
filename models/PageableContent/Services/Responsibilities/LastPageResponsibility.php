<?php

namespace app\models\PageableContent\Services\Responsibilities;

use app\models\PageableContent\Entity\ContentPage;
use app\models\PageableContent\Entity\SortedPage;
use Yii;
use yii\db\ActiveQuery;

/**
 * Экшн запускаемый, если требуется загрузить последнюю страницу
 *
 * Class LastPageResponsibility
 * @package app\models\PageableContent\Services\Responsibilities
 */
final class LastPageResponsibility implements Responsibility
{
    private $perPage;

    public function __construct()
    {
        $this->perPage = Yii::$app->params['perPage'];
    }

    private function getMaxPage(ActiveQuery $query): int
    {
        return (int) ceil($query->count() / $this->perPage);
    }

    public function isActive(ActiveQuery $query, SortedPage $sortedPageLink): bool
    {
        return $sortedPageLink->getNumber() === $this->getMaxPage($query);
    }

    public function process(ActiveQuery $query, SortedPage $sortedPageLink): ContentPage
    {
        $contentQuery = (clone $query);
        $contentQuery->limit($this->perPage);

        $dir = $sortedPageLink->getSortBy()->getDir();
        $field = $sortedPageLink->getSortBy()->getField();

        $contentQuery->orderBy([
            $field => $dir->reverse()->forQuery(),
            'id' => $dir->reverse()->forQuery(),
        ]);
        $content = array_reverse($contentQuery->all());

        return new ContentPage($content, $this->perPage, $query->count());

    }
}
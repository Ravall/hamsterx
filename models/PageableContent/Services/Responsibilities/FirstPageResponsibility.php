<?php

namespace app\models\PageableContent\Services\Responsibilities;

use app\models\PageableContent\Entity\ContentPage;
use app\models\PageableContent\Entity\SortedPage;
use Yii;
use yii\db\ActiveQuery;
use yii\web\Application;

/**
 * Экшн запускаемый, если требуется загрузить первую страницу
 *
 * Class FirstPageResponsibility
 * @package app\models\PageableContent\Services\Responsibilities
 */
final class FirstPageResponsibility implements Responsibility
{
    /**
     * @var array
     */
    private $perPage;

    public function __construct()
    {
        $this->perPage = Yii::$app->params['perPage'];
    }

    public function isActive(ActiveQuery $query, SortedPage $sortedPageLink): bool
    {
        return $sortedPageLink->getNumber() === 1;
    }

    public function process(ActiveQuery $query, SortedPage $sortedPageLink): ContentPage
    {
        $contentQuery = (clone $query);
        $contentQuery->limit($this->perPage);

        $dir = $sortedPageLink->getSortBy()->getDir();
        $field = $sortedPageLink->getSortBy()->getField();

        $contentQuery->orderBy([
            $field => $dir->forQuery(),
            'id' => $dir->forQuery(),
        ]);

        return new ContentPage($contentQuery->all(), $this->perPage, $query->count());

    }
}
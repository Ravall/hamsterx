<?php

namespace app\models\PageableContent\Services\Responsibilities;

use app\models\PageableContent\Entity\ContentPage;
use app\models\PageableContent\Entity\SortedPage;
use app\models\PageableContent\Enums\DirEnum;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * Экшн запускаемый, когда требуется загрузить предыдущю страницу
 *
 * Class StepNextResponsibility
 * @package app\models\PageableContent\Services\Responsibilities
 */
final class StepPrevResponsibility implements Responsibility
{
    private $perPage;

    public function __construct()
    {
        $this->perPage = Yii::$app->params['perPage'];
    }

    public function isActive(ActiveQuery $query, SortedPage $sortedPageLink): bool
    {
        if (!$sortedPageLink->getPortionPosition() || $sortedPageLink->getPortionPosition()->empty()) {
            return false;
        }

        /** если предыдщуая больше следующей  */
        return ($sortedPageLink->getNumber() <= $sortedPageLink->getPortionPosition()->getPageNumber());
    }

    public function process(ActiveQuery $query, SortedPage $sortedPageLink): ContentPage
    {
        $contentQuery = (clone $query);
        $contentQuery->limit($this->perPage);

        $offset = $this->perPage * ($sortedPageLink->getNumber() - $sortedPageLink->getPortionPosition()->getPageNumber());

        $dir = $sortedPageLink->getSortBy()->getDir();
        $field = $sortedPageLink->getSortBy()->getField();


        $contentQuery->orderBy([
            $field => $dir->reverse()->forQuery(),
            'id' => $dir->reverse()->forQuery(),
        ]);

        $condition = [
            DirEnum::DESC()->equals($dir) ? '>' : '<',
            '(' . implode(',', [$field, 'id']) . ')',
            new Expression(
                '(' . implode(',', [
                    "'" . $sortedPageLink->getPortionPosition()->getValue() . "'",
                    $sortedPageLink->getPortionPosition()->getId()
                ]) . ')'),
        ];

        $contentQuery
            ->offset($offset)
            ->where($condition);

        $content = array_reverse($contentQuery->all());

        return new ContentPage($content, $this->perPage, $query->count());
    }
}
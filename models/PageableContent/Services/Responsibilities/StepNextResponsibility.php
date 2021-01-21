<?php

namespace app\models\PageableContent\Services\Responsibilities;

use app\models\PageableContent\Entity\ContentPage;
use app\models\PageableContent\Entity\SortedPage;
use app\models\PageableContent\Enums\DirEnum;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * Экшн запускаемый, когда требуется загрузить следующую страницу
 *
 * Class StepNextResponsibility
 * @package app\models\PageableContent\Services\Responsibilities
 */
final class StepNextResponsibility implements Responsibility
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

        /** если предыдщуая меньше следующей  */
        return ($sortedPageLink->getNumber() > $sortedPageLink->getPortionPosition()->getPageNumber());
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

        $condition = [
            DirEnum::DESC()->equals($dir) ? '<=' : '>=',
            '(' . implode(',', [$field, 'id']) . ')',
            new Expression(
                '(' . implode(',', [
                    "'" . $sortedPageLink->getPortionPosition()->getValue() . "'",
                    $sortedPageLink->getPortionPosition()->getId()
                ]) . ')'),
        ];

        $offset = $this->perPage * (
                $sortedPageLink->getNumber() - $sortedPageLink->getPortionPosition()->getPageNumber()
        );

        $contentQuery
            ->offset($offset)
            ->where($condition);

        return new ContentPage($contentQuery->all(), $this->perPage, $query->count());

    }
}
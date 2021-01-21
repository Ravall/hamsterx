<?php

namespace app\models\PageableContent\Services\Responsibilities;


use app\models\PageableContent\Entity\ContentPage;
use app\models\PageableContent\Entity\SortedPage;
use Yii;
use yii\db\ActiveQuery;

/**
 * Экшн запускаемый, если требуется прыгнуть на страницу в первую половину
 *
 * Class JumpBeforeHalfResponsibility
 * @package app\models\PageableContent\Services\Responsibilities
 */
final class JumpAboveHalfResponsibility implements Responsibility
{
    /**
     * @var LastPageResponsibility
     */
    private $lastPageResponsibility;
    /**
     * @var StepPrevResponsibility
     */
    private $stepPrevResponsibility;
    /**
     * @var int
     */
    private $perPage;

    public function __construct(
        LastPageResponsibility $lastPageResponsibility,
        StepPrevResponsibility $stepPrevResponsibility
    )
    {
        $this->lastPageResponsibility = $lastPageResponsibility;
        $this->stepPrevResponsibility = $stepPrevResponsibility;
        $this->perPage = Yii::$app->params['perPage'];
    }

    private function getMaxPage(ActiveQuery $query): int
    {
        return (int) ceil($query->count() / $this->perPage);
    }

    public function isActive(ActiveQuery $query, SortedPage $sortedPageLink): bool
    {
        if ($sortedPageLink->getPortionPosition() && !$sortedPageLink->getPortionPosition()->empty()) {
            return false;
        }

        return ($sortedPageLink->getNumber() > $this->getMaxPage($query) / 2);
    }

    public function process(ActiveQuery $query, SortedPage $sortedPageLink): ContentPage
    {
        /**
         * считаем что прыгаем с последнгей
         */
        $last = new SortedPage($this->getMaxPage($query), $sortedPageLink->getSortBy());
        $result = $this->lastPageResponsibility->process($query, $last);
        $jumpPage = $result->getPageNumber($sortedPageLink->getNumber(), $last);
        return $this->stepPrevResponsibility->process($query, $jumpPage);
    }
}

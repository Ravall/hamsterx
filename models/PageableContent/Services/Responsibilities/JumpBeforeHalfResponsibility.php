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
final class JumpBeforeHalfResponsibility implements Responsibility
{
    private $perPage;
    /**
     * @var FirstPageResponsibility
     */
    private $firstPageResponsibility;
    /**
     * @var StepNextResponsibility
     */
    private $stepNextResponsibility;


    public function __construct(
        FirstPageResponsibility $firstPageResponsibility,
        StepNextResponsibility $stepNextResponsibility
    ) {
        $this->perPage = Yii::$app->params['perPage'];
        $this->firstPageResponsibility = $firstPageResponsibility;
        $this->stepNextResponsibility = $stepNextResponsibility;
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

        return ($sortedPageLink->getNumber() <= $this->getMaxPage($query) / 2);

    }

    public function process(ActiveQuery $query, SortedPage $sortedPageLink): ContentPage
    {
        $first = new SortedPage(1, $sortedPageLink->getSortBy());
        $result = $this->firstPageResponsibility->process($query, $first);
        $jumpPage = $result->getPageNumber($sortedPageLink->getNumber(), $first);
        return $this->stepNextResponsibility->process($query, $jumpPage);
    }
}
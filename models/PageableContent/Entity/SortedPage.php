<?php
declare(strict_types=1);

namespace app\models\PageableContent\Entity;

final class SortedPage
{
    /**
     * @var int
     */
    private $page;
    /**
     * @var SortBy
     */
    private $sortBy;
    /**
     * @var PortionPosition|null
     */
    private $portionPosition;

    public function __construct(int $page,  SortBy $sortBy, PortionPosition $portionPosition = null)
    {
        $this->page = $page;
        $this->sortBy = $sortBy;
        $this->portionPosition = $portionPosition;
    }

    public function getPortionPosition(): ?PortionPosition
    {
        return $this->portionPosition;
    }

    public function getSortBy(): SortBy
    {
        return $this->sortBy;
    }

    public function getNumber(): int
    {
        return $this->page;
    }

    public function getUrl()
    {
        $params['page'] = $this->page;
        $params['sort'] = $this->sortBy->getField();
        $params['dir'] = $this->sortBy->getDir()->getValue();

        if (!is_null($this->portionPosition) && !$this->portionPosition->empty()) {
            $params['position_id'] = $this->portionPosition->getId();
            $params['position_value'] = $this->portionPosition->getValue();
            $params['position_page'] = $this->portionPosition->getPageNumber();
        }

        return '/?'.http_build_query($params);
    }

}
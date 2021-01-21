<?php
declare(strict_types=1);

namespace app\models\PageableContent\Entity;

final class PortionPosition
{
    /**
     * @var mixed
     */
    private $value;
    /**
     * @var int
     */
    private $id;
    /**
     * @var int
     */
    private $pageNumber;

    /**
     * PortionPosition constructor.
     * @param mixed $value
     * @param int|null $id
     * @param int|null $pageNumber
     */
    public function __construct($value, ?int $id, ?int $pageNumber)
    {
        $this->value = $value;
        $this->id = $id;
        $this->pageNumber = $pageNumber;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getPageNumber()
    {
        return $this->pageNumber;
    }

    public function empty(): bool
    {
        return is_null($this->value) || is_null($this->id) || is_null($this->pageNumber);
    }
}
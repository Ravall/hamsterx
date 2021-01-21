<?php
declare(strict_types=1);

namespace app\models\PageableContent\Entity;

use app\models\PageableContent\Enums\DirEnum;

final class SortBy
{
    /**
     * @var string
     */
    private $sortBy;
    /**
     * @var DirEnum
     */
    private $dir;

    public function __construct(string $sortBy, DirEnum $dir)
    {
        $this->sortBy = $sortBy;
        $this->dir = $dir;
    }

    public function getField(): string
    {
        return $this->sortBy;
    }
    public function getDir(): DirEnum
    {
        return $this->dir;
    }

    public function getQueryDir(): int
    {
        return [
            DirEnum::ASC()->getValue() => SORT_ASC,
            DirEnum::DESC()->getValue() => SORT_DESC
        ][$this->dir->getValue()];
    }

    public function isDesc(string $sortBy): bool
    {
        return ($sortBy === $this->sortBy && $this->dir->equals(DirEnum::DESC()));
    }

    public function isAsc(string $sortBy): bool
    {
        return ($sortBy === $this->sortBy && $this->dir->equals(DirEnum::ASC()));
    }
}
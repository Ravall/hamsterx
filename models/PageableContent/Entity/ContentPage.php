<?php
declare(strict_types=1);

namespace app\models\PageableContent\Entity;


use app\models\PageableContent\Enums\DirEnum;

final class ContentPage
{
    /**
     * @var array
     */
    private $content;
    /**
     * @var int
     */
    private $perPage;
    /**
     * @var int
     */
    private $totalRows;

    public function __construct(array $content, int $perPage, int $totalRows)
    {
        $this->content = $content;
        $this->perPage = $perPage;
        $this->totalRows = $totalRows;
    }

    public function getContent(): array
    {
        return $this->content;
    }

    public function getFirstPageSortedBy(string $field, DirEnum $dir)
    {
        return new SortedPage(1, new SortBy($field, $dir));
    }

    public function getLastPage(SortedPage $page): SortedPage
    {
        return new SortedPage($this->getTotalPages(),  $page->getSortBy());
    }

    public function getFirstPage(SortedPage $page): SortedPage
    {
        return new SortedPage(1, $page->getSortBy());
    }

    public function getPreviousPage(SortedPage $page): ?SortedPage
    {
        return ($page->getNumber() > 1)
            ? new SortedPage(
                $page->getNumber() - 1,
                $page->getSortBy(),
                new PortionPosition(
                    $this->content[0][$page->getSortBy()->getField()],
                    $this->content[0]['id'],
                    $page->getNumber()
                )
            )
            : null;
    }

    /**
     * @param SortedPage $page
     * @return SortedPage|null
     */
    public function getNextPage(SortedPage $page): ?SortedPage
    {
        return ($page->getNumber() <= $this->getTotalPages())
            ? new SortedPage(
                $page->getNumber() + 1,
                $page->getSortBy(),
                new PortionPosition(
                    $this->content[0][$page->getSortBy()->getField()],
                    $this->content[0]['id'],
                    $page->getNumber()
                )
            )
            : null;
    }

    /**
     * @param SortedPage $page
     * @param int $deep
     * @return SortedPage[]
     */
    public function getPreviousPages(SortedPage $page, int $deep): array
    {
        $pages = [];
        for (
            $i = $page->getNumber() - 1;
        ($i > 0 && $i > $page->getNumber() - $deep);
            $i--
        ) {
            $pages[] = new SortedPage(
                $i,
                $page->getSortBy(),
                new PortionPosition(
                    $this->content[0][$page->getSortBy()->getField()],
                    $this->content[0]['id'],
                    $page->getNumber()
                )
            );
        }
        return array_reverse($pages);
    }

    /**
     * @param SortedPage $page
     * @param $deep
     * @return SortedPage[]
     */
    public function getNextPages(SortedPage $page, $deep): array
    {
        $pages = [];
        for (
            $i = $page->getNumber() + 1;
            ($i <= $this->getTotalPages() && $i < $page->getNumber() + $deep);
            $i++
        ) {
            $pages[] = new SortedPage(
                $i,
                $page->getSortBy(),
                new PortionPosition(
                    $this->content[0][$page->getSortBy()->getField()],
                    $this->content[0]['id'],
                    $page->getNumber()
                )
            );
        }
        return $pages;
    }

    public function isLast(SortedPage $page): bool
    {
        return $page->getNumber() === $this->getTotalPages();
    }

    public function isFirst(SortedPage $page): bool
    {
        return 1 === $page->getNumber();
    }

    public function pageMoreThen(SortedPage $page, int $count): bool
    {
        return $page->getNumber() > $count;
    }

    public function pageBeforeLast(SortedPage $page, int $count): bool
    {
        return $this->getTotalPages() < $page->getNumber() + $count;
    }

    private function getTotalPages(): int
    {
        return (int) ceil($this->totalRows / $this->perPage);
    }
}
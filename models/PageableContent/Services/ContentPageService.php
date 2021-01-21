<?php

namespace app\models\PageableContent\Services;

use app\models\PageableContent\Entity\ContentPage;
use app\models\PageableContent\Entity\SortedPage;
use app\models\PageableContent\Services\Responsibilities\FirstPageResponsibility;
use app\models\PageableContent\Services\Responsibilities\JumpAboveHalfResponsibility;
use app\models\PageableContent\Services\Responsibilities\JumpBeforeHalfResponsibility;
use app\models\PageableContent\Services\Responsibilities\LastPageResponsibility;
use app\models\PageableContent\Services\Responsibilities\Responsibility;
use app\models\PageableContent\Services\Responsibilities\StepNextResponsibility;
use app\models\PageableContent\Services\Responsibilities\StepPrevResponsibility;
use LogicException;
use yii\db\ActiveQuery;

final class ContentPageService
{
    /**
     * @var array
     */
    private $responsibilities;

    public function __construct(
        FirstPageResponsibility $firstPageResponsibility,
        LastPageResponsibility $lastPageResponsibility,
        StepNextResponsibility $stepNextResponsibility,
        StepPrevResponsibility $stepPrevResponsibility,
        JumpBeforeHalfResponsibility $jumpBeforeHalfResponsibility,
        JumpAboveHalfResponsibility $jumpAboveHalfResponsibility
    ) {
        $this->responsibilities = [
            $firstPageResponsibility,
            $lastPageResponsibility,
            $stepNextResponsibility,
            $stepPrevResponsibility,
            $jumpBeforeHalfResponsibility,
            $jumpAboveHalfResponsibility
        ];
    }

    /**
     * в чем идея
     * Don’t touch what you don’t need
     * мы на ххх странице
     * и прыгаем на ххх+3 страницу, то нам достаточно пропустить занчения
     *
     * SELECT * FROM "video_content" WHERE (added,id) <= (date1, id1) ORDER BY "added" DESC, "id" DESC OFFSET 20*3 LIMIT 20
     * где date1 и id1 - значения первой строчки на странице ххх
     *
     * т.е все будет работать быстро. За исключением прыжков с 1-й страницы на 10000-ую тогда будет большой OFFSET
     * но интерфейс такие прыжки не позволяет. Если вбил руками page в url страницы = то увы, сам себе злобный буратино.
     *
     * на случай прыжков с 1-й на 49000 страницу  оптимизация:
     *  считать что проще офсетнутся на 1000 с полседней, чем на 49000 с 1-й
     *  (как минимум это может сократить скорость работы от 2 раз и выше)
     *
     *
     * @param ActiveQuery $query
     * @param SortedPage $sortedPageLink
     * @return ContentPage
     */
    public function getContentPage(ActiveQuery $query, SortedPage $sortedPageLink): ContentPage
    {
        /** @var Responsibility $responsibility */
        foreach ($this->responsibilities as $responsibility) {
            if ($responsibility->isActive($query, $sortedPageLink)) {
                return $responsibility->process($query, $sortedPageLink);
            }
        }

        throw new LogicException('Не найдена Responsibility');
    }
}


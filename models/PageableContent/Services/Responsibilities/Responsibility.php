<?php

namespace app\models\PageableContent\Services\Responsibilities;


use app\models\PageableContent\Entity\ContentPage;
use app\models\PageableContent\Entity\SortedPage;
use yii\db\ActiveQuery;

interface Responsibility
{
    public function isActive(ActiveQuery $query, SortedPage $sortedPageLink): bool;
    public function process(ActiveQuery $query, SortedPage $sortedPageLink): ContentPage;
}

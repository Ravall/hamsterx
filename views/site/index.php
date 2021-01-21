<?php

/* @var $this yii\web\View */

use app\models\PageableContent\Entity\ContentPage;
use app\models\PageableContent\Entity\SortedPage;
use app\models\PageableContent\Enums\DirEnum;
use app\models\VideoContent;

$this->title = 'Video Content';
/**
 * @var SortedPage $sortedPage
 * @var ContentPage $pageContent
 */
?>

<div class="container">

    <div class="jumbotron">
        <h1>Video Content</h1>
    </div>

    <div class="body-content">
        <table class="table">
            <tr>
                <th>id</th>
                <th>title</th>
                <th>thumbnail_url</th>
                <th>duration</th>
                <th>
                    <a <?php if ($sortedPage->getSortBy()->isDesc('views')):?>style="color: red"<?php endif; ?>
                            href="<?=($pageContent->getFirstPageSortedBy('views', DirEnum::DESC()))->getUrl()?>">
                        &darr;
                    </a>
                    views
                    <a <?php if ($sortedPage->getSortBy()->isAsc('views')):?>style="color: red"<?php endif; ?>
                            href="<?=($pageContent->getFirstPageSortedBy('views', DirEnum::ASC()))->getUrl()?>">
                        &uarr;
                    </a>
                </th>
                <th><a <?php if ($sortedPage->getSortBy()->isDesc('added')):?>style="color: red"<?php endif; ?>
                        href="<?=($pageContent->getFirstPageSortedBy('added', DirEnum::DESC()))->getUrl()?>">
                        &darr;
                    </a>
                    added
                    <a <?php if ($sortedPage->getSortBy()->isAsc('added')):?>style="color: red"<?php endif; ?>
                        href="<?=($pageContent->getFirstPageSortedBy('added', DirEnum::ASC()))->getUrl()?>">
                        &uarr;
                    </a></th>
            </tr>

        <?php
        /** @var VideoContent $row */
        foreach ($pageContent->getContent() as $row): ?>
            <tr>
                <td><?=$row->id?></td>
                <td><?=$row->title?></td>
                <td><?=$row->thumbnailUrl?></td>
                <td><?=$row->durationInMinutes?></td>
                <td><?=$row->views?></td>
                <td><?=$row->added?></td>
            </tr>
        <?php endforeach; ?>
        </table>
    </div>

    <nav aria-label="...">
        <ul class="pagination">
            <li class="page-item">
                <a class="page-link" href="<?=$pageContent->getFirstPage($sortedPage)->getUrl()?>">First</a>
            </li>

            <li class="page-item <?= ($pageContent->isFirst($sortedPage)) ? 'disabled': ''?>">
                <?php if(!$pageContent->isFirst($sortedPage)): ?>
                    <a class="page-link" href="<?=$pageContent->getPreviousPage($sortedPage)->getUrl()?>" tabindex="-1">Previous</a>
                <?php else: ?>
                    <a class="page-link" tabindex="-1">Previous</a>
                <?php endif; ?>

            </li>
            <?php foreach ($pageContent->getPreviousPages($sortedPage, 2) as $page):?>
                <li class="page-item"><a class="page-link" href="<?=$page->getUrl();?>"><?=$page->getNumber();?></a></li>
            <?php endforeach;?>
            <li class="page-item active">
                <a class="page-link" href="#"><?=$sortedPage->getNumber();?> <span class="sr-only">(current)</span></a>
            </li>
            <?php foreach ($pageContent->getNextPages($sortedPage, 2) as $page):?>
                <li class="page-item"><a class="page-link" href="<?=$page->getUrl();?>"><?=$page->getNumber();?></a></li>
            <?php endforeach;?>


            <li class="page-item <?=($pageContent->isLast($sortedPage)) ? 'disabled': ''?>">
                <?php if (!$pageContent->isLast($sortedPage)): ?>
                    <a class="page-link" href="<?=$pageContent->getNextPage($sortedPage)->getUrl()?>">Next</a>
                <?php else: ?>
                    <a class="page-link" href="">Next</a>
                <?php endif ?>
            </li>

            <li class="page-item">
                <a class="page-link" href="<?=$pageContent->getLastPage($sortedPage)->getUrl()?>">Last</a>
            </li>
        </ul>
    </nav>

</div>

<?php

declare(strict_types=1);

namespace app\models\Requests;

use app\models\PageableContent\Entity\PortionPosition;
use app\models\PageableContent\Entity\SortBy;
use app\models\PageableContent\Entity\SortedPage;
use app\models\PageableContent\Enums\DirEnum;
use app\models\VideoContent;
use Yii;
use yii\base\Model;
use yii\db\ActiveQuery;

final class TableSortRequest extends Model
{
    public $dir;
    public $sort;
    public $page;
    /**
     * @var mixed
     */
    public $position_value;
    public $position_id;
    /**
     * @var mixed
     */
    public $position_page;

    /**
     * @var int
     */
    private $maxPage;
    /**
     * @var mixed
     */
    private $perPage;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->perPage = Yii::$app->params['perPage'];
        $this->maxPage = $this->getMaxPage(VideoContent::find());
    }

    private function getMaxPage(ActiveQuery $query): int
    {
        return (int) ceil($query->count() / $this->perPage);
    }

    public function rules()
    {
        return [
            ['position_id', 'integer'],
            ['position_value', 'string'],
            [
                'dir',
                'in',
                'range' => DirEnum::values(),
                'message' => 'Невалидое направление сортировки. Допустимые значения: '
                    . implode(',', DirEnum::values())
            ],
            [
                'sort',
                'in',
                'range' => ['added', 'views'],
                'message' => 'Сортировка возможна только по двум полям: added, views'
            ],
            ['page', 'integer', 'min' => 1, 'max' => $this->maxPage],
            ['position_page', 'integer', 'min' => 1, 'max' => $this->maxPage],
            ['page', 'default', 'value' => 1],
            ['page', 'filter', 'filter' => function() {
                return (int) $this->page;
            }],
            ['sort', 'default', 'value' => 'added'],
            ['dir', 'default', 'value' => DirEnum::DESC()],

            [
                'position_page',
                'filter',
                'filter' => function() {
                    return (int) $this->position_page;
                },
                'skipOnEmpty' => true
            ],
            [
                'position_id',
                'filter',
                'filter' => function() {
                    return (int) $this->position_id;
                },
                'skipOnEmpty' => true
            ],
        ];
    }


    public function getSortedPage(): SortedPage
    {
        return new SortedPage(
            $this->page,
            new SortBy($this->sort, new DirEnum($this->dir)),
            new PortionPosition(
                $this->position_value,
                $this->position_id,
                $this->position_page,
            )
        );
    }
}

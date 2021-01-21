<?php

namespace app\models\PageableContent\Enums;

use MyCLabs\Enum\Enum;

/**
 * Class DirEnum
 * @package app\models\PageableContent\Enums
 *
 * @method static static ASC()
 * @method static static DESC()
 */
final class DirEnum extends Enum
{
    private const ASC = 'asc';
    private const DESC = 'desc';

    public function forQuery(): int
    {
        return [
            DirEnum::ASC()->getValue() => SORT_ASC,
            DirEnum::DESC()->getValue() => SORT_DESC
        ][$this->getValue()];
    }

    public function reverse(): self
    {
        return (DirEnum::ASC())->equals($this)
            ? DirEnum::DESC()
            : DirEnum::ASC();
    }
}